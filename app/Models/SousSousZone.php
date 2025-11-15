<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SousSousZone extends Model
{
    protected $fillable = ['sous_zone_id', 'name', 'code'];

    public function sousZone()
    {
        return $this->belongsTo(SousZone::class, 'sous_zone_id');
    }

    // Optional: get stocks in this sous-sous-zone
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'sous_sous_zone_id');
    }
}
