<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        $all = Setting::all_cached();
        return $all[$key] ?? $default;
    }
}