<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HoldTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_create_hold(): void
    {
        $product = Product::factory()->create([
            'stock' => 10
        ]);

        $responses = collect();

        for ($i = 0; $i < 5; $i++) {
            $responses->push(
                $this->postJson('/api/v1/holds', [
                    'product_id' => $product->id,
                    'qty'        => 3
                ])
            );
        }

        $successCount = $responses->filter(fn($res) => $res->status() === 200)->count();

        $this->assertTrue($successCount <= 3);

        $product->refresh();
        $this->assertEquals(10 - ($successCount * 3), $product->stock);
    }
}
