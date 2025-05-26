<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoilLayerType extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'soil_layer_type_id';
    protected $fillable = [
        'name',
        'description',
        'default_cohesion',
        'default_coefficient_of_adhesion',
        'default_angle_of_internal_friction',
        'default_coefficient_of_external_friction',
        'default_moist_unit_weight',
        'default_saturated_unit_weight',
        'default_nc',
        'default_nq',
    ];
}
