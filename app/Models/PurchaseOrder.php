<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'user_id',
        'order_date',
        'expected_date',
        'received_date',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'paid_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Get the supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who created the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all purchase order items
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Generate unique PO number
     */
    public static function generatePONumber(): string
    {
        $prefix = 'PO';
        $date = date('Ymd');
        $lastPO = self::whereDate('created_at', today())->latest('id')->first();
        $number = $lastPO ? (int)substr($lastPO->po_number, -3) + 1 : 1;
        return $prefix . '-' . $date . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Scope to get pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get received orders
     */
    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }
}
