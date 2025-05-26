<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoilLayer extends Model
{
    use HasFactory;

    protected $primaryKey = 'soil_layer_id';
    protected $fillable = [
        'soil_profile_id',
        'start_depth',
        'blow_count',
        'soil_layer_type_id',
        'cohesion',
        'coefficient_of_adhesion',
        'angle_of_internal_friction',
        'coefficient_of_external_friction',
        'moist_unit_weight',
        'saturated_unit_weight',
        'nc',
        'nq',
        'thickness'
    ];

    /**
     * The soil profile the soil layer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function soilProfile(): BelongsTo
    {
        return $this->belongsTo(SoilProfile::class, 'soil_profile_id');
    }

    /**
     * The soil layer type the soil layer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function soilLayerType(): BelongsTo
    {
        return $this->belongsTo(SoilLayerType::class, 'soil_layer_type_id');
    }

    /**
     * Initialize default properties based on soil type and blow count
     */
    public function init()
    {
        $this->setSandDefaultValues();
        $this->setClayDefaultProperties();
        $this->setNq();
    }

    /**
     * Set default values for sand layers based on blow count
     */
    private function setSandDefaultValues()
    {
        if ($this->soilLayerType->name !== 'Sand') {
            return;
        }

        $n = $this->blow_count;

        if ($n >= 0 && $n <= 4) {
            $this->moist_unit_weight = 85;
            $this->saturated_unit_weight = 95;

            if ($n >= 0 && $n <= 2) {
                $this->angle_of_internal_friction = 20;
            } else {
                $this->angle_of_internal_friction = 25;
            }
        }

        if ($n >= 5 && $n <= 10) {
            $this->moist_unit_weight = 90;
            $this->saturated_unit_weight = 100;
            $this->angle_of_internal_friction = round(0.4 * $n + 26);
        }

        if ($n >= 11 && $n <= 50) {
            $this->moist_unit_weight = 100;
            $this->saturated_unit_weight = 110;

            if ($n >= 11 && $n <= 30) {
                $this->angle_of_internal_friction = round(0.263 * $n + 28.1);
            } else {
                $this->angle_of_internal_friction = round(0.21 * $n + 30.473);
            }
        }

        if ($n > 50) {
            $this->moist_unit_weight = 110;
            $this->saturated_unit_weight = 120;

            if ($n >= 50 && $n <= 70) {
                $this->angle_of_internal_friction = 42;
            } else {
                $this->angle_of_internal_friction = 45;
            }
        }
    }

    /**
     * Set default properties for clay layers based on blow count
     */
    private function setClayDefaultProperties()
    {
        if ($this->soilLayerType->name !== 'Clay') {
            return;
        }

        $n = $this->blow_count;

        if ($n == 0) {
            $this->cohesion = 0;
            $this->moist_unit_weight = 90;
            $this->saturated_unit_weight = 100;
        } elseif ($n == 1) {
            $this->cohesion = 250;
            $this->moist_unit_weight = 90;
            $this->saturated_unit_weight = 100;
        } elseif ($n == 2) {
            $this->cohesion = 500;
            $this->moist_unit_weight = 90;
            $this->saturated_unit_weight = 100;
        } elseif ($n == 3) {
            $this->cohesion = 500;
            $this->moist_unit_weight = 90;
            $this->saturated_unit_weight = 100;
        } elseif ($n == 4) {
            $this->cohesion = 1000;
            $this->moist_unit_weight = 90;
            $this->saturated_unit_weight = 100;
        } elseif ($n >= 5 && $n <= 8) {
            $this->cohesion = round(333.33 * $n - 666.65);
            $this->moist_unit_weight = 100;
            $this->saturated_unit_weight = 110;
        } elseif ($n >= 9 && $n <= 16) {
            $this->cohesion = round(285.71 * $n - 571.43);
            $this->moist_unit_weight = 100;
            $this->saturated_unit_weight = 110;
        } elseif ($n >= 17 && $n <= 32) {
            $this->cohesion = round(266.67 * $n - 533.33);
            $this->moist_unit_weight = 100;
            $this->saturated_unit_weight = 110;
        } elseif ($n >= 33 && $n <= 45) {
            $this->cohesion = 8000;
            $this->moist_unit_weight = 110;
            $this->saturated_unit_weight = 120;
        } else {
            $this->cohesion = 10000;
            $this->moist_unit_weight = 110;
            $this->saturated_unit_weight = 120;
        }

        $this->nc = ($this->cohesion == 0) ? 0 : 9;
    }

    /**
     * Set Nq value based on angle of internal friction
     */
    public function setNq()
    {
        $phi = $this->angle_of_internal_friction;

        if ($phi <= 0) {
            $this->nq = 0;
            return;
        } elseif ($phi >= 50) {
            $this->nq = 320;
        } elseif ($phi >= 45 && $phi <= 49.9) {
            $this->nq = 130 + (($phi - 45) / (50 - 45)) * (320 - 130);
        } elseif ($phi >= 40 && $phi <= 44.9) {
            $this->nq = 64 + (($phi - 40) / (45 - 40)) * (130 - 64);
        } elseif ($phi >= 35 && $phi <= 39.9) {
            $this->nq = 33 + (($phi - 35) / (40 - 35)) * (64 - 33);
        } elseif ($phi >= 30 && $phi <= 34.9) {
            $this->nq = 18 + (($phi - 30) / (35 - 30)) * (33 - 18);
        } elseif ($phi >= 25 && $phi <= 29.9) {
            $this->nq = 11 + (($phi - 25) / (30 - 25)) * (18 - 11);
        } elseif ($phi >= 20 && $phi <= 24.9) {
            $this->nq = 6.4 + (($phi - 20) / (25 - 20)) * (11 - 6.4);
        } elseif ($phi >= 15 && $phi <= 19.9) {
            $this->nq = 3.9 + (($phi - 15) / (20 - 15)) * (6.4 - 3.9);
        } elseif ($phi >= 10 && $phi <= 14.9) {
            $this->nq = 2.5 + (($phi - 10) / (15 - 10)) * (3.9 - 2.5);
        } elseif ($phi >= 5 && $phi <= 9.9) {
            $this->nq = 1.6 + (($phi - 5) / (10 - 5)) * (2.5 - 1.6);
        } elseif ($phi >= 0 && $phi <= 4.9) {
            $this->nq = 1 + (($phi - 0) / (5 - 0)) * (1.6 - 1);
        }
    }

    /**
     * Get the adhesion factor (alpha) based on cohesion.
     *
     * @return float
     */
    public function getAlphaAttribute(): float
    {
        // α method for clay
        if ($this->soilLayerType->name === 'Clay') {
            $cu = $this->cohesion * 144; // Convert cohesion from psi to psf
            if ($cu <= 25 * 144) { // 25 psi in psf
                return 1.0;
            } elseif ($cu <= 70 * 144) { // 70 psi in psf
                return 1.0 - ($cu - 25 * 144) / 100;
            } else {
                return 0.45;
            }
        }
        return 0; // Default for non-clay soils
    }

    /**
     * Get the lateral earth pressure coefficient (K).
     *
     * @return float
     */
    public function getKAttribute(): float
    {
        // K can be estimated as 1 - sin(φ) for sandy soils
        if ($this->soilLayerType->name === 'Sand') {
            $phi = (3.141592 / 180) * $this->angle_of_internal_friction; // Convert φ to radians
            return 1 - sin($phi);
        }
        return 0; // Default for non-sandy soils
    }

    /**
     * Get the interface friction angle (delta).
     *
     * @return float
     */
    public function getDeltaAttribute(): float
    {
        // δ is typically 0.7 * φ for steel piles in sand
        if ($this->soilLayerType->name === 'Sand') {
            $phi = (3.141592 / 180) * $this->angle_of_internal_friction; // Convert φ to radians
            return 0.7 * $phi;
        }
        return 0; // Default for non-sandy soils
    }
}