<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'amount',
        'due_amount',
        'status',
        'notes',
        'flat_id',
        'bill_category_id',
        'building_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'due_amount' => 'float'
    ];

    /**
     * Get the flat this bill belongs to
     */
    public function flat(): BelongsTo
    {
        return $this->belongsTo(Flat::class);
    }

    /**
     * Get the bill category
     */
    public function billCategory(): BelongsTo
    {
        return $this->belongsTo(BillCategory::class);
    }

    /**
     * Get the building this bill belongs to
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
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

    /**
     * Scope to filter by flat
     */
    public function scopeForFlat($query, $flatId)
    {
        return $query->where('flat_id', $flatId);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by month
     */
    public function scopeForMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Check if bill is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if bill is unpaid
     */
    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    /**
     * Mark bill as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'due_amount' => 0.00
        ]);
    }

    /**
     * Mark bill as unpaid
     */
    public function markAsUnpaid(): void
    {
        $this->update([
            'status' => 'unpaid',
            'due_amount' => $this->amount
        ]);
    }
}
