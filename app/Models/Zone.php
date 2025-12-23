<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function sousZones(): HasMany
    {
        return $this->hasMany(SousZone::class);
    }

    public function scopeLibrairie($query)
    {
        return $query->where('type', 'librairie');
    }

    public function scopeMagasinage($query)
    {
        return $query->where('type', 'magasinage');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->stocks()->sum('quantity');
    }
}
