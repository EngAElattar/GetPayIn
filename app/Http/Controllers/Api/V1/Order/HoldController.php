<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Services\Order\HoldService;
use App\Http\Requests\Api\Hold\HoldRequest;
use App\Http\Resources\V1\Hold\HoldResource;
use App\Http\Controllers\Api\V1\BaseController;

class HoldController extends BaseController
{
    public function __construct(
        private readonly HoldService $holdService
    ) {}

    public function store(HoldRequest $request)
    {
        $data = $request->validated();
        try {

            $hold = $this->holdService->store($data);
            return $this->responseWithMeta(
                data: [
                    'data' => new HoldResource($hold),
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
