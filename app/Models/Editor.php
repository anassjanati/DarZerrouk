<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Editor extends Model
{
    protected $fillable = [
        'name',
        'specialization',
        'is_active',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
