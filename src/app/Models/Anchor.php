<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anchor extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'anchor_id';
    protected $fillable = [
        'project_id',
        'lead_shaft_od',
        'lead_shaft_length',
        'extension_shaft_od',
        'wall_thickness',
        'yield_strength',
        'tensile_strength',
        'empirical_torque_factor',
        'required_allowable_capacity',
        'anchor_type',
        'required_safety_factor',
        'anchor_declination_degree',
        'pile_head_position',
        'x1',
        'y1',
        'x2',
        'y2',
        'x3',
        'y3',
        'x4',
        'y4',
        'x5',
        'y5',
        'omit_shaft_resistance',
        'omit_helix_mechanical_strength_check',
        'omit_shaft_mechanical_strength_check',
        'field_notes',
        'soil_profile_id'
    ];

    /**
     * The project the anchore bleongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the helixes attached to this anchor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function helixes(): HasMany
    {
        return $this->hasMany(Helix::class, 'anchor_id', 'anchor_id');
    }

    /**
     * Get the soil profile associated with this anchor.
     */
    public function soilProfile(): BelongsTo
    {
        return $this->belongsTo(SoilProfile::class, 'soil_profile_id');
    }

    // Derived TitaValue from declination degree
    public function getTitaValueAttribute(): float
    {
        $tita = 0;
        if ($this->anchor_declination_degree != 0) {
            $tita = (3.141592 / 180) * $this->anchor_declination_degree;
        }
        return $tita;
    }

    // Get the size of the top helix
    public function getTopHelixSize(): float
    {
        $helixes = $this->helixes()->orderByDesc('helix_id')->get();

        if ($helixes->isEmpty()) {
            return 0;
        }

        $topHelix = $helixes->first();

        return $topHelix->size;
    }

    // Get the total helix spacing (for all helixes except the topmost)
    public function getHelixSpacing(): float
    {
        $helixes = $this->helixes()->orderBy('helix_id')->get();

        $totalLength = 0;
        $topPosition = $this->helixes->count();

        $filteredHelixes = $this->helixes
            ->filter(function($helix) use ($topPosition) {
                return $helix->helix_id <= $topPosition - 1;
            });

        foreach ($filteredHelixes as $helix) {
            if ($helix->position < $helixes->count()) {
                $totalLength += (3 * ($helix->size / 12));
            }
        }

        return $totalLength;
    }

    // Get helix spacing for a specific helix number
    public function getHelixSpacingForHelix(int $helixNumber): float
    {
        $helixes = $this->helixes()->where('helix_id', '<=', $helixNumber)->orderBy('helix_id')->get();
        $totalLength = 0;

        foreach ($helixes as $helix) {
            $totalLength += (3 * ($helix->size / 12));
        }

        return $totalLength;
    }

    // Get an array of all helix sizes, sorted in ascending order
    public function getHelixSizesArray(): array
    {
        $helixes = $this->helixes()->orderBy('helix_id')->get();

        if ($helixes->isEmpty()) {
            throw new \Exception("Helix missing! Please add Helix in the Anchor.");
        }

        $helixSizes = $helixes->pluck('size')->toArray();
        sort($helixSizes);

        return $helixSizes;
    }

    public function hasBackSlope(): bool
    {
        return ($this->x1 != 0 || $this->y1 != 0 || $this->x2 != 0 || $this->y2 != 0);
    }
}
