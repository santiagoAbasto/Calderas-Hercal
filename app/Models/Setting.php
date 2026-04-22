<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value !== null && $setting->value !== ''
            ? $setting->value
            : $default;
    }
}
