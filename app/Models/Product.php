<?php

namespace App\Models;

use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'stock',
    ];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function getCalculatedStockAttribute(): int
    {
        $in  = $this->stockMovements()->where('direction', 'in')->sum('qty');
        $out = $this->stockMovements()->where('direction', 'out')->sum('qty');

        return $in - $out;
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->total_stock - $this->reserved_stock - $this->sold_stock);
    }
}
