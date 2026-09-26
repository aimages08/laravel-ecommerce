<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id', 'name', 'phone', 'email',
        'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}