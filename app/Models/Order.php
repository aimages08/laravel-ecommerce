<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'subtotal', 'shipping', 'total',
        'status', 'payment_method', 'payment_status', 'notes',
        'tracking_number', 'carrier', 'shipped_at', 'delivered_at', 'cancelled_at',
        'coupon_id', 'discount',
    ];

    protected $casts = [
        'shipped_at'   => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusSteps(): array
    {
        $steps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];

        $currentIndex = array_search($this->status, $steps);

        // For cancelled/returned, no progress
        if (in_array($this->status, ['cancelled', 'returned'])) {
            $currentIndex = -1;
        }

        return array_map(function ($step, $index) use ($currentIndex) {
            return [
                'name'    => $step,
                'done'    => $index <= $currentIndex,
                'current' => $index === $currentIndex,
            ];
        }, $steps, array_keys($steps));
    }


    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}