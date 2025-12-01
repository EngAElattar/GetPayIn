<?php

namespace App\Models;

use App\Enums\Order\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'hold_id',
        'status',
        'unit_price',
        'amount',
        'payment_reference',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function hold()
    {
        return $this->belongsTo(Hold::class);
    }
}
