<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryTransaction;

class InventoryService
{
    /**
     * Adjust stock for a Product OR ProductVariant.
     */
    public function adjust($model, int $delta, string $type = 'adjustment', ?string $note = null, ?string $reference = null): void
    {
        if (!$model) return;

        $before = (int) $model->stock;
        $after  = max(0, $before + $delta);

        $model->update(['stock' => $after]);

        // Log against the parent product (works for both)
        $productId = $model instanceof ProductVariant
            ? $model->product_id
            : $model->id;

        InventoryTransaction::create([
            'product_id' => $productId,
            'type'       => $type,
            'quantity'   => $delta,
            'before_qty' => $before,
            'after_qty'  => $after,
            'reference'  => $reference,
            'note'       => $note,
            'created_by' => auth()->id(),
        ]);
    }

    public function setStock($model, int $newQty, ?string $note = null): void
    {
        if (!$model) return;
        $delta = $newQty - (int) $model->stock;
        $this->adjust($model, $delta, 'adjustment', $note);
    }

    public function deductForOrder(Product $product, int $qty, string $orderNumber): void
    {
        $this->adjust($product, -$qty, 'sale', "Order {$orderNumber}", $orderNumber);
    }
}