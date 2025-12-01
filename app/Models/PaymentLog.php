<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentLog extends Model
{
    use HasFactory;
    protected $table = 'payment_logs';

    protected $fillable = [
        'idempotency_key',
        'order_id',
        'status'
    ];
}
