<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name'        => 'Flash Sale Product',
            'price'       => 1000,
            'stock'       => 2000,
        ];
    }
}
