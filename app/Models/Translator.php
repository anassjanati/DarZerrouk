<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Translator extends Model
{
    protected $fillable = [
        'name',
        'languages',
        'is_active',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
