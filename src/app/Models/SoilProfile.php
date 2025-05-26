<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Collection;

class SoilProfile extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'soil_profile_id';
    protected $fillable = [
        'project_id',
        'maximum_depth',
        'water_table_depth',
        'soil_type',
    ];

    protected $casts = [
        'maximum_depth' => 'float',
        'water_table_depth' => 'float',
    ];

    private float $cutOffDepth = 0;
    private int $negSoilStratumCount = 0;
    private float $externalFriction = 0;
    /**
     * The project the soil profile belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the soil layers in this soil profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function soilLayers(): HasMany
    {
        return $this->hasMany(SoilLayer::class, 'soil_profile_id', 'soil_profile_id');
    }

    // Check if there are any negative soil layers
    public function hasNegativeLayers()
    {
        return 0;
    }

    public function getNegativeLayers()
    {
        return $this->negSoilStratumCount;
    }

    public function getExternalFriction()
    {
        return $this->externalFriction;
    }

    // Update the thicknesses of soil layers based on depth
    public function updateThickness(Collection $soilLayers)
    {
        $hdepFactor = 0;
        $this->negSoilStratumCount = 0;
        // If only one soil layer
        if ($soilLayers->count() == 1) {
            $layer = $soilLayers->first();
            if ($layer->start_depth < 0) {
                $hdepFactor = -$layer->start_depth;
                $thickness = -$layer->start_depth + $this->maximum_depth;
                $this->negSoilStratumCount = 1;
            } else {
                $thickness = 500;
            }
            $layer->thickness = $thickness;
            return;
        }

        // For multiple layers
        foreach ($soilLayers as $index => $layer) {
            // If last layer
            if ($index == $soilLayers->count() - 1) {
                if ($layer->start_depth < 0) {
                    $thickness = -$layer->start_depth + $this->maximum_depth;
                    $hdepFactor += -$layer->start_depth;
                    $this->negSoilStratumCount++;
                } elseif ($layer->start_depth == 0) {
                    $thickness = 500;
                } else {
                    $thickness = 5000 - $layer->start_depth;
                }
            } else {
                $nextLayer = $soilLayers[$index + 1];
                
                if ($layer->start_depth < 0 && $nextLayer->start_depth < 0) {
                    $thickness = (-$layer->start_depth) - (-$nextLayer->start_depth);
                    $hdepFactor += $thickness;
                    $this->negSoilStratumCount++;
                } elseif ($layer->start_depth < 0 && $nextLayer->start_depth >= 0) {
                    if ($nextLayer->start_depth > 0) {
                        $this->cutOffDepth = 0 - $layer->start_depth;
                    }
                    $thickness = -$layer->start_depth + $nextLayer->start_depth;
                    $hdepFactor += -$layer->start_depth;
                    $this->negSoilStratumCount++;
                } else {
                    $thickness = $nextLayer->start_depth - $layer->start_depth;
                }
            }

            $layer->thickness = $thickness;
        }
    }

    public function getCutOffDepth()
    {
        return $this->cutOffDepth;
    }
}
