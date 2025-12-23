<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonDeCommande extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref',
        'supplier_id',
        'date',
        'status',
        'comments',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Lignes du bon de commande.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(BonDeCommandeLine::class);
    }

    /**
     * Fournisseur lié.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Créateur du bon de commande.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Total TTC basé sur selling_price × quantity.
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->lines->sum(function (BonDeCommandeLine $line) {
            return $line->selling_price * $line->quantity;
        });
    }

    /**
     * Total coût (cost_price × quantity) pour analyse de marge.
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->lines->sum(function (BonDeCommandeLine $line) {
            return $line->cost_price * $line->quantity;
        });
    }
}
