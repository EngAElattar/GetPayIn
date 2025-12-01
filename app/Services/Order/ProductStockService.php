<?php

namespace App\Services\Order;

use App\Models\ProductStock;
use App\Enums\Stock\StockReferenceType;

class ProductStockService
{
    public function record(int $productId, string $direction, int $qty, $referenceType = null, ?int $referenceId = null)
    {
        return ProductStock::create([
            'product_id'   => $productId,
            'direction'         => $direction,
            'qty'       => $qty,
            'reference_type' => StockReferenceType::HOLD->value,
            'reference_id' => $referenceId,
        ]);
    }
}
