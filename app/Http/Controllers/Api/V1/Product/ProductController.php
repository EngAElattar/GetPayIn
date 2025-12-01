<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Services\Product\ProductService;
use App\Http\Resources\PaginationResource;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\V1\Product\ProductResource;
use App\Http\Requests\Api\Product\ShowProductRequest;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function __construct(
        private readonly ProductService $productService
    ) {}
    public function index(Request $request)
    {
        $products = $this->productService->getData();
        return $this->responseWithMeta(
            data: [
                'data' => ProductResource::collection($products),
            ],
            meta: PaginationResource::make($products),
            message: 'تم عرض المنتجات بنجاح'
        );
    }

    public function show(ShowProductRequest $request)
    {
        $id = (int) $request->validated('id');
        $product = $this->productService->getProduct($id);

        if (!$product) {
            return $this->notFoundResponse($message = 'المنتج غير موجود');
        }
        return $this->responseWithMeta(
            data: [
                'data' => new ProductResource($product),
            ],
            meta: [],
            message: 'تم عرض المنتج بنجاح'
        );
    }
}
