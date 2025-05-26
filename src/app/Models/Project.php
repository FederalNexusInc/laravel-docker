<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use PHPUnit\TextUI\Help;

class Project extends Model
{

    use HasFactory;
    
    protected $primaryKey = 'project_id';
    protected $fillable = [
        'project_name',
        'project_number',
        'run_id',
        'soil_reporter',
        'soil_report_number',
        'soil_report_date',
        'pile_type',
        'boring_number',
        'boring_log_date',
        'termination_depth',
        'project_address',
        'project_city',
        'project_state',
        'project_zip_code',
        'remarks',
        'created_by'
    ];

    protected $dates = [
        'soil_report_date',
        'boring_log_date',
    ];

    /**
     * The user who created the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the soil profiles for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function soilProfile(): HasOne
    {
        return $this->hasOne(SoilProfile::class, 'project_id', 'project_id');
    }

    /**
     * Get the soil layers for the project through the soil profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function soilLayers(): HasManyThrough
    {
        return $this->hasManyThrough(SoilLayer::class, SoilProfile::class, 'project_id', 'soil_profile_id');
    }

    /**
     * Get the anchors for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function anchors(): HasMany
    {
        return $this->hasMany(Anchor::class, 'project_id', 'project_id');
    }

    /**
     * Get the specialist for the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function projectSpecialist(): HasOne
    {
        return $this->hasOne(ProjectSpecialist::class, 'project_id');
    }

    /**
     * Get the helixes for the project through the anchors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function helixes(): HasManyThrough
    {
        return $this->hasManyThrough(Helix::class, Anchor::class, 'project_id', 'anchor_id');
    }
}
