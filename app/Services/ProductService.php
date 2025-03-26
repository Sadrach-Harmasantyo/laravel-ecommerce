<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getFeaturedProducts(int $limit = 8): Collection
    {
        return Product::where('is_active', true)
            ->where('is_featured', true)
            ->withAvailableVariants()
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $product->lowest_price = $product->getLowestVariantPrice();
                return $product;
            });
    }

    public function getOnSaleProducts(int $limit = 8): Collection
    {
        return Product::where('is_active', true)
            ->where('on_sale', true)
            ->withAvailableVariants()
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $product->lowest_price = $product->getLowestVariantPrice();
                return $product;
            });
    }
}