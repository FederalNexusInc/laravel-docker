<?php

namespace App\Services;

use App\Models\Anchor;
use App\Models\SoilLayer;
use App\Models\SoilProfile;
use Illuminate\Database\Eloquent\Collection;

class HelixCapacityCalculator
{
    /**
     * Calculates the helix capacity based on soil profile type (positive or negative layers).
     *
     * @param Anchor $anchor
     * @param SoilProfile $soilProfile
     * @param float $helixDepth
     * @param float $horizontalDepth
     * @param float $pCon
     * @return array
     */
    public function calculateHelixCapacities(Anchor $anchor, SoilProfile $soilProfile, Collection $soilLayers,float $helixDepth, float $horizontalDepth, float $pCon): array
    {
        if ($soilProfile->hasNegativeLayers()) {
            return $this->calculateForNegativeProfile($anchor, $soilProfile, $soilLayers, $helixDepth, $horizontalDepth, $pCon);
        } else {
            return $this->calculateForPositiveProfile($anchor, $soilProfile, $soilLayers, $helixDepth, $pCon);
        }
    }

    /**
     * Calculates helix capacity for soil profiles with negative layers.
     *
     * @param Anchor $anchor
     * @param SoilProfile $soilProfile
     * @param float $helixDepth
     * @param float $horizontalDepth
     * @param float $pCon
     * @return array
     */
    protected function calculateForNegativeProfile(Anchor $anchor, SoilProfile $soilProfile, Collection $soilLayers, float $helixDepth, float $horizontalDepth, float $pCon): array
    {
        $result = [
            'QT' => 0,
            'count' => 0,
            'imperial_stratum_level' => 0,
            'imperial_stratum' => $anchor->empirical_torque_factor,
        ];

        // Get the last negative layer
        $lastNegativeLayer = $soilProfile->soilLayers
            ->where('start_depth', '<', 0)
            ->sortByDesc('start_depth')
            ->first();

        if (!$lastNegativeLayer) {
            return $result;
        }

        // Calculate X and Y values based on anchor back slope data
        $x1 = $anchor->x1;
        $x2 = $anchor->x2;
        $y1 = $anchor->y1;
        $y2 = $anchor->y2;

        if ($horizontalDepth < $x1) {
            $x1 = 0;
            $y1 = 0;
            $x2 = $anchor->x1;
            $y2 = $anchor->y1;
        } elseif ($horizontalDepth > $anchor->x1 && $horizontalDepth < $anchor->x2) {
            $x1 = $anchor->x1;
            $y1 = $anchor->y1;
            $x2 = $anchor->x2;
            $y2 = $anchor->y2;
        } elseif ($horizontalDepth > $anchor->x2 && $horizontalDepth < $anchor->x3) {
            $x1 = $anchor->x2;
            $y1 = $anchor->y2;
            $x2 = $anchor->x3;
            $y2 = $anchor->y3;
        } elseif ($horizontalDepth > $anchor->x3 && $horizontalDepth < $anchor->x4) {
            $x1 = $anchor->x3;
            $y1 = $anchor->y3;
            $x2 = $anchor->x4;
            $y2 = $anchor->y4;
        } elseif ($horizontalDepth > $anchor->x4 && $horizontalDepth < $anchor->x5) {
            $x1 = $anchor->x4;
            $y1 = $anchor->y4;
            $x2 = $anchor->x5;
            $y2 = $anchor->y5;
        }

        // Calculate temporary value
        $temp = $y1 + (($horizontalDepth - $x1) / ($x2 - $x1)) * ($y2 - $y1) - (-$lastNegativeLayer->start_depth);
        $resultValue = 0;
        $addThickness = 0;

        // Process layers above the last negative layer
        foreach ($soilProfile->soilLayers->where('start_depth', '<', $lastNegativeLayer->start_depth)->sortBy('start_depth') as $layer) {
            if ($layer->start_depth == 0 && $temp < $layer->thickness) {
                $resultValue = $temp * $layer->moist_unit_weight;
                break;
            } else {
                $addThickness += $layer->thickness;
                if ($temp <= $addThickness) {
                    $resultValue += (($temp - ($addThickness - $layer->thickness)) * $layer->moist_unit_weight);
                    break;
                } else {
                    $resultValue += ($layer->thickness * $layer->moist_unit_weight);
                }
            }
        }

        // Handle different water table scenarios
        if ($soilProfile->water_table_depth == 0) {
            $value = 0;
            $addThickness = 0;

            if ($helixDepth < $lastNegativeLayer->thickness) {
                $value = $resultValue + $helixDepth * $lastNegativeLayer->saturated_unit_weight;
                $result['QT'] = $pCon * (($lastNegativeLayer->cohesion * $lastNegativeLayer->nc) + ($value * $lastNegativeLayer->nq));
                $result['count']++;
                return $result;
            } else {
                foreach ($soilProfile->soilLayers->where('start_depth', '>=', $lastNegativeLayer->start_depth)->sortBy('start_depth') as $layer) {
                    $addThickness += $layer->thickness;
                    if ($helixDepth <= $addThickness - $soilProfile->getCutOffDepth()) {
                        $value = $resultValue + $value + (($helixDepth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->saturated_unit_weight);
                        $value = $resultValue + $value - (($helixDepth - $soilProfile->water_table_depth) * 62.4);
                        $result['QT'] = $pCon * (($layer->cohesion * $layer->nc) + ($value * $layer->nq));
                        $result['count']++;
                        return $result;
                    } else {
                        $resultValue += ($layer->thickness * $layer->saturated_unit_weight);
                    }
                }
            }
        } elseif ($helixDepth > $soilProfile->water_table_depth) {
            $addThickness = 0;
            $value = 0;
            $overWaterLevel = false;

            foreach ($soilProfile->soilLayers->where('start_depth', '>=', $lastNegativeLayer->start_depth)->sortBy('start_depth') as $layer) {
                $addThickness += $layer->thickness;
                if ($helixDepth < $addThickness - $soilProfile->getCutOffDepth()) {
                    if (!$overWaterLevel) {
                        $value += (($soilProfile->water_table_depth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->moist_unit_weight);
                        $value += (($helixDepth - $soilProfile->water_table_depth) * $layer->saturated_unit_weight);
                        $value = $resultValue + $value - (($helixDepth - $soilProfile->water_table_depth) * 62.4);
                        $result['QT'] = $pCon * (($layer->cohesion * $layer->nc) + ($value * $layer->nq));
                        $result['count']++;
                        return $result;
                    } else {
                        $value += (($helixDepth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->saturated_unit_weight);
                        $value = $resultValue + $value - (($helixDepth - $soilProfile->water_table_depth)) * 62.4;
                        $result['QT'] = $pCon * (($layer->cohesion * $layer->nc) + ($value * $layer->nq));
                        $result['count']++;
                        return $result;
                    }
                } else {
                    if ($addThickness - $soilProfile->getCutOffDepth() > $soilProfile->water_table_depth) {
                        if (!$overWaterLevel) {
                            $value += (($soilProfile->water_table_depth - ($addThickness = $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->moist_unit_weight);
                            $value += (($addThickness - $soilProfile->water_table_depth) * $layer->saturated_unit_weight);
                            $overWaterLevel = true;
                        } else {
                            $value += ($layer->thickness * $layer->saturated_unit_weight);
                        }
                    } else {
                        $value += ($layer->thickness * $layer->moist_unit_weight);
                    }
                }
            }
        } elseif ($helixDepth < $soilProfile->water_table_depth) {
            $value = 0;
            $addThickness = 0;

            if ($helixDepth < $lastNegativeLayer->thickness) {
                $value = $resultValue + $helixDepth * $lastNegativeLayer->moist_unit_weight;
                $result['QT'] = $pCon * (($lastNegativeLayer->cohesion * $lastNegativeLayer->nc) + ($value * $lastNegativeLayer->nq));
                $result['count']++;
                return $result;
            } else {
                foreach ($soilProfile->soilLayers->where('start_depth', '>=', $lastNegativeLayer->start_depth)->sortBy('start_depth') as $layer) {
                    $addThickness += $layer->thickness;
                    if ($helixDepth <= $addThickness - $soilProfile->getCutOffDepth()) {
                        $value = $resultValue + $value + (($helixDepth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->moist_unit_weight);
                        $result['QT'] = $pCon * (($layer->cohesion * $layer->nc) + ($value * $layer->nq));
                        $result['count']++;
                        return $result;
                    } else {
                        $value += ($layer->thickness * $layer->moist_unit_weight);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Calculates helix capacity for soil profiles with only positive layers.
     *
     * @param Anchor $anchor
     * @param SoilProfile $soilProfile
     * @param float $helixDepth
     * @param float $pCon
     * @return array
     */
    protected function calculateForPositiveProfile(Anchor $anchor, SoilProfile $soilProfile, Collection $soilLayers, float $helixDepth, float $pCon): array
    {
        $soilProfile->updateThickness($soilLayers);
        $result = [
            'QT' => 0,
            'count' => 0,
            'imperial_stratum_level' => 0,
            'imperial_stratum' => $anchor->empirical_torque_factor,
        ];
        $imperialStratum = 0;
        // Ensure soil layers are loaded and sorted
        $sortedLayers = $soilLayers->sortBy('start_depth');

        // Check if there are any layers
        if ($sortedLayers->isEmpty()) {
            return $result;
        }

        if ($soilProfile->water_table_depth == 0) {
            $firstLayer = $sortedLayers->first();
    
            if ($firstLayer && $helixDepth < $firstLayer->thickness - $soilProfile->getCutOffDepth()) {
                $value = $helixDepth * $firstLayer->saturated_unit_weight;
                $value = $value - (($helixDepth - $soilProfile->water_table_depth) * 62.4);
    
                $result['count']++;
                $result['QT'] = $pCon * (($firstLayer->cohesion * $firstLayer->nc) + ($value * $firstLayer->nq));
                $result['imperial_stratum_level'] = $imperialStratum;
                return $result;
            } else {
                $addThickness = 0;
                $value = 0;
    
                foreach ($sortedLayers as $layer) {
                    $addThickness += $layer->thickness;
    
                    if ($helixDepth < $addThickness - $soilProfile->getCutOffDepth()) {
                        $imperialStratum++;
                        $value += (($helixDepth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->saturated_unit_weight);
                        $value = $value - (($helixDepth - $soilProfile->water_table_depth) * 62.4);
    
                        $result['count']++;
                        $result['imperial_stratum_level'] = $imperialStratum;
                        $result['QT'] = $pCon * (($layer->cohesion * $layer->nc) + ($value * $layer->nq));
                        return $result;
                    } else {
                        $value += $layer->thickness * $layer->saturated_unit_weight;
                    }
                }
            }
        } elseif ($helixDepth < $soilProfile->water_table_depth) {
            $firstLayer = $sortedLayers->first();
            if ($firstLayer && $helixDepth < $firstLayer->thickness - $soilProfile->getCutOffDepth()) {
                
                $value = $helixDepth * $firstLayer->moist_unit_weight;
                $result['count']++;
                $result['imperial_stratum_level'] = $imperialStratum;
                $result['QT'] = $pCon * (($firstLayer->cohesion * $firstLayer->nc) + ($value * $firstLayer->nq));
                return $result;
            } else {
                $addThickness = 0;
                $value = 0;
    
                foreach ($sortedLayers as $layer) {
                    $addThickness += $layer->thickness;
    
                    if ($helixDepth < $addThickness - $soilProfile->getCutOffDepth()) {
                        $imperialStratum++;
                        $value += ($helixDepth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->moist_unit_weight;
    
                        $result['count']++;
                        $result['imperial_stratum_level'] = $imperialStratum;
                        $result['QT'] = $pCon * (($layer->cohesion * $layer->nc) + ($value * $layer->nq));
                        return $result;
                    } else {
                        $value += $layer->thickness * $layer->moist_unit_weight;
                    }
                }
            }
        } elseif ($helixDepth > $soilProfile->water_table_depth) {
            $addThickness = 0;
            $value = 0;
            $overWaterLevel = false;
    
            foreach ($sortedLayers as $layer) {
                $addThickness += $layer->thickness;
    
                if ($helixDepth < $addThickness - $soilProfile->getCutOffDepth()) {
                    if (!$overWaterLevel) {
                        $imperialStratum++;
                        $value += (($soilProfile->water_table_depth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->moist_unit_weight);
                        $value += (($helixDepth - $soilProfile->water_table_depth) * $layer->saturated_unit_weight);
                        $value = $value - (($helixDepth - $soilProfile->water_table_depth) * 62.4);
    
                        $result['count']++;
                        $result['imperial_stratum_level'] = $imperialStratum;
                        $result['QT'] = $pCon * (($layer->cohesion * $layer->nc) + ($value * $layer->nq));

                        return $result;
                    } else {
                        $imperialStratum++;
                        $value += (($helixDepth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->saturated_unit_weight);
                        $value = $value - (($helixDepth - $soilProfile->water_table_depth) * 62.4);
    
                        $result['count']++;
                        $result['imperial_stratum_level'] = $imperialStratum;
                        $result['QT'] = $pCon * (($layer->cohesion * $layer->nc) + ($value * $layer->nq));

                        return $result;
                    }
                } else {
                    if ($addThickness - $soilProfile->getCutOffDepth() > $soilProfile->water_table_depth) {
                        if (!$overWaterLevel) {
                            $value += (($soilProfile->water_table_depth - ($addThickness - $layer->thickness - $soilProfile->getCutOffDepth())) * $layer->moist_unit_weight);
                            $value += (($addThickness - $soilProfile->water_table_depth) * $layer->saturated_unit_weight);
                            $overWaterLevel = true;
                        } else {
                            $value += $layer->thickness * $layer->saturated_unit_weight;
                        }
                    } else {
                        $value += $layer->thickness * $layer->moist_unit_weight;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Gets the helix shaft length for a specific helix number.
     *
     * @param Anchor $anchor
     * @param int $helixNumber
     * @return float
     */
    protected function getHelixShaftLength(Anchor $anchor, int $helixNumber): float
    {
        if ($helixNumber == 0) {
            return 0;
        }

        $length = 0;
        foreach ($anchor->helixes->where('position', '<', $helixNumber) as $helix) {
            $length += (3 * $helix->size / 12);
        }

        return $length;
    }
}