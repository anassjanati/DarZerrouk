<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'author_id', 'translator_id', 'corrector_id',
        'publisher_id',
        'barcode',
        'title', 'title_ar', 'subtitle',
        'description', 'language',
        'edition', 'edition_year', 'edition_number', 'publication_year',
        'pages', 'format', 'condition', 'cover_image',
        // pricing (keep price_1 as main retail, price_2 as promo if you need)
        'cost_price', 'retail_price', 'wholesale_price',
        'discount_percentage',
        'min_stock_level', 'reorder_level',
        'weight', 'notes',
        'is_featured', 'is_active',
    ];

    protected $casts = [
        'cost_price'          => 'decimal:2',
        'retail_price'       => 'decimal:2',
        'wholesale_price'     => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'weight'              => 'decimal:2',
        'is_featured'         => 'boolean',
        'is_active'           => 'boolean',
        'min_stock_level'     => 'integer',
        'reorder_level'       => 'integer',
        'pages'               => 'integer',
        'publication_year'    => 'integer',
        'edition_year'        => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function author(): BelongsTo     { return $this->belongsTo(Author::class); }
    public function translator(): BelongsTo { return $this->belongsTo(Translator::class); }
    public function corrector(): BelongsTo  { return $this->belongsTo(Corrector::class); }
    public function publisher(): BelongsTo  { return $this->belongsTo(Publisher::class); }
    public function category(): BelongsTo   { return $this->belongsTo(Category::class); }
    public function stocks(): HasMany       { return $this->hasMany(Stock::class); }

    /*
    |--------------------------------------------------------------------------
    | Stock helpers
    |--------------------------------------------------------------------------
    */

    public function getTotalStockAttribute()
    {
        return $this->stocks->sum('quantity');
    }

    public function getIsLowStockAttribute()
    {
        $min = $this->min_stock_level ?: $this->reorder_level ?: 1;
        return $this->total_stock > 0 && $this->total_stock <= $min;
    }

    public function getIsOutOfStockAttribute()
    {
        return $this->total_stock <= 0;
    }

    public function scopeOutOfStock($query)
    {
        return $query->whereRaw(
            'COALESCE((SELECT SUM(quantity) FROM stocks WHERE stocks.book_id = books.id), 0) <= 0'
        );
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw(
            'COALESCE((SELECT SUM(quantity) FROM stocks WHERE stocks.book_id = books.id), 0) > 0
             AND COALESCE((SELECT SUM(quantity) FROM stocks WHERE stocks.book_id = books.id), 0)
                 <= IFNULL(books.min_stock_level, books.reorder_level)'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function ($query) {
            $query->where('is_active', true);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Display helpers (for fast, consistent UI)
    |--------------------------------------------------------------------------
    */

    // Main display title (Arabic first if exists)
    public function getDisplayTitleAttribute()
    {
        return $this->title_ar ?: $this->title;
    }

    // Designation = title + meta for listing/search preview
    public function getDesignationAttribute()
    {
        $details = [];

        if ($this->title)                    $details[] = $this->title;
        if ($this->author && $this->author->name)       $details[] = $this->author->name;
        if ($this->publisher && $this->publisher->name) $details[] = $this->publisher->name;
        if ($this->category && $this->category->name)   $details[] = $this->category->name;
        if ($this->edition_year)             $details[] = $this->edition_year;
        if ($this->language)                 $details[] = $this->language;

        return implode(' - ', array_filter($details));
    }

    // Normal selling price for caisse (use price_1 as retail)
    public function getRetailPriceAttribute()
{
    return (float) $this->attributes['retail_price'];
}


    // Price after discount (used in show view)
    public function getPriceAfterDiscountAttribute()
    {
        $price = $this->retail_price;
        $discount = $this->discount_percentage ?? 0;

        return $price * (1 - $discount / 100);
    }
}
