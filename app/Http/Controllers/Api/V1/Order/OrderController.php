<?php

namespace App\Http\Controllers\Api\V1\Order;


use App\Services\Order\OrderService;
use App\Http\Requests\Api\Order\OrderRequest;
use App\Http\Resources\V1\Order\OrderResource;
use App\Http\Controllers\Api\V1\BaseController;

class OrderController extends BaseController
{
    public function __construct(
        private readonly OrderService $orderService,

    ) {}

    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        try {
            $order = $this->orderService->createFromHold($request->hold_id);

            return $this->responseWithMeta(
                data: [
                    'data' => new OrderResource($order),
                ],
                meta: [],
                message: 'تم انشاء الطلب بنجاح'
            );
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
