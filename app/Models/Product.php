<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sku',
        'images',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
        'is_featured',
        'in_stock',
        'on_sale',
        'category_id',
        'brand_id',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'in_stock' => 'boolean',
        'on_sale' => 'boolean',
        'images' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    public function updateStockStatus(): void
    {
        $this->in_stock = $this->variants()->where('stock_quantity', '>', 0)->exists();
        $this->save();
    }

    public function getAvailableVariants()
    {
        return $this->variants()->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();
    }

    public function getLowestVariantPrice()
    {
        $lowestVariantPrice = $this->variants()
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->min('price');

        return $lowestVariantPrice ?? $this->price;
    }

    public function scopeWithAvailableVariants($query)
    {
        return $query->with(['variants' => function ($query) {
            $query->where('is_active', true)
                  ->where('stock_quantity', '>', 0);
        }]);
    }
}