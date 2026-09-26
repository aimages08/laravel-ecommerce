<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'meta_title', 'meta_description',
        'show_in_footer', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'show_in_footer' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeFooter($q)
    {
        return $q->where('show_in_footer', true)->where('is_active', true)->orderBy('sort_order');
    }
}