<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectSpecialist extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_specialists_id';
    protected $fillable = [
        'project_id',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
