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
    'publisher_id', 'barcode', 'title', 'title_ar', 'subtitle', 'designation',
    'description', 'language', 'edition', 'edition_year', 'edition_number',
    'publication_year', 'pages', 'format', 'condition', 'cover_image',
    'cost_price', 'price_1', 'price_2', 'selling_price', 'wholesale_price',
    'discount_percentage', 'min_stock_level', 'reorder_level', 'weight', 'notes',
    'is_featured', 'is_active'
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
        'min_stock_level' => 'integer',
        'reorder_level' => 'integer',
        'pages' => 'integer',
        'publication_year' => 'integer',
        'edition_year' => 'integer',
    ];

    public function author(): BelongsTo      { return $this->belongsTo(Author::class); }
    public function translator(): BelongsTo  { return $this->belongsTo(Translator::class); }
    public function corrector(): BelongsTo   { return $this->belongsTo(Corrector::class); }
    public function publisher(): BelongsTo   { return $this->belongsTo(Publisher::class); }
    public function category(): BelongsTo    { return $this->belongsTo(Category::class); }
    public function stocks(): HasMany        { return $this->hasMany(Stock::class); }

    // Stock quantity helpers
    public function getTotalStockAttribute() {
        return $this->stocks->sum('quantity');
    }
    public function getIsLowStockAttribute() {
        $min = $this->min_stock_level ?: $this->reorder_level ?: 1;
        return $this->total_stock > 0 && $this->total_stock <= $min;
    }
    public function getIsOutOfStockAttribute() {
        return $this->total_stock <= 0;
    }
    public function scopeOutOfStock($query)
{
    // For MySQL: Books whose total stock quantity is zero (using subquery)
    return $query->whereRaw('COALESCE((SELECT SUM(quantity) FROM stocks WHERE stocks.book_id = books.id), 0) <= 0');
}
public function scopeLowStock($query)
{
    // Books whose total stock quantity is above 0 and below the min_stock_level (or reorder_level)
    return $query->whereRaw(
        'COALESCE((SELECT SUM(quantity) FROM stocks WHERE stocks.book_id = books.id), 0) > 0
         AND COALESCE((SELECT SUM(quantity) FROM stocks WHERE stocks.book_id = books.id), 0) <= IFNULL(books.min_stock_level, books.reorder_level)'
    );
}
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

public function getDesignationAttribute()
{
    $details = [];
    $details[] = $this->title;
    if ($this->author && $this->author->name) $details[] = $this->author->name;
    if ($this->publisher && $this->publisher->name) $details[] = $this->publisher->name;
    if ($this->category && $this->category->name) $details[] = $this->category->name;
    if ($this->edition_year) $details[] = $this->edition_year;
    if ($this->language) $details[] = $this->language;
    // Add more fields as needed

    return implode(' - ', array_filter($details));
}
}
