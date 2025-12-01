<?php

namespace App\Http\Resources\V1\Hold;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HoldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'qty' => $this->qty,
            'unit_price' => $this->unit_price,
            'amount' => $this->amount,
            'expires_at' => $this->expires_at,
        ];
    }
}
