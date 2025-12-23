<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'sale_date',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'change_amount',
        'points_earned',
        'points_redeemed',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'sale_date'        => 'datetime',
        'subtotal'         => 'decimal:2',
        'discount_value'   => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'tax_percentage'   => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'change_amount'    => 'decimal:2',
        'points_earned'    => 'integer',
        'points_redeemed'  => 'integer',
    ];

    /**
     * Client (colonne customer_id).
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'customer_id');
    }

    /**
     * Utilisateur (caissier).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lignes de vente (détail des articles).
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Paiements liés à la vente.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Transactions de points de fidélité.
     */
    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Génère un numéro de facture unique du type INV-YYYYMMDD-001.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix   = 'INV';
        $date     = date('Ymd');
        $lastSale = self::whereDate('created_at', today())->latest('id')->first();
        $number   = $lastSale
            ? (int) substr($lastSale->invoice_number, -3) + 1
            : 1;

        return $prefix . '-' . $date . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Scope : ventes du jour.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    /**
     * Scope : ventes complétées.
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }
}
