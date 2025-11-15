<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BonDeCommande extends Model
{
    protected $fillable = [
        'ref', 'supplier_id', 'date', 'status', 'comments', 'user_id'
    ];

    public function lines() { return $this->hasMany(BonDeCommandeLine::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function user() { return $this->belongsTo(User::class); }
}
