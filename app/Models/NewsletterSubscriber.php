<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'token', 'is_subscribed', 'unsubscribed_at'];

    protected $casts = [
        'is_subscribed' => 'boolean',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($s) {
            $s->token = $s->token ?: Str::random(40);
        });
    }
}