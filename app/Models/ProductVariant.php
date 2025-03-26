<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'value',
        'sku',
        'price',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }
}