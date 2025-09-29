<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'postal_code',
        'owner_id',
    ];

    /**
     * Get the owner of the building
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all flats in this building
     */
    public function flats(): HasMany
    {
        return $this->hasMany(Flat::class);
    }

    /**
     * Get all tenants in this building
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Get all bill categories for this building
     */
    public function billCategories(): HasMany
    {
        return $this->hasMany(BillCategory::class);
    }

    /**
     * Get all bills for this building
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Scope to filter by building owner
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }
}
