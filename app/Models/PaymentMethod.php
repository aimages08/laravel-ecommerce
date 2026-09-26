<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentMethod extends Model
{
        protected $fillable = [
        'payment_method_id', 'key', 'value', 'is_encrypted', 'field_type', 'sort_order',
    ];

    protected $casts = [
        'is_enabled'   => 'boolean',
        'is_test_mode' => 'boolean',
    ];

    public function settings()
    {
        return $this->hasMany(PaymentMethodSetting::class)->orderBy('sort_order');
    }

    /** Get a decrypted credential value by key */
    public function getSetting(string $key, $default = null)
    {
        $row = $this->settings()->where('key', $key)->first();
        if (!$row || $row->value === null) return $default;

        return $row->is_encrypted
            ? Crypt::decryptString($row->value)
            : $row->value;
    }

    /** Set a credential value (encrypts if requested) */
    public function setSetting(string $key, $value, bool $encrypt = false): void
    {
        $stored = $encrypt && $value !== null && $value !== ''
            ? Crypt::encryptString($value)
            : $value;

        $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'is_encrypted' => $encrypt]
        );
    }

    public function scopeEnabled($q)
    {
        return $q->where('is_enabled', true)->orderBy('sort_order');
    }
}