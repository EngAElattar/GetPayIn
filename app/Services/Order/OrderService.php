<?php

namespace App\Services\Order;

use App\Models\Hold;
use App\Models\Order;
use App\Enums\Hold\HoldStatus;
use App\Enums\Order\OrderStatus;
use Illuminate\Support\Facades\DB;
use App\Services\Order\ProductStockService;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly ProductStockService $productStockService
    ) {}


    public function createFromHold(int $holdId): Order
    {
        return DB::transaction(function () use ($holdId) {

            $hold = Hold::query()
                ->lockForUpdate()
                ->find($holdId);

            if (!$hold) {
                throw ValidationException::withMessages([
                    'hold_id' => 'This hold not found.',
                ]);
            }

            if ($hold->status !== HoldStatus::ACTIVE) {
                throw ValidationException::withMessages([
                    'hold_id' => 'This hold already used or expired.',
                ]);
            }

            if ($hold->expires_at->isPast()) {
                throw ValidationException::withMessages([
                    'hold_id' => 'This hold has expired.',
                ]);
            }

            $order = Order::create([
                'hold_id' => $hold->id,
                'unit_price' => $hold->unit_price,
                'amount' => $hold->amount,
                'status'  => OrderStatus::PENDING,
            ]);

            $hold->status = HoldStatus::USED;
            $hold->save();

            return $order;
        });
    }
}
