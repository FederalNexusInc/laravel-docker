<?php

namespace App\Services;

use App\Models\Anchor;
use App\Models\SoilLayer;
use App\Models\SoilProfile;
use App\Enums\CalculationType;

class TorsionalResistanceCalculator
{
    private const WATER_UNIT_WEIGHT = 62.4; // Unit weight of water in pcf
    private const PI = 3.141592;

    /**
     * Calculate torsional resistance based on soil conditions and anchor configuration
     *
     * @param Anchor $anchor
     * @param SoilProfile $profile
     * @param float $embedmentDepth
     * @param array|null $helixResults
     * @return float
     */
    public function calculate(Anchor $anchor, SoilProfile $profile, float $embedmentDepth, CalculationType $calculationType, ?array $helixResults = null): float
    {
        // Determine which calculation strategy to use based on soil profile
        if ($profile->negative_soil_layers > 1) {
            return $this->calculateNegativeProfileTorsionalResistance($anchor, $profile, $embedmentDepth);
        } elseif ($profile->external_friction == 0) {
            return $this->calculateZeroExternalFrictionTorsionalResistance($anchor, $profile, $embedmentDepth, $calculationType);
        } else {
            // For split section, use shaft resistance multiplied by shaft diameter/24
            $shaftResistance = app(ShaftResistanceCalculator::class)->calculate(
                $anchor, 
                $profile, 
                $embedmentDepth, 
                $helixResults
            );
            $shaftDiameter = $this->getEffectiveShaftDiameter($anchor, $embedmentDepth);
            return $shaftResistance * ($shaftDiameter / 24);
        }
    }

    /**
     * Calculate torsional resistance for negative soil profiles
     *
     * @param Anchor $anchor
     * @param SoilProfile $profile
     * @param float $embedmentDepth
     * @return float
     */
    private function calculateNegativeProfileTorsionalResistance(Anchor $anchor, SoilProfile $profile, float $embedmentDepth): float
    {
        $tita = (3.141592 / 180) * $anchor->tita_value;
        $leadShaftSize = $anchor->lead_shaft_od_in / 12; // Convert to feet
        $extensionShaftSize = $anchor->extension_shaft_od_in / 12; // Convert to feet
        
        // Get soil layers ordered by level and calculate stratum tops
        $layers = $profile->soilLayers->sortBy('level');
        $stratumTops = $layers->pluck('start_depth')->map(function($depth) use ($tita) {
            return $depth / sin($tita);
        })->toArray();
        
        // Add a zero at the end to avoid array bounds issues
        $stratumTops[] = 0;
        
        // Find first layer with positive start depth and get initial values
        $firstPositiveLayer = $layers->firstWhere('start_depth', '>', 0);
        $startDepth = $firstPositiveLayer ? $firstPositiveLayer->start_depth : 0;
        $startMUW = $layers->firstWhere('level', 0)->moist_unit_weight;
        
        // Generate shaft sections for calculation
        $shaftSections = $this->generateShaftSections($embedmentDepth, $anchor->lead_shaft_length);
        
        $totalTorsionalResistance = 0;
        
        foreach ($shaftSections as $i => $section) {
            $section1 = $i == 0 ? 0 : $shaftSections[$i - 1];
            $section2 = $section;
            
            // Determine shaft diameter based on embedment depth
            $shaftOD = ($section <= ($embedmentDepth - $anchor->lead_shaft_length)) 
                ? $extensionShaftSize 
                : $leadShaftSize;
            
            // Calculate midpoint values (Xmp, Hmpi, Dmp)
            $xmp = ($section1 + $section2) / 2 * cos($tita);
            $hmpi = $this->calculateHmpi($xmp, $anchor);
            $dmp = $xmp * tan($tita);
            
            // Find the appropriate soil layer for this section
            foreach ($layers as $k => $layer) {
                if ($section >= $stratumTops[$k] && ($section <= $stratumTops[$k + 1] || $stratumTops[$k + 1] == 0)) {
                    // Calculate effective stress
                    $se = $this->calculateEffectiveStressForNegativeProfile(
                        $i, 
                        $k, 
                        $hmpi, 
                        $dmp, 
                        $startDepth, 
                        $startMUW, 
                        $profile->water_table_depth, 
                        $profile->getSoilStratumDepth(), 
                        $layer
                    );
                    
                    // Calculate resistance for this section and add to total
                    $sectionResistance = self::PI * $shaftOD * ($section2 - $section1) * 
                        (($layer->coefficient_of_adhesion * $layer->cohesion) + 
                         ($se * $layer->coefficient_of_external_friction)) * 
                        ($shaftOD / 2); // Multiply by radius for torsional
                    
                    $totalTorsionalResistance += $sectionResistance;
                    break;
                }
            }
        }
        
        return round($totalTorsionalResistance);
    }

    private function calculateBasicResistance(
        float $section1, float $section2, float $activeLength,
        float $embedmentDepth, float $helixDepth,
        Anchor $anchor, SoilProfile $profile, CalculationType $calculationType
    ): float {
        $shaftOD = ($helixDepth < ($embedmentDepth - $anchor->lead_shaft_length)) 
            ? $anchor->extension_shaft_od_in / 12 
            : $anchor->lead_shaft_od_in / 12;
        
        $soilLayer = $this->findSoilLayerAtDepth($helixDepth, $profile);
        $value = $this->calculateEffectiveStress(
            $soilLayer, $helixDepth, 
            $profile->water_table_depth, 
            $calculationType, $section1, $section2
        );
        
        return self::PI * $shaftOD * $activeLength * 
              (($soilLayer->coefficient_of_adhesion * $soilLayer->cohesion) + 
               ($value * $soilLayer->coefficient_of_external_friction));
    }

    /**
     * Calculate torsional resistance when external friction is zero
     *
     * @param Anchor $anchor
     * @param float $embedmentDepth
     * @return float
     */
    private function calculateZeroExternalFrictionTorsionalResistance(Anchor $anchor, SoilProfile $profile, float $pEmbeded, CalculationType $calculationType): float
    {
        $section1 = $pEmbeded;
        $section2 = 0;
        $activeLength = $pEmbeded;
        $helixDepth = $section1;
        
        // Get appropriate shaft diameter
        $dSumQT = $this->calculateBasicResistance(
            $activeLength, 0, $activeLength, 
            $pEmbeded, $activeLength, 
            $anchor, $profile, $calculationType
        );
        
        $asConst1 = $section1 < ($pEmbeded - $anchor->lead_shaft_length) 
            ? $anchor->extension_shaft_od_in 
            : $anchor->lead_shaft_od_in;

        $dSumQT = $dSumQT * ($asConst1 / 24);

        return $dSumQT;
    }

    /**
     * Generate shaft sections for calculation
     *
     * @param float $embedmentDepth
     * @param float $leadShaftLength
     * @return array
     */
    private function generateShaftSections(float $embedmentDepth, float $leadShaftLength): array
    {
        $sections = [];
        $step = 1.0; // 1 foot sections by default
        
        // Add sections from top to bottom
        for ($depth = 0; $depth <= $embedmentDepth; $depth += $step) {
            $sections[] = $depth;
        }
        
        // Make sure we include the exact embedment depth
        if (!in_array($embedmentDepth, $sections)) {
            $sections[] = $embedmentDepth;
            sort($sections);
        }
        
        return $sections;
    }
    
    /**
     * Calculate Hmpi value based on backslope parameters
     *
     * @param float $xmp
     * @param Anchor $anchor
     * @return float
     */
    private function calculateHmpi(float $xmp, Anchor $anchor): float
    {
        // If backslope data is available, use it
        if ($anchor->backSlope) {
            return $anchor->backSlope->m * $xmp + $anchor->backSlope->b;
        }
        
        // Default to zero if no backslope data
        return 0;
    }
    
    /**
     * Calculate effective stress for negative profile sections
     *
     * @param int $sectionIndex
     * @param int $layerIndex
     * @param float $hmpi
     * @param float $dmp
     * @param float $startDepth
     * @param float $startMUW
     * @param float $phreaticSurface
     * @param array $soilDepths
     * @param SoilLayer $layer
     * @return float
     */
    private function calculateEffectiveStressForNegativeProfile(
        int $sectionIndex, 
        int $layerIndex, 
        float $hmpi, 
        float $dmp, 
        float $startDepth, 
        float $startMUW, 
        float $phreaticSurface, 
        array $soilDepths, 
        SoilLayer $layer
    ): float {
        // First section special case
        if ($sectionIndex == 0) {
            return ($hmpi + $dmp) * $layer->moist_unit_weight;
        }
        
        // For layers below the first
        if ($layerIndex > 0) {
            if ($phreaticSurface == round($sectionIndex * sin((3.141592 / 180) * $anchor->tita_value), 0)) {
                return (($hmpi + $startDepth) * $startMUW) + 
                       (($dmp - $phreaticSurface) * ($layer->saturated_unit_weight - self::WATER_UNIT_WEIGHT)) + 
                       (($phreaticSurface - $soilDepths[$layerIndex]) * $layer->moist_unit_weight);
            } else {
                return (($hmpi + $startDepth) * $startMUW) + 
                       (($dmp - $soilDepths[$layerIndex]) * $layer->moist_unit_weight);
            }
        }
        
        // Default case
        return ($hmpi + $dmp) * $layer->moist_unit_weight;
    }
    
    /**
     * Get effective shaft diameter based on embedment depth
     *
     * @param Anchor $anchor
     * @param float $embedmentDepth
     * @return float
     */
    private function getEffectiveShaftDiameter(Anchor $anchor, float $embedmentDepth): float
    {
        return ($embedmentDepth <= ($anchor->embedment_depth - $anchor->lead_shaft_length)) 
            ? $anchor->extension_shaft_od_in / 12  // Use extension shaft if in extension section
            : $anchor->lead_shaft_od_in / 12;     // Otherwise use lead shaft
    }

    /**
     * Find soil layer at specific depth
     *
     * @param float $depth
     * @param SoilProfile $profile
     * @return SoilLayer
     */
    private function findSoilLayerAtDepth(float $depth, SoilProfile $profile): SoilLayer
    {
        foreach ($profile->soilLayers as $layer) {
            $layerEnd = $layer->start_depth + $layer->thickness;
            if ($depth >= $layer->start_depth && $depth <= $layerEnd) {
                return $layer;
            }
        }
        
        // Return last layer if depth exceeds all layers
        return $profile->soilLayers->last();
    }

    /**
     * Calculate effective stress based on water table position
     *
     * @param SoilLayer $layer
     * @param float $depth
     * @param float $waterTableDepth
     * @param CalculationType $calculationType
     * @return float
     */
    private function calculateEffectiveStress(
        SoilLayer $layer, float $depth, 
        float $waterTableDepth, CalculationType $calculationType,
        float $section1, float $section2,
    ): float {
        if ($waterTableDepth == 0) {
            return $depth * $layer->saturated_unit_weight - ($depth * self::WATER_UNIT_WEIGHT);
        } elseif ($depth < $waterTableDepth) {
            return $calculationType === CalculationType::TENSION
                ? (($section1 + $section2) / 2) * $layer->moist_unit_weight
                : $depth * $layer->moist_unit_weight;
        }
        
        $aboveWater = $waterTableDepth * $layer->moist_unit_weight;
        $belowWater = ($depth - $waterTableDepth) * ($layer->saturated_unit_weight - self::WATER_UNIT_WEIGHT);
        return $aboveWater + $belowWater;
    }
}