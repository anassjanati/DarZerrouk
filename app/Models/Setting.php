<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'boolean' => (bool) $setting->value,
            'number' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set setting value
     */
    public static function set(string $key, $value): void
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = is_array($value) ? json_encode($value) : $value;
        $setting->save();
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
