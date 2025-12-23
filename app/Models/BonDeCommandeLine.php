<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonDeCommandeLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'bon_de_commande_id',
        'book_id',
        'quantity',
        'selling_price',
        'cost_price',
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'selling_price' => 'decimal:2',
        'cost_price'    => 'decimal:2',
    ];

    public function bonDeCommande(): BelongsTo
    {
        return $this->belongsTo(BonDeCommande::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Sous‑total vente (selling_price × quantity).
     */
    public function getSubtotalAttribute(): float
    {
        return (float) ($this->selling_price * $this->quantity);
    }

    /**
     * Sous‑total coût (cost_price × quantity).
     */
    public function getCostSubtotalAttribute(): float
    {
        return (float) ($this->cost_price * $this->quantity);
    }
}
