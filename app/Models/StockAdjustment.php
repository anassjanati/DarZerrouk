<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'type',
        'quantity',
        'reason',
        'notes',
        'adjustment_date',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'quantity' => 'integer',
    ];

    /**
     * Get the book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the user who made the adjustment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
