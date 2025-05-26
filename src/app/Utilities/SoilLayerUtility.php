<?php

namespace App\Utilities;

use App\Models\SoilLayer;
use Illuminate\Support\Collection;

class SoilLayerUtility
{
    /**
     * Finds the soil layer at a given depth.
     *
     * @param Collection $soilLayers Collection of soil layers.
     * @param float $depth Depth below the ground surface in feet.
     * @return SoilLayer|null The soil layer at the specified depth.
     */
    public static function findSoilLayerAtDepth(Collection $soilLayers, float $depth): ?SoilLayer
    {
        // Sort soil layers by start_depth in ascending order
        $sortedSoilLayers = $soilLayers->sortBy('start_depth');

        // Check if depth is above first layer
        if ($depth < $sortedSoilLayers->first()->start_depth) {
            return $sortedSoilLayers->first();
        }
        
        // Check if depth is below last layer
        if ($depth > $sortedSoilLayers->last()->start_depth + $sortedSoilLayers->last()->thickness) {
            return null;
        }

        // Iterate through the soil layers
        foreach ($sortedSoilLayers as $index => $soilLayer) {
            // Get the start depth of the current layer
            $layerStart = $soilLayer->start_depth;

            // Get the start depth of the next layer (if it exists)
            $layerEnd = $sortedSoilLayers->has($index + 1)
                ? $sortedSoilLayers[$index + 1]->start_depth
                : 100; // Use 100 feet as the maximum depth for the final layer

            // Check if the depth falls within the current layer's range
            if ($depth >= $layerStart && $depth <= $layerEnd) {
                return $soilLayer;
            }
        }

        // If no layer is found, return null
        return null;
    }
}