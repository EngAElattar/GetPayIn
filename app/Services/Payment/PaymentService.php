<?php

namespace App\Services\Payment;

use App\Models\Hold;
use App\Models\Order;
use App\Models\Product;
use App\Models\PaymentLog;
use App\Enums\Hold\HoldStatus;
use App\Enums\Order\OrderStatus;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function handleWebhook(string $idempotencyKey, int $orderId, string $status): void
    {
        DB::transaction(function () use ($idempotencyKey, $orderId, $status) {

            $existingLog = PaymentLog::where('idempotency_key', $idempotencyKey)->first();
            if ($existingLog) {
                if ((int) $existingLog->order_id === (int) $orderId && $existingLog->status === $status) {
                    return;
                }

                throw new \Exception('تم معالجة هذا الطلب سابقاً.');
            }

            $paymentLog = PaymentLog::create([
                'idempotency_key' => $idempotencyKey,
                'order_id'        => $orderId,
                'status'          => $status
            ]);

            $order = Order::where('id', $orderId)
                ->lockForUpdate()
                ->firstOrFail();

            $hold = Hold::where('id', $order->hold_id)
                ->lockForUpdate()
                ->first();


            if ($status === 'success') {
                if ($order->status === OrderStatus::PAID) {
                    throw new \Exception('تم دفع هذا الطلب مسبقاً.');
                }

                if ($order->status !== OrderStatus::PENDING) {
                    throw new \Exception('لا يمكن دفع هذا الطلب لأن حالته الحالية لا تسمح بذلك.');
                }

                $order->status = OrderStatus::PAID;
                $order->payment_reference = $paymentLog->id;
                $order->save();
                return;
            }

            if ($status === 'failed') {

                if ($order->status === OrderStatus::PAID) {
                    throw new \Exception('عملية الدفع فشلت.');
                }

                $order->status = OrderStatus::CANCELED;
                $order->save();

                if ($hold && $hold->status === HoldStatus::USED) {

                    $product = Product::where('id', $hold->product_id)
                        ->lockForUpdate()
                        ->first();

                    if ($product) {
                        $product->stock += $hold->qty;
                        $product->save();
                    }

                    $hold->status = HoldStatus::EXPIRED;
                    $hold->save();
                }
            }
        });
    }
}
