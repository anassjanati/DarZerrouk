<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'author_id',
        'translator_id',        // NEW
        'editor_id',            // NEW
        'publisher_id',
        'zone_id',              // NEW
        'isbn',
        'barcode',
        'title',
        'title_ar',
        'subtitle',
        'description',
        'language',
        'edition',
        'edition_year',         // NEW
        'edition_number',       // NEW
        'publication_year',
        'pages',
        'format',
        'condition',
        'cover_image',
        'cost_price',
        'price_1',              // NEW (Prix normal)
        'price_2',              // NEW (Prix après remise)
        'selling_price',        // Keep for compatibility
        'wholesale_price',
        'discount_percentage',
        'stock_quantity',
        'reorder_level',
        'min_stock_level',      // NEW
        'shelf_location',
        'weight',
        'notes',                // NEW
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'price_1' => 'decimal:2',
        'price_2' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
        'reorder_level' => 'integer',
        'min_stock_level' => 'integer',
        'pages' => 'integer',
        'publication_year' => 'integer',
        'edition_year' => 'integer',
    ];

    /**
     * Relationships
     */
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function translator(): BelongsTo
    {
        return $this->belongsTo(Translator::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(Editor::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    /**
     * Accessors & Helpers
     */

    /**
     * Get final price after discount (using price_2 if set, otherwise calculate from price_1)
     */
    public function getFinalPriceAttribute(): float
    {
        // If price_2 exists, use it (discounted price)
        if ($this->price_2 > 0) {
            return $this->price_2;
        }

        // Otherwise calculate from price_1 with discount_percentage
        if ($this->price_1 > 0) {
            $discount = $this->price_1 * ($this->discount_percentage / 100);
            return $this->price_1 - $discount;
        }

        // Fallback to selling_price for backward compatibility
        $discount = $this->selling_price * ($this->discount_percentage / 100);
        return $this->selling_price - $discount;
    }

    /**
     * Get the primary selling price (prefer price_2, then price_1, then selling_price)
     */
    public function getActivePriceAttribute(): float
    {
        if ($this->price_2 > 0) {
            return $this->price_2;
        }
        if ($this->price_1 > 0) {
            return $this->price_1;
        }
        return $this->selling_price;
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if ($this->price_1 > 0 && $this->price_2 > 0) {
            return $this->price_1 - $this->price_2;
        }
        return $this->selling_price * ($this->discount_percentage / 100);
    }
    /**
 * Get discount percentage (recalculated from price_1 and price_2)
 */
public function getDiscountPercentageCalculatedAttribute(): float
{
    if ($this->price_1 > 0 && $this->price_2 > 0 && $this->price_2 < $this->price_1) {
        return (($this->price_1 - $this->price_2) / $this->price_1) * 100;
    }
    return $this->discount_percentage ?? 0;
}

/**
 * Get unit price based on quantity (for bulk discounts)
 */
public function getUnitPrice($quantity = 1): float
{
    // If buying 10+ books, use wholesale price (price_2)
    if ($quantity >= 10 && $this->price_2 > 0 && $this->price_2 < $this->price_1) {
        return $this->price_2;
    }
    
    // Otherwise use retail price (price_1)
    return $this->price_1 > 0 ? $this->price_1 : $this->selling_price;
}


    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        $threshold = $this->min_stock_level ?: $this->reorder_level;
        return $this->stock_quantity <= $threshold && $this->stock_quantity > 0;
    }

    /**
     * Check if out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Get full display name with author
     */
    public function getFullTitleAttribute(): string
    {
        $title = $this->title;
        if ($this->author) {
            $title .= ' - ' . $this->author->name;
        }
        return $title;
    }

    /**
     * Scopes
     */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock_quantity', '>', 0)
                     ->where(function($q) {
                         $q->whereColumn('stock_quantity', '<=', 'min_stock_level')
                           ->orWhereColumn('stock_quantity', '<=', 'reorder_level');
                     });
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('title_ar', 'like', "%{$term}%")
              ->orWhere('isbn', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%")
              ->orWhereHas('author', function($q) use ($term) {
                  $q->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('publisher', function($q) use ($term) {
                  $q->where('name', 'like', "%{$term}%");
              });
        });
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function scopeByPublisher($query, $publisherId)
    {
        return $query->where('publisher_id', $publisherId);
    }

    public function scopeByZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    public function scopeInPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price_2', [$min, $max])
                     ->orWhereBetween('price_1', [$min, $max])
                     ->orWhereBetween('selling_price', [$min, $max]);
    }
}
