<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'sku', 'price', 'sale_price', 'stock', 'image', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values',
            'variant_id',
            'attribute_value_id'
        );
    }

    /**
     * Effective price (variant override or product fallback).
     */
    public function getEffectivePriceAttribute()
    {
        if ($this->sale_price) return $this->sale_price;
        if ($this->price) return $this->price;
        return $this->product->sale_price ?? $this->product->price;
    }

    /**
     * Label like "S / Red"
     */
    public function getLabelAttribute(): string
    {
        return $this->attributeValues->pluck('value')->implode(' / ');
    }
}