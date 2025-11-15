<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BonDeCommandeLine extends Model
{
    protected $fillable = [
        'bon_de_commande_id', 'book_id', 'quantity', 'cost_price'
    ];

    public function bonDeCommande() { return $this->belongsTo(BonDeCommande::class); }
    public function book() { return $this->belongsTo(Book::class); }
}
