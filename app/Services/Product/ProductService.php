<?php

namespace App\Services\Product;

use App\Models\Product;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function getData()
    {
        return Product::query()
            ->orderBy('id', 'desc')
            ->paginate(9);
    }

    public function getProduct(int $id)
    {
        return Product::query()
            ->select([
                'id',
                'name',
                'price',
                'stock',
            ])
            ->find($id);
    }
}
