<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCartSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get the value attribute and cast it to the appropriate type
     */
    public function getValueAttribute($value)
    {
        // Try to decode as JSON, otherwise return as is
        $decoded = json_decode($value, true);
        return $decoded ?? $value;
    }

    /**
     * Set the value attribute, encoding arrays/objects as JSON
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value) || is_object($value)
            ? json_encode($value)
            : $value;
    }

    /**
     * Scope to get a setting by key
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Get a setting value by key
     */
    public static function getSetting(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function setSetting(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
