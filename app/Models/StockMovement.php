<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'from_zone_id',
        'to_zone_id',
        'from_sous_zone_id',
        'to_sous_zone_id',
        'from_sous_sous_zone_id',
        'to_sous_sous_zone_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function book(): BelongsTo { return $this->belongsTo(Book::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function fromZone(): BelongsTo { return $this->belongsTo(Zone::class, 'from_zone_id'); }
    public function toZone(): BelongsTo { return $this->belongsTo(Zone::class, 'to_zone_id'); }

    public function fromSousZone(): BelongsTo { return $this->belongsTo(SousZone::class, 'from_sous_zone_id'); }
    public function toSousZone(): BelongsTo { return $this->belongsTo(SousZone::class, 'to_sous_zone_id'); }

    public function fromSousSousZone(): BelongsTo { return $this->belongsTo(SousSousZone::class, 'from_sous_sous_zone_id'); }
    public function toSousSousZone(): BelongsTo { return $this->belongsTo(SousSousZone::class, 'to_sous_sous_zone_id'); }

    public function getFromLocationAttribute(): string
    {
        if ($this->fromSousSousZone) return $this->fromSousSousZone->code;
        if ($this->fromSousZone) return $this->fromSousZone->code;
        if ($this->fromZone) return $this->fromZone->code;
        return 'N/A';
    }

    public function getToLocationAttribute(): string
    {
        if ($this->toSousSousZone) return $this->toSousSousZone->code;
        if ($this->toSousZone) return $this->toSousZone->code;
        if ($this->toZone) return $this->toZone->code;
        return 'N/A';
    }
}
