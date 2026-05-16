<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    // =========================================
    // STATIC HELPERS — get/set dengan cache
    // =========================================

    /**
     * Ambil nilai setting by key.
     * Cache 1 jam agar tidak query setiap request.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => (bool) $setting->value,
                'integer' => (int) $setting->value,
                'json'    => json_decode($setting->value, true),
                default   => $setting->value,
            };
        });
    }

    /**
     * Set nilai setting by key.
     * Forget cache setelah update.
     */
    public static function set(string $key, mixed $value): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = is_array($value) ? json_encode($value) : $value;
        $setting->save();

        Cache::forget("setting:{$key}");
    }

    /**
     * Ambil semua settings dalam group tertentu.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->keyBy('key')
            ->map(fn($s) => $s->value)
            ->toArray();
    }

    // =========================================
    // SCOPES
    // =========================================

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
