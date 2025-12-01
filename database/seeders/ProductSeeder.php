<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\Stock\StockDirection;
use App\Enums\Stock\StockReferenceType;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Flash Sale Product 1', 'price' => 10000, 'stock' => 5000],
        ];

        DB::transaction(function () use ($items) {

            foreach ($items as $item) {

                $product = Product::create([
                    'name'          => $item['name'],
                    'price'   => $item['price'],
                    'stock'   => 0,
                ]);

                ProductStock::create([
                    'product_id'     => $product->id,
                    'direction'      => StockDirection::IN->value,
                    'qty'            => $item['stock'],
                    'reference_type' => StockReferenceType::ADMIN->value,
                    'reference_id'   => null,
                    'note'           => 'Initial stock seeding',
                ]);

                $product->stock += $item['stock'];
                $product->save();
            }
        });
    }
}
