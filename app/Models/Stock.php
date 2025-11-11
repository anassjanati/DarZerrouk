<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['book_id', 'zone_id', 'sous_zone_id', 'sous_sous_zone_id', 'quantity'];

    public function book()          { return $this->belongsTo(Book::class); }
    public function zone()          { return $this->belongsTo(Zone::class); }
    public function sousZone()      { return $this->belongsTo(\App\Models\SousZone::class, 'sous_zone_id'); }
    public function sousSousZone()  { return $this->belongsTo(\App\Models\SousSousZone::class, 'sous_sous_zone_id'); }
}
