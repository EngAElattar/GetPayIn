<?php

namespace App\Services\Order;

use App\Models\Hold;
use App\Models\Product;
use App\Enums\Hold\HoldStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Enums\Stock\StockDirection;
use Illuminate\Support\Facades\Cache;
use App\Services\Order\ProductStockService;
use Illuminate\Validation\ValidationException;

class HoldService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly ProductStockService $productStockService
    ) {}

    public function getProductForUpdate(int $productId): Product
    {
        return Product::where('id', $productId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function store(array $data): Hold
    {
        [$hold, $productId] = DB::transaction(function () use ($data) {
            $product = $this->getProductForUpdate($data['product_id']);

            if ($product->stock < $data['qty']) {
                throw ValidationException::withMessages([
                    'qty' => 'Not enough stock available.',
                ]);
            }

            $product->stock -= $data['qty'];
            $product->save();

            $hold = Hold::create([
                'product_id' => $product->id,
                'qty'        => $data['qty'],
                'unit_price' => $product->price,
                'amount'     => $product->price * $data['qty'],
                'status'     => HoldStatus::ACTIVE->value,
                'expires_at' => now()->addMinutes(2),
            ]);

            $this->productStockService->record(
                productId: $product->id,
                direction: StockDirection::OUT->value,
                qty: $data['qty'],
                referenceId: $hold->id
            );

            return [$hold, $product->id];
        });

        return $hold;
    }
}
