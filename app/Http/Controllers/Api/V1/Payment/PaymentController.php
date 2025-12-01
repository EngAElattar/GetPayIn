<?php

namespace App\Http\Controllers\Api\V1\Payment;


use App\Services\Payment\PaymentService;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\Payment\PaymentRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentController extends BaseController
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {}
    public function handle(PaymentRequest $request)
    {
        $data = $request->validated();

        try {
            $this->paymentService->handleWebhook(
                idempotencyKey: $data['idempotency_key'],
                orderId: $data['order_id'],
                status: $data['status'],
            );

            return response()->json([
                'message' => 'تم معالجة الدفع بنجاح.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'حدث خطأ غير متوقع.'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'حدث خطأ غير متوقع.'
            ], 500);
        }
    }
}
