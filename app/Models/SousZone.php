<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SousZone extends Model
{
    protected $fillable = ['zone_id', 'name', 'code'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function sousSousZones()
    {
        return $this->hasMany(SousSousZone::class, 'sous_zone_id');
    }

    // Optional: get stocks in this sous zone
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'sous_zone_id');
    }
}
