<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Hold;
use App\Models\Order;
use App\Enums\Hold\HoldStatus;
use App\Enums\Order\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_order_from_hold()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $hold = Hold::create([
            'product_id' => $product->id,
            'qty'        => 4,
            'unit_price' => $product->price,
            'amount'     => $product->price * 4,
            'status'     => HoldStatus::ACTIVE->value,
            'expires_at' => now()->addMinutes(2),
        ]);

        $product->decrement('stock', 4);

        $response = $this->postJson('/api/v1/orders', [
            'hold_id' => $hold->id
        ]);

        $response->assertStatus(200);
        $payload = $response->json();

        $orderId = data_get($payload, 'data.data.id');

        $this->assertNotNull($orderId, 'Order id not found in response');

        $order = Order::find($orderId);

        $this->assertEquals($hold->id, $order->hold_id);
        $this->assertEquals(OrderStatus::PENDING, $order->status);

        $hold->refresh();
        $this->assertEquals(HoldStatus::USED, $hold->status);
    }

    public function test_fails_create_order_from_expired_hold()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $hold = Hold::create([
            'product_id' => $product->id,
            'qty'        => 4,
            'unit_price' => $product->price,
            'amount'     => $product->price * 4,
            'status'     => HoldStatus::ACTIVE->value,
            'expires_at' => now()->subMinutes(3),
        ]);

        $product->decrement('stock', 4);

        $response = $this->postJson('/api/v1/orders', [
            'hold_id' => $hold->id
        ]);

        $response->assertStatus(500);

        $this->assertDatabaseMissing('orders', [
            'hold_id' => $hold->id
        ]);

        $hold->refresh();
        $this->assertEquals(HoldStatus::ACTIVE, $hold->status);
    }


    public function test_does_not_allow_reusing_same_hold_twice()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $hold = Hold::create([
            'product_id' => $product->id,
            'qty'        => 4,
            'unit_price' => $product->price,
            'amount'     => $product->price * 4,
            'status'     => HoldStatus::ACTIVE->value,
            'expires_at' => now()->addMinutes(2),
        ]);

        $product->decrement('stock', 4);

        $first = $this->postJson('/api/v1/orders', [
            'hold_id' => $hold->id
        ]);

        $first->assertStatus(200);

        $hold->refresh();
        $this->assertEquals(HoldStatus::USED, $hold->status);

        $second = $this->postJson('/api/v1/orders', [
            'hold_id' => $hold->id
        ]);

        $second->assertStatus(500);

        $this->assertEquals(1, Order::where('hold_id', $hold->id)->count());
    }
}
