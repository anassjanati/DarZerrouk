<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publisher extends Model
{
    protected $fillable = [
        'name',
        'country',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
