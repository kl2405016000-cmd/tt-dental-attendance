<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getVal($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setVal($key, $value)
    {
        $setting = self::updateOrCreate(['key' => $key], ['value' => $value]);

        if ($key === 'workplace_lat') {
            self::updateOrCreate(['key' => 'location_1_lat'], ['value' => $value]);
        } elseif ($key === 'workplace_lng') {
            self::updateOrCreate(['key' => 'location_1_lng'], ['value' => $value]);
        } elseif ($key === 'workplace_radius') {
            self::updateOrCreate(['key' => 'location_1_radius'], ['value' => $value]);
        } elseif ($key === 'location_1_lat') {
            self::updateOrCreate(['key' => 'workplace_lat'], ['value' => $value]);
        } elseif ($key === 'location_1_lng') {
            self::updateOrCreate(['key' => 'workplace_lng'], ['value' => $value]);
        } elseif ($key === 'location_1_radius') {
            self::updateOrCreate(['key' => 'workplace_radius'], ['value' => $value]);
        }

        return $setting;
    }
}
