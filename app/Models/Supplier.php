<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BonDeCommande;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'tax_id',
        'payment_terms',
        'credit_limit',
        'current_balance',
        'total_purchases',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit'     => 'decimal:2',
        'current_balance'  => 'decimal:2',
        'total_purchases'  => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    /**
     * Bons de commande de ce fournisseur.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(BonDeCommande::class, 'supplier_id', 'id');
    }

    /**
     * Scope to get active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate unique supplier code.
     */
    public static function generateCode(): string
    {
        $prefix = 'SUP';
        $lastSupplier = self::latest('id')->first();
        $number = $lastSupplier ? $lastSupplier->id + 1 : 1;

        return $prefix.'-'.str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
