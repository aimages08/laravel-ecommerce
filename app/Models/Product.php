<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'sku', 'barcode',
        'description', 'price', 'sale_price', 'cost_price', 'tax_rate',
        'stock', 'low_stock_threshold',
        'shipping_type', 'shipping_fee', 'shipping_rate_per_kg',
        'weight', 'length', 'width', 'height',
        'image', 'is_featured', 'is_new', 'is_digital', 'is_active', 'status',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'is_new'      => 'boolean',
        'is_digital'  => 'boolean',
        'low_stock_alert_enabled' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function primaryImageOrFirst()
    {
        return $this->hasOne(ProductImage::class)
            ->where('type', 'image')
            ->orderByDesc('is_primary')
            ->orderBy('sort_order');
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $img = $this->primaryImage;
        if (!$img) return null;
        return $this->resolveImageUrl($img->path);
    }

    public function resolveImageUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        return asset('storage/' . $path);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->latest();
    }


    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class)->latest();
    }


    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productAttributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes');
    }

    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }


}