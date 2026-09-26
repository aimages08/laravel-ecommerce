<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;

class ShippingService
{
    /**
     * Calculate shipping fee for a given cart.
     * Cart format: [productId => ['price' => x, 'quantity' => y, 'name' => z]]
     */
    public function calculate(array $cart, float $subtotal): float
    {
        // Free shipping globally if subtotal >= threshold
        $freeOver = (float) setting('shipping_free_over', 0);
        if ($freeOver > 0 && $subtotal >= $freeOver) {
            return 0;
        }

        if (empty($cart)) {
            return 0;
        }

        $globalRate = (float) setting('shipping_flat_rate', 0);
        $maxShipping = 0;

        // Fetch products in one query
        $products = Product::whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        foreach ($cart as $productId => $item) {
            $product = $products[$productId] ?? null;
            if (!$product) continue;

            $fee = $this->feeForProduct($product, $item['quantity'], $globalRate);

            // MAX rule
            if ($fee > $maxShipping) {
                $maxShipping = $fee;
            }
        }

        return (float) $maxShipping;
    }

    /**
     * Fee for a single product line.
     */
    public function feeForProduct(Product $product, int $qty, float $globalRate = null): float
    {
        $globalRate = $globalRate ?? (float) setting('shipping_flat_rate', 0);

        switch ($product->shipping_type) {
            case 'free':
                return 0;

            case 'flat':
                return (float) ($product->shipping_fee ?? 0);

            case 'per_kg':
                $weight = (float) ($product->weight ?? 0);
                $rate   = (float) ($product->shipping_rate_per_kg ?? 0);
                return $weight * $rate * $qty;

            case 'global':
            default:
                return $globalRate;
        }
    }
}