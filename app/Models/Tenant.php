<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact',
        'email',
        'building_id',
        'flat_id',
    ];

    /**
     * Get the building this tenant belongs to
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the flat this tenant is assigned to
     */
    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }

    /**
     * Scope to filter by building
     */
    public function scopeForBuilding($query, $buildingId)
    {
        return $query->where('building_id', $buildingId);
    }

    /**
     * Scope to filter by building owner
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->whereHas('building', function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        });
    }
}
