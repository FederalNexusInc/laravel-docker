<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Helix extends Model
{
    use HasFactory;

    protected $primaryKey = 'helix_id';
    protected $table = 'helixes';

    protected $fillable = [
        'anchor_id',
        'description',
        'size',
        'thickness',
        'rating',
        'helix_count',
    ];

    /**
     * The Anchor the helix belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anchor(): BelongsTo
    {
        return $this->belongsTo(Anchor::class, 'anchor_id', 'anchor_id');
    }
}
