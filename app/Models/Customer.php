<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_tier_id',
        'code',
        'name',
        'email',
        'phone',
        'whatsapp',
        'address',
        'city',
        'date_of_birth',
        'gender',
        'total_points',
        'total_purchases',
        'last_purchase_date',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_purchase_date' => 'date',
        'total_points' => 'integer',
        'total_purchases' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the membership tier
     */
    public function membershipTier(): BelongsTo
    {
        return $this->belongsTo(MembershipTier::class);
    }

    /**
     * Get all sales for this customer
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get all loyalty points transactions
     */
    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Scope to get active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate unique customer code
     */
    public static function generateCode(): string
    {
        $prefix = 'CUST';
        $lastCustomer = self::latest('id')->first();
        $number = $lastCustomer ? $lastCustomer->id + 1 : 1;
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
