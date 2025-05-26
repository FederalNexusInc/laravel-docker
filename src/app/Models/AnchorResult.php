<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnchorResult extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'required_allowable_pile_capacity',
        'applied_factor_of_safety',
        'helical_pile_diameter',
        'helix_configuration',
        'torque_correlation_factor',
        'allowable_frictional_resistance',
        'allowable_end_bearing_capacity',
        'allowable_pile_capacity',
        'approximate_pile_embedment_depth',
        'required_min_installation_torque',
        'tension_results',
        'compression_results',
        'calculation_steps',
    ];

    protected $casts = [
        'tension_results' => 'array',
        'compression_results' => 'array',
        'calculation_steps' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Initialize default values for the arrays if not provided
        if (!isset($this->attributes['tension_results'])) {
            $this->attributes['tension_results'] = [];
        }
        if (!isset($this->attributes['compression_results'])) {
            $this->attributes['compression_results'] = [];
        }
        if (!isset($this->attributes['calculation_steps'])) {
            $this->attributes['calculation_steps'] = [];
        }
    }
}
