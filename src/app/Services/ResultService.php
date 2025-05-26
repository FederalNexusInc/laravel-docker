<?php

namespace App\Services;

use Exception;
use App\Models\Anchor;
use App\Models\Project;
use App\Models\SoilLayer;
use App\Models\SoilProfile;
use App\Models\Helix;
use App\Services\ResultData;
use App\Enums\CalculationType;
use App\Models\ProjectSpecialist;
use App\Utilities\SoilLayerUtility;
use Illuminate\Database\Eloquent\Collection;

class ResultService
{
    private $helixCapacityCalculator;
    private $shaftResistanceCalculator;
    private $torsionalResistanceCalculator;
    private float $shaft_rating_compression = 78000;
    private float $shaft_rating_tension = 78000;

    public function __construct(
        HelixCapacityCalculator $helixCapacityCalculator,
        ShaftResistanceCalculator $shaftResistanceCalculator,
        TorsionalResistanceCalculator $torsionalResistanceCalculator,
    ) {
        $this->helixCapacityCalculator = $helixCapacityCalculator;
        $this->shaftResistanceCalculator = $shaftResistanceCalculator;
        $this->torsionalResistanceCalculator = $torsionalResistanceCalculator;
    }

    public function calculateProjectResults(int $projectId, int $interval = 1): ResultData
    {
        try {
            $project = Project::with(['anchors.helixes', 'anchors.soilProfile.soilLayers', 'soilProfile.soilLayers'])->findOrFail($projectId);
            $results = new ResultData();

            $anchor = $project->anchors()->first();

            $soilProfile = $project->soilProfile;
            if (!$soilProfile) {
                throw new \Exception("No soil profile found for anchor or project");
            }

            $helixes = $project->helixes()->get();

            $results->ProjectId = $project->project_id;
            $results->AnchorId = $anchor->anchor_id;
            $results->CalculationType = match ($anchor->anchor_type) {
                1 => CalculationType::COMPRESSION,
                2 => CalculationType::TENSION,
                default => throw new \Exception("Invalid anchor_type: {$anchor->anchor_type}"),
            };
            $this->setBasicCalculations($anchor, $soilProfile);

            $helix_configuration = '';
            foreach ($helixes as $helix) {
                $helix_configuration .= $helix->size . '-';
            }

            $helix_configuration = rtrim($helix_configuration, '-');
            $results->HelixConfiguration = $helix_configuration;
            
            $helix_configuration = '';
            $results->RequiredAllowableCapacity = $anchor->required_allowable_capacity;
            $results->EmpericalTorqueFactor = $anchor->empirical_torque_factor;
            $results->RequiredSafetyFactor = $anchor->required_safety_factor;
            $results->RequiredAllowablePileCapacity = $anchor->required_allowable_capacity * 1000;
            $results->RequiredUltimateCapacity = $results->RequiredAllowablePileCapacity * $results->RequiredSafetyFactor;
            $results->HelicalPileDiameter = $this->convertMixedNumberToInches($anchor->lead_shaft_od);

            $this->max_tn_tq = 0;

            
            $soilLayers = $soilProfile->relationLoaded('soilLayers') 
                ? $soilProfile->soilLayers 
                : $soilProfile->soilLayers()->get();
            // Calculate Emin based on calculation type
            
            $emin = $this->calculateEmin($anchor, $results->CalculationType, $soilProfile);

            // Iterate through depths from emin to 150 feet with the specified interval
            $maxCalculationDepth = $soilProfile->maximum_depth;

            for ($depth = $emin; $depth <= 150; $depth += $interval) {
                $soilLayer = SoilLayerUtility::findSoilLayerAtDepth($soilLayers, $depth);
                $dTorsonalResistance = 0;

                // Calculate helix capacities for each helix at current depth
                $helixCapacities = $this->calculateHelixCapacitiesAtDepth(
                    $anchor, 
                    $helixes, 
                    $soilProfile,
                    $soilLayers, 
                    $depth, 
                    $results->CalculationType,
                );

                $dTorsonalResistance = $helixCapacities->sum('TorsionalResistance');

                if($helixCapacities->isEmpty()){
                    break;
                }

                // Calculate shaft resistance for the current depth
                $shaftResistance = $this->calculateShaftResistance(
                    $anchor, 
                    $soilProfile,
                    $soilLayers, 
                    $helixCapacities, 
                    $depth, 
                    $results->CalculationType
                );

                // Calculate torsional resistance for the current depth
                $torsionalResistance = $this->calculateTorsionalResistance(
                    $anchor, 
                    $soilProfile,
                    $soilLayers, 
                    $helixCapacities, 
                    $depth,
                    $dTorsonalResistance,
                    $results->CalculationType
                );

                $this->max_tn_tq = max($dTorsonalResistance, $this->max_tn_tq);

                // Calculate total anchor capacity (sum of helix capacities + shaft resistance)
                $sumQT = $helixCapacities->sum('HelixCapacityQT');
                $anchorCapacity = $sumQT + $shaftResistance;

                // Check against mechanical strength ratings
                $ratingValue = ($results->CalculationType === CalculationType::COMPRESSION) 
                    ? $this->shaft_rating_compression 
                    : $this->shaft_rating_compression;

                if (!$anchor->omit_shaft_mechanical_strength_check && $ratingValue < $anchorCapacity) {
                    $anchorCapacity = $ratingValue;
                }

                $this->max_tn_tq = max($anchorCapacity, $this->max_tn_tq);

                $anchorCapacity = round($anchorCapacity, 0);

                // Store the results for the current depth
                $results->addDepthResult($depth, [
                    'Embedment' => (int)$depth,
                    'sumQT' => (int)$sumQT,
                    'anchor_capacity' => $anchorCapacity,
                    'shaft_resistance' => $shaftResistance,
                    'torsional_resistance' => $torsionalResistance,
                ]);
            }
            
            $apc = new AllowablePileCapacity(
                $anchor->required_allowable_capacity, 
                $anchor->required_safety_factor, 
                $anchor->empirical_torque_factor,
                $results->DepthResults
            );
            
            $results->AllowableEndBearing = round($apc->getAllowableEndBearing(), 2) / 1000.00;
            $results->AllowableFrictionalResistance = round($apc->getAllowableFrictionalResistance(), 2) / 1000.00;
            $results->AllowablePileCapacity = round($apc->getOutput(), 2) / 1000.00;
            $results->ApproximatePileEmbedmentDepth = $apc->getDepth();
            $results->RequiredInstallationTorque = $apc->getTorque();

            $this->reportData($project, $anchor, $soilLayers, $helixes, $results);
        } catch (\Exception $e) {
            // Log the error and rethrow or handle appropriately
            throw new \Exception("Failed to calculate project results: " . $e->getMessage());
        }
        return $results;
    }

    /**
     * Calculate Emin based on calculation type (Tension or Compression)
     */
    private function calculateEmin(Anchor $anchor, CalculationType $calculationType, SoilProfile $soilProfile): float
    {
        return $calculationType === CalculationType::TENSION 
            ? $this->calculateTensionEmin($anchor, $soilProfile) 
            : $this->calculateCompressionEmin($anchor);
    }

    /**
     * Calculate Emin for Tension anchors
     */
    private function calculateTensionEmin(Anchor $anchor, SoilProfile $soilProfile): float
    {
        $e1 = 0;
        $e2 = 0;
        $dTita = (3.141592 / 180) * $anchor->anchor_declination_degree;

        if (!$anchor->hasBackSlope()) {
            $e1 = (4 / sin($dTita));
        } else {
            $dvar = 4 * cos($dTita);
            if ($anchor->x1 < $dvar && $dvar < $anchor->x2) {
                $e1 = $this->getE1Value($dTita, $anchor->x1, $anchor->y1, $anchor->x2, $anchor->y2);
            } elseif ($anchor->x2 < $dvar && $dvar < $anchor->x3) {
                $e1 = $this->getE1Value($dTita, $anchor->x2, $anchor->y2, $anchor->x3, $anchor->y3);
            } elseif ($anchor->x3 < $dvar && $dvar < $anchor->x4) {
                $e1 = $this->getE1Value($dTita, $anchor->x3, $anchor->y3, $anchor->x4, $anchor->y4);
            } elseif ($anchor->x4 < $dvar && $dvar < $anchor->x5) {
                $e1 = $this->getE1Value($dTita, $anchor->x4, $anchor->y4, $anchor->x5, $anchor->y5);
            }
        }

        $helixSize = $anchor->getTopHelixSize() / 12;
        $e2 = $soilProfile->soil_type === 'Cohesive' ? 5 * $helixSize : 7 * $helixSize;
        $maxEmin = max($e1, $e2) + $anchor->getHelixSpacing();
        return ($maxEmin < 0 ? ceil($maxEmin) : floor($maxEmin)) + 1;
    }

    private function getE1Value(float $dTita, float $x1, float $y1, float $x2, float $y2): float
    {
        $tieValue = ($y2 - $y1) / ($x2 - $x1);
        $e1 = (4 - $y1) / ($tieValue + tan($dTita));
        return $e1 / cos($dTita);
    }

    /**
     * Calculate Emin for Compression anchors
     */
    private function calculateCompressionEmin(Anchor $anchor): float
    {
        $helixSize = $anchor->getTopHelixSize() / 12; // Convert to feet
        $e1 = 2 * $helixSize;

        if ($anchor->helixes->count() > 0) {
            $e1 += $anchor->getHelixSpacing();
        }

        return ($e1 < 0 ? ceil($e1) : floor($e1)) + 1;
    }

    /**
     * Calculate helix capacities at a specific depth
     */
    private function calculateHelixCapacitiesAtDepth(
        Anchor $anchor,
        Collection $helixes,
        SoilProfile $soilProfile,
        Collection $soilLayers,
        float $depth,
        CalculationType $calculationType,
    ): \Illuminate\Database\Eloquent\Collection {
        $helixCapacities = collect();
        $tita = $anchor->tita_value;

        if ($helixes->isEmpty()) {
            throw new \Exception("No helixes found for anchor ID: {$anchor->anchor_id}");
        }

        foreach ($helixes as $i => $helix) {
            if (!isset($helix->size) || !isset($anchor->lead_shaft_od_in)) {
                throw new \Exception("Missing required helix properties for calculation");
            }

            $con = pi() * (pow($helix->size, 2) - pow($anchor->lead_shaft_od_in, 2)) / (4 * 144);

            $depths = $horizontals = $qValues = [];
            $validPositions = 0;

            foreach (['1', '2', '3', '4'] as $pos) {
                try {
                    $depths[$pos] = $this->findHelixDepth($anchor, 'HELIX_CAPACITY_HELIX_DEPTH', $depth, $tita, $pos, $i, $calculationType);
                    $horizontals[$pos] = $this->findHelixDepth($anchor, 'HELIX_CAPACITY_HORIZONTAL_DISTANCE', $depth, $tita, $pos, $i, $calculationType);
                    $qValues[$pos] = $this->helixCapacityCalculator->calculateHelixCapacities(
                        $anchor,
                        $soilProfile,
                        $soilLayers,
                        $depths[$pos],
                        $horizontals[$pos],
                        $con
                    );

                    // Track valid positions
                    if ($qValues[$pos]['count'] > 0) {
                        $validPositions++;
                    }
                } catch (\Exception $e) {
                    throw new \Exception("Error calculating position $pos for helix $i: " . $e->getMessage());
                }
            }

            if (round($depths['1'], 2) > $soilProfile->maximum_depth)
            {
                break;
            }
            // Calculate total QT
            if ($validPositions > 0) {
                $totalNumerator = $qValues['1']['QT'] + $qValues['2']['QT'] + $qValues['3']['QT'] + $qValues['4']['QT'];
                $totalDenominator = $qValues['1']['count'] + $qValues['2']['count'] + $qValues['3']['count'] + $qValues['4']['count'];

                if ($totalDenominator <= 0) {
                    throw new \Exception("Invalid helix capacity calculation: sum of counts must be greater than zero");
                }
                $totalQt = round($totalNumerator / $totalDenominator, 2);
            } else {
                $totalQt = 0;
            }

            // Check against helix rating
            $helixCapacityQT = round($totalQt, 0);
            $resultStatus = null;
            if (!$anchor->omit_helix_mechanical_strength_check && $totalQt > $helix->rating) {
                $helixCapacityQT = $helix->rating;
                $resultStatus = 'A'; // Alert
            }

            $helixCapacities->push([
                'HelixNbr' => $i,
                'HelixDepth1' => $depths['1'],
                'HelixDepth2' => $depths['2'],
                'HelixDepth3' => $depths['3'],
                'HelixDepth4' => $depths['4'],
                'HorizontalDistance1' => $horizontals['1'],
                'HorizontalDistance2' => $horizontals['2'],
                'HorizontalDistance3' => $horizontals['3'],
                'HorizontalDistance4' => $horizontals['4'],
                'HelixCapacityQT1' => $qValues['1']['QT'],
                'HelixCapacityQT2' => $qValues['2']['QT'],
                'HelixCapacityQT3' => $qValues['3']['QT'],
                'HelixCapacityQT4' => $qValues['4']['QT'],
                'HelixCapacityQT' => $helixCapacityQT,
                'HelixRating' => $helix->rating,
                'ResultStatus' => $resultStatus,
                'TorsionalResistance' => $qValues['1']['QT'] / $anchor->empirical_torque_factor
            ]);
        }

        return new \Illuminate\Database\Eloquent\Collection($helixCapacities->all());
    }

    /**
     * Find helix depth or horizontal distance based on parameters
     */
    private function findHelixDepth(Anchor $anchor, string $depthType, float $embeddedDepth, float $tita, string $pos, int $helixNumber, CalculationType $calculationType): float
    {
        if (!isset($anchor->helixes[$helixNumber])) {
            throw new \Exception("Helix number $helixNumber not found");
        }

        $position = (int)$pos;
        $helixShaftLength = $this->getHelixShaftLength($anchor, $helixNumber);
        $part2 = ($position - 1) * $anchor->helixes[$helixNumber]->size / 12;
        $part3 = ($depthType === 'HELIX_CAPACITY_HELIX_DEPTH') ? sin($tita) : cos($tita);

        return ($calculationType === CalculationType::COMPRESSION) 
            ? ($embeddedDepth - $helixShaftLength + $part2) * $part3 
            : ($embeddedDepth - $helixShaftLength - $part2) * $part3;
    }

    /**
     * Gets the helix shaft length for a specific helix number
     */
    private function getHelixShaftLength(Anchor $anchor, int $helixNumber): float
    {
        if ($helixNumber == 0) return 0;

        $length = 0;
        foreach ($anchor->helixes as $helix) {
            if ($helix->helix_id <= $helixNumber) {
                $length += (3 * $helix->size / 12);
            }
        }

        return $length;
    }

    private function calculateShaftResistance(
        Anchor $anchor,
        SoilProfile $profile,
        Collection $soilLayers,
        Collection $helixCapacities,
        float $depth,
        CalculationType $calculationType
    ): float {
        if ($anchor->omit_shaft_resistance) return 0;

        return ($profile->negative_soil_layers <= 1 && $profile->external_friction != 0)
            ? $this->shaftResistanceCalculator->calculate($anchor, $profile, $depth, $calculationType, $helixCapacities->toArray())
            : $this->shaftResistanceCalculator->calculate($anchor, $profile, $depth, $calculationType);
    }

    private function calculateTorsionalResistance(
        Anchor $anchor,
        SoilProfile $profile,
        Collection $soilLayers,
        Collection $helixCapacities,
        float $depth,
        float $dTorsonalResistance,
        CalculationType $calculationType
    ): float {
        $torsionalResistance = $dTorsonalResistance;

        if (!$anchor->omit_shaft_resistance) {
            $value = $this->torsionalResistanceCalculator->calculate($anchor, $profile, $depth, $calculationType);
            $torsionalResistance += $value;
        }

        return round($torsionalResistance, 0);
    }

    private function setBasicCalculations(Anchor $anchor, SoilProfile $profile): void
    {
        $anchor->lead_shaft_od_in = $this->convertMixedNumberToInches($anchor->lead_shaft_od);
        $anchor->extension_shaft_od_in = $this->convertMixedNumberToInches($anchor->extension_shaft_od);
        if ($anchor->pile_head_position > 0) {
            $layers = $profile->soilLayers->sortBy('start_depth')->map(function (SoilLayer $layer) use ($anchor) {
                $layer->start_depth = $layer->start_depth - $anchor->pile_head_position;
                return $layer;
            });
    
            $profile->setRelation('soilLayers', $layers);
        }
    }

    private function convertMixedNumberToInches(string $value): float
    {
        $value = str_replace('-', ' ', trim($value));
        $parts = explode(' ', $value);

        if (count($parts) === 2) {
            return (int)$parts[0] + $this->convertFractionToDecimal($parts[1]);
        } elseif (strpos($parts[0], '/') !== false) {
            return $this->convertFractionToDecimal($parts[0]);
        } else {
            return (float)$parts[0];
        }
    }

    private function convertFractionToDecimal(string $fraction): float
    {
        $parts = explode('/', $fraction);
        return count($parts) === 2 ? (float)$parts[0] / (float)$parts[1] : 0;
    }

    private function reportData(Project $project, Anchor $anchor, Collection $soilLayers, Collection $helixes, ResultData $results): void
    {
        $specialist = $project->projectSpecialist()->first();
        $soilProfile = $project->soilProfile()->first();

        // Project Data
        $results->ProjectName = $project->project_name;
        $results->ProjectNumber = $project->project_number;
        $results->SoilReportNumber = $project->soil_report_number;
        $results->SoilReportDate = date('m/d/Y', strtotime($project->soil_report_date));
        $results->PileType = $project->pile_type;
        $results->BoringNumber = $project->boring_number;
        $results->BoringLogDate = date('m/d/Y', strtotime($project->boring_log_date));
        $results->BoringTerminationDepth = $project->termination_depth;
        $results->ProjectAddress = $project->project_address;
        $results->ProjectNotes = $project->remarks;

        // Soil Specialist Data
        $results->SpecialistName = $specialist->name;
        $results->SpecialistEmail = $specialist->specialist_email;
        $results->SpecialistCompany = $specialist->company_name;

        // Soil Profile Data
        $results->MaxDepth = $soilProfile->maximum_depth;
        $results->WaterTableDepth = $soilProfile->water_table_depth;
        $results->SoilType = $soilProfile->soil_type;
        $updatedSoilLayers = $soilLayers->map(function ($layer) {
            $data = $layer->toArray();
            $data['soil_layer_type_name'] = $layer->soilLayerType->name ?? null;
            return $data;
        });
        $results->SoilLayers = $updatedSoilLayers;

        // Anchor Data
        $results->AnchorDeclinationDegree = $anchor->anchor_declination_degree;
        $results->PileHeadPosition = $anchor->pile_head_position;
        $results->XValues = [$anchor->x1, $anchor->x2, $anchor->x3, $anchor->x4, $anchor->x5];
        $results->YValues = [$anchor->y1, $anchor->y2, $anchor->y3, $anchor->y4, $anchor->y5];
        $results->OmitShaftResistance = $anchor->omit_shaft_resistance;
        $results->OmitHelixMechanicalStrengthCheck = $anchor->omit_helix_mechanical_strength_check;
        $results->OmitShaftMechanicalStrengthCheck = $anchor->omit_shaft_mechanical_strength_check;
    }
}