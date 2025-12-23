<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * User who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic subject of the activity.
     */
    public function subject(): ?MorphTo
    {
        if ($this->model && $this->model_id) {
            return $this->morphTo('subject', 'model', 'model_id');
        }

        return null;
    }

    /**
     * Scopes.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Static helper to log activity.
     */
    public static function log(
        string $action,
        string $description,
        ?string $model = null,
        ?int $modelId = null,
        $properties = null
    ): self {
        return static::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model'      => $model,
            'model_id'   => $modelId,
            'description'=> $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
