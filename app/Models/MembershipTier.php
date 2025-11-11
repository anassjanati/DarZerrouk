<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'discount_percentage',
        'points_multiplier',
        'min_purchase_amount',
        'color',
        'benefits',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'points_multiplier' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
    ];

    /**
     * Get all customers in this tier
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
