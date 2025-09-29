<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flat extends Model
{
    use HasFactory;

    protected $fillable = [
        'flat_number',
        'owner_name',
        'owner_contact',
        'owner_email',
        'building_id',
    ];

    /**
     * Get the building this flat belongs to
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get all tenants in this flat
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Get all bills for this flat
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
