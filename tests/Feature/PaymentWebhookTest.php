<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Hold;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Enums\Hold\HoldStatus;
use App\Enums\Order\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_webhook_order()
    {
        $product = Product::factory()->create([
            'stock'       => 10,
            'price' => 1000,
        ]);

        $hold = Hold::create([
            'product_id' => $product->id,
            'qty'        => 3,
            'unit_price' => $product->price,
            'amount'     => $product->price * 3,
            'status'     => HoldStatus::USED->value,
            'expires_at' => now()->addMinutes(5),
        ]);

        $product->decrement('stock', 3);

        $order = Order::create([
            'hold_id'    => $hold->id,
            'unit_price' => $hold->unit_price,
            'amount'     => $hold->amount,
            'status'     => OrderStatus::PENDING->value,
        ]);

        $payload = [
            'idempotency_key' => 'evt_100',
            'order_id'        => $order->id,
            'status'          => 'success',
        ];

        $this->postJson('/api/v1/payments/webhook', $payload)
            ->assertStatus(200);

        $order->refresh();
        $product->refresh();
        $hold->refresh();

        $this->assertEquals(OrderStatus::PAID, $order->status);
        $this->assertNotNull($order->payment_reference);
        $this->assertEquals(7, $product->stock);
        $this->assertEquals(HoldStatus::USED, $hold->status);

        $this->postJson('/api/v1/payments/webhook', $payload)
            ->assertStatus(200);

        $this->assertEquals(1, PaymentLog::where('idempotency_key', 'evt_100')->count());
        $this->assertEquals(OrderStatus::PAID, $order->fresh()->status);
        $this->assertEquals(7, $product->fresh()->stock);
    }

    public function test_failed_webhook_order()
    {
        $product = Product::factory()->create([
            'stock'       => 10,
            'price' => 1000,
        ]);

        $hold = Hold::create([
            'product_id' => $product->id,
            'qty'        => 4,
            'unit_price' => $product->price,
            'amount'     => $product->price * 4,
            'status'     => HoldStatus::USED->value,
            'expires_at' => now()->addMinutes(5),
        ]);

        $product->decrement('stock', 4);

        $order = Order::create([
            'hold_id'    => $hold->id,
            'unit_price' => $hold->unit_price,
            'amount'     => $hold->amount,
            'status'     => OrderStatus::PENDING->value,
        ]);

        $payload = [
            'idempotency_key' => 'evt_fail_1',
            'order_id'        => $order->id,
            'status'          => 'failed',
        ];

        $this->postJson('/api/v1/payments/webhook', $payload)
            ->assertStatus(200);

        $order->refresh();
        $product->refresh();
        $hold->refresh();

        $this->assertEquals(OrderStatus::CANCELED, $order->status);

        $this->assertEquals(10, $product->stock);

        $this->assertEquals(HoldStatus::EXPIRED, $hold->status);

        $this->assertDatabaseHas('payment_logs', [
            'idempotency_key' => 'evt_fail_1',
            'status'          => 'failed',
        ]);
    }
}
