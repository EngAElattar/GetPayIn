<?php

namespace App\Jobs;

use App\Models\Hold;
use App\Models\Product;
use App\Enums\Hold\HoldStatus;
use Illuminate\Support\Facades\DB;
use App\Enums\Stock\StockDirection;
use Illuminate\Support\Facades\Cache;
use App\Enums\Stock\StockReferenceType;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Order\ProductStockService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReleaseExpiredHolds implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(ProductStockService $productStockService): void
    {
        Hold::where('status', HoldStatus::ACTIVE->value)
            ->where('expires_at', '<=', now())
            ->chunkById(100, function ($holds) use ($productStockService) {
                foreach ($holds as $hold) {
                    $this->releaseSingleHold($hold, $productStockService);
                }
            });
    }

    protected function releaseSingleHold(Hold $hold, ProductStockService $productStockService): void
    {
        DB::transaction(function () use ($hold, $productStockService) {

            $hold = Hold::where('id', $hold->id)
                ->lockForUpdate()
                ->first();

            if (! $hold) return;
            if ($hold->status !== HoldStatus::ACTIVE || $hold->expires_at->isFuture()) return;

            $product = Product::where('id', $hold->product_id)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                $hold->status = HoldStatus::EXPIRED;
                $hold->save();
                return;
            }

            $product->stock += $hold->qty;
            $product->save();

            $productStockService->record(
                productId: $product->id,
                direction: StockDirection::IN->value,
                qty: $hold->qty,
                referenceType: StockReferenceType::HOLD->value,
                referenceId: $hold->id,
            );

            $hold->status = HoldStatus::EXPIRED->value;
            $hold->save();
        });
    }
}
