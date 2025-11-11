<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = ['book_id', 'from_zone_id', 'to_zone_id', 'quantity', 'user_id'];

    public function book()    { return $this->belongsTo(Book::class); }
    public function fromZone(){ return $this->belongsTo(Zone::class, 'from_zone_id'); }
    public function toZone()  { return $this->belongsTo(Zone::class, 'to_zone_id'); }
    public function user()    { return $this->belongsTo(User::class); }
}
