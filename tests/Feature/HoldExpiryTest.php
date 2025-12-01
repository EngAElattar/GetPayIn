<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Hold;
use App\Models\Product;
use App\Enums\Hold\HoldStatus;
use App\Jobs\ReleaseExpiredHolds;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HoldExpiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_holds()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $hold = Hold::create([
            'product_id' => $product->id,
            'qty'        => 5,
            'unit_price' => $product->price,
            'amount'     => $product->price * 5,
            'status'     => HoldStatus::ACTIVE,
            'expires_at' => now()->subMinutes(2),
        ]);

        $product->decrement('stock', 5);

        (new ReleaseExpiredHolds)->handle(app('App\Services\Order\ProductStockService'));

        $product->refresh();
        $hold->refresh();

        $this->assertEquals(10, $product->stock);
        $this->assertEquals(HoldStatus::EXPIRED, $hold->status);
    }
}
