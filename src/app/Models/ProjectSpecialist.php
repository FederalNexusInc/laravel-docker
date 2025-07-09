<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectSpecialist extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_specialists_id';
    protected $fillable = [
        'name',
        'specialist_email',
        'company_name',
        'address',
        'city',
        'state',
        'zip',
        'remarks',
    ];

    /**
     * The project the specialist is assigned to.
     *
     * @return HasMany<Project>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_specialists_id', 'project_specialist_id');
    }
}
