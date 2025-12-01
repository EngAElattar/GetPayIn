<?php

namespace App\Models;

use App\Enums\Hold\HoldStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hold extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'qty',
        'unit_price',
        'amount',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'status' => HoldStatus::class,
        'expires_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
