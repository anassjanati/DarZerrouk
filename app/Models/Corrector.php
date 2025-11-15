<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Corrector extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
