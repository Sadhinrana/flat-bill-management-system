<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'building_id',
    ];

    /**
     * Get the building this category belongs to
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get all bills in this category
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
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
