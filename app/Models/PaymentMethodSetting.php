<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodSetting extends Model
{
    protected $fillable = [
    'payment_method_id', 'key', 'value', 'is_encrypted', 'field_type', 'sort_order',
];

    protected $casts = ['is_encrypted' => 'boolean'];

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}