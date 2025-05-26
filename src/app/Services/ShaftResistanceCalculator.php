<?php

namespace App\Services;

use App\Models\Anchor;
use App\Models\SoilLayer;
use App\Models\SoilProfile;
use App\Enums\CalculationType;
use Illuminate\Database\Eloquent\Collection;

class ShaftResistanceCalculator
{
    private const WATER_UNIT_WEIGHT = 62.4; // Unit weight of water in pcf
    private const PI = 3.141592;

    public function calculate(Anchor $anchor, SoilProfile $profile, float $embedmentDepth, CalculationType $calculationType, ?array $helixResults = null): float
    {
        if ($profile->getNegativeLayers() > 1) {
            return $this->calculateNegativeProfileResistance($anchor, $profile, $embedmentDepth, $calculationType);
        } elseif ($profile->getExternalFriction() == 0) {
            return $this->calculateZeroExternalFrictionResistance($anchor, $profile, $embedmentDepth, $calculationType);
        }

        if ($helixResults === null) {
            throw new \InvalidArgumentException("Helix results array is required for split section calculation");
        }
        
        return $this->calculateSplitSectionResistance($anchor, $profile, $helixResults, $embedmentDepth, $calculationType);
    }

    private function calculateNegativeProfileResistance(Anchor $anchor, SoilProfile $profile, float $embedmentDepth): float
    {
        $tita = $anchor->tita_value;
        $leadShaftSize = $anchor->lead_shaft_od_in / 12;
        $extensionShaftSize = $anchor->extension_shaft_od_in / 12;
        
        $layers = $profile->soilLayers->sortBy('level');
        $stratumTops = $layers->pluck('start_depth')->map(function($depth) use ($tita) {
            return $depth / sin($tita);
        })->toArray();
        
        $stratumTops[] = 0; // Add a zero at the end
        
        $firstPositiveLayer = $layers->firstWhere('start_depth', '>', 0);
        $startDepth = $firstPositiveLayer ? $firstPositiveLayer->start_depth : 0;
        $startMUW = $layers->firstWhere('level', 0)->moist_unit_weight;
        
        $shaftSections = $this->generateShaftSections($embedmentDepth, $anchor->lead_shaft_length);
        $shaftSectionData = $this->prepareShaftSectionData($anchor, $embedmentDepth);
        
        $qs = 0;
        
        foreach ($shaftSections as $i => $section) {
            $section1 = $i == 0 ? 0 : $shaftSections[$i - 1];
            $section2 = $section;
            
            $shaftOD = ($section <= ($embedmentDepth - $anchor->lead_shaft_length)) 
                ? $extensionShaftSize 
                : $leadShaftSize;
            
            $xmp = ($section1 + $section2) / 2 * cos($tita);
            $hmpi = $this->calculateHmpi($xmp, $anchor, $section2, $shaftSectionData);
            $dmp = $xmp * tan($tita);
            
            foreach ($layers as $k => $layer) {
                if ($section >= $stratumTops[$k] && ($section <= $stratumTops[$k + 1] || $stratumTops[$k + 1] == 0)) {
                    $se = $this->calculateEffectiveStressForNegativeProfile(
                        $i, $k, $hmpi, $dmp, $startDepth, $startMUW, 
                        $profile->water_table_depth, $profile->getSoilStratumDepth(), 
                        $layer
                    );
                    
                    $qs += self::PI * $shaftOD * ($section2 - $section1) * 
                          (($layer->coefficient_of_adhesion * $layer->cohesion) + 
                           ($se * $layer->coefficient_of_external_friction));
                    break;
                }
            }
            
            // Skip dead zones if they exist
            if (isset($shaftSectionData->deadZone) && in_array($section2, $shaftSectionData->deadZone)) {
                $i++; // Skip next section
            }
        }
        
        return round($qs);
    }

    private function prepareShaftSectionData(Anchor $anchor, float $embedmentDepth): object
    {
        $data = new \stdClass();
        
        if ($anchor->backSlope) {
            // Convert backslope data to segments
            $data->bsBreaks = [$anchor->x1, $anchor->x2, $anchor->x3, $anchor->x4, $anchor->x5];
            $data->mSegment = [];
            $data->bSegment = [];
            
            // Calculate slopes and intercepts for each segment
            for ($i = 1; $i < 5; $i++) {
                $x1 = $anchor->{"x$i"};
                $y1 = $anchor->{"y$i"};
                $x2 = $anchor->{"x".($i+1)};
                $y2 = $anchor->{"y".($i+1)};
                
                if ($x2 != $x1) {
                    $m = ($y2 - $y1) / ($x2 - $x1);
                    $b = $y1 - $m * $x1;
                } else {
                    $m = 0;
                    $b = $y1;
                }
                
                $data->mSegment[] = $m;
                $data->bSegment[] = $b;
            }
        }
        
        // Add dead zones if needed
        $data->deadZone = []; // Populate this array with dead zone values if applicable
        
        return $data;
    }

    private function calculateHmpi(float $xmp, Anchor $anchor, float $section2, object $shaftSectionData): float
    {
        if (!$anchor->backSlope) {
            return 0;
        }

        if (isset($shaftSectionData->bsBreaks)) {
            foreach ($shaftSectionData->bsBreaks as $j => $breakPoint) {
                if ($section2 <= $breakPoint) {
                    return $shaftSectionData->mSegment[$j] * $xmp + $shaftSectionData->bSegment[$j];
                }
            }
        }
        
        return $anchor->backSlope->m * $xmp + $anchor->backSlope->b;
    }

    private function calculateSplitSectionResistance(Anchor $anchor, SoilProfile $profile, array $helixResults, float $embedmentDepth, CalculationType $calculationType): float
    {
        $extensionLength = max($embedmentDepth - $anchor->lead_shaft_length, 0);
        $sections = $profile->getSoilStratumDepth();
        $sections[] = $profile->water_table_depth;
        sort($sections);
        
        $sumQT = 0;
        
        foreach ($anchor->helixes as $i => $helix) {
            $sectionData = $helixResults[$i];
            
            if ($calculationType === CalculationType::TENSION) {
                $sectionTop = $sectionData['helix_depth2'];
                $sectionBottom = ($i + 1 == count($anchor->helixes)) ? 0 : $helixResults[$i + 1]['helix_depth4'];
            } else {
                $sectionTop = $sectionData['helix_depth1'];
                $sectionBottom = ($i + 1 < count($anchor->helixes)) ? $helixResults[$i + 1]['helix_depth2'] : 0;
            }
            
            $sumQT += $this->calculateSplitSectionQT(
                $sections, $sectionTop, $sectionBottom, 
                $embedmentDepth, $extensionLength, 
                $anchor, $profile
            );
        }
        
        return $sumQT;
    }

    private function calculateZeroExternalFrictionResistance(Anchor $anchor, SoilProfile $profile, float $embedmentDepth, CalculationType $calculationType): float
    {
        $helixSpacing = 0;
        $sumQT = 0;
        
        foreach ($anchor->helixes as $i => $helix) {
            if ($i == (count($anchor->helixes) - 1)) {
                $activeLength = $calculationType === CalculationType::COMPRESSION
                    ? $embedmentDepth - $helixSpacing
                    : $embedmentDepth - $helixSpacing - ($helix->size / 12);
            } else {
                $helixSpacing += (3 * ($helix->size / 12));
                
                $activeLength = $calculationType === CalculationType::COMPRESSION
                    ? (3 * ($helix->size / 12)) - ($anchor->helixes[$i + 1]->size / 12)
                    : (3 * ($helix->size / 12)) - ($helix->size / 12);
            }
            $sumQT += $this->calculateBasicResistance(
                $activeLength, 0, $activeLength, 
                $embedmentDepth, $activeLength, 
                $anchor, $profile, $calculationType
            );
        }
        
        return $sumQT;
    }

    private function generateShaftSections(float $embedmentDepth, float $leadShaftLength): array
    {
        $sections = [];
        $step = 1.0; // 1 foot sections
        
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

    private function calculateEffectiveStressForNegativeProfile(
        int $sectionIndex, int $layerIndex, float $hmpi, float $dmp, 
        float $startDepth, float $startMUW, float $phreaticSurface, 
        array $soilDepths, SoilLayer $layer
    ): float {
        if ($sectionIndex == 0) {
            return ($hmpi + $dmp) * $layer->moist_unit_weight;
        }
        
        if ($layerIndex > 0) {
            if ($phreaticSurface == round($sectionIndex * sin($this->anchor->tita_value), 0)) {
                return (($hmpi + $startDepth) * $startMUW) + 
                       (($dmp - $phreaticSurface) * ($layer->saturated_unit_weight - self::WATER_UNIT_WEIGHT)) + 
                       (($phreaticSurface - $soilDepths[$layerIndex]) * $layer->moist_unit_weight);
            }
            return (($hmpi + $startDepth) * $startMUW) + 
                   (($dmp - $soilDepths[$layerIndex]) * $layer->moist_unit_weight);
        }
        
        return ($hmpi + $dmp) * $layer->moist_unit_weight;
    }
    
    private function calculateSplitSectionQT(
        array $sections, float $sectionTop, float $sectionBottom, 
        float $embedmentDepth, float $extensionLength, 
        Anchor $anchor, SoilProfile $profile
    ): float {
        $shaftSections = [$sectionTop];
        
        foreach ($sections as $depth) {
            if ($depth < $sectionTop && $depth > $sectionBottom) {
                $shaftSections[] = $depth;
            }
        }
        
        $shaftSections[] = $sectionBottom;
        sort($shaftSections);
        
        $sumQT = 0;
        
        for ($i = 0; $i < count($shaftSections) - 1; $i++) {
            $section1 = $shaftSections[$i + 1];
            $section2 = $shaftSections[$i];
            
            if ($section1 > $extensionLength && $extensionLength > $section2) {
                $sumQT += $this->calculateBasicResistance(
                    $section1, $extensionLength, $section1 - $extensionLength,
                    $embedmentDepth, ($section1 + $extensionLength) / 2,
                    $anchor, $profile
                );
                
                $sumQT += $this->calculateBasicResistance(
                    $extensionLength, $section2, $extensionLength - $section2,
                    $embedmentDepth, ($extensionLength + $section2) / 2,
                    $anchor, $profile
                );
            } else {
                $sumQT += $this->calculateBasicResistance(
                    $section1, $section2, $section1 - $section2,
                    $embedmentDepth, ($section1 + $section2) / 2,
                    $anchor, $profile
                );
            }
        }
        
        return $sumQT;
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
    
    private function findSoilLayerAtDepth(float $depth, SoilProfile $profile): SoilLayer
    {
        foreach ($profile->soilLayers as $layer) {
            $layerEnd = $layer->start_depth + $layer->thickness;
            if ($depth >= $layer->start_depth && $depth <= $layerEnd) {
                return $layer;
            }
        }
        
        return $profile->soilLayers->last();
    }

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