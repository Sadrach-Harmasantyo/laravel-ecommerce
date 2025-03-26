<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class CartManagement
{
    static public function addItemToCart($product_id, $variant_id = null)
    {
        if (!Auth::check()) {
            return false;
        }

        $cart_items = self::getCartItemsFromCookie();

        // Unique key for each product-variant combination
        $cart_key = $variant_id 
            ? $product_id . '-' . $variant_id 
            : $product_id . '-default';

        // Check if item already exists with exact product and variant
        $existing_item_key = null;
        foreach ($cart_items as $key => $item) {
            $item_key = ($item['variant_id'] 
                ? $item['product_id'] . '-' . $item['variant_id'] 
                : $item['product_id'] . '-default');
            
            if ($item_key === $cart_key) {
                $existing_item_key = $key;
                break;
            }
        }

        if ($existing_item_key !== null) {
            // Increment quantity for existing item
            $cart_items[$existing_item_key]['quantity']++;
            $cart_items[$existing_item_key]['total_amount'] = 
                $cart_items[$existing_item_key]['quantity'] * 
                $cart_items[$existing_item_key]['unit_amount'];
        } else {
            // Add new item
            $product = Product::findOrFail($product_id);
            $variant = $variant_id ? ProductVariant::findOrFail($variant_id) : null;

            // Check stock availability
            if ($variant) {
                if (!$variant->is_active || $variant->stock_quantity <= 0) {
                    return false; // Cannot add inactive or out of stock variant
                }
            } else {
                // Check if product has variants and is out of stock
                if ($product->variants()->exists() && !$product->variants()->where('stock_quantity', '>', 0)->exists()) {
                    return false;
                }
            }

            $cart_items[] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'variant_name' => $variant?->name,
                'variant_value' => $variant?->value,
                'name' => $product->name . ($variant ? ' - ' . $variant->name : ''),
                'image' => $product->images[0] ?? null,
                'quantity' => 1,
                'unit_amount' => $variant?->price ?? $product->price,
                'total_amount' => $variant?->price ?? $product->price,
                'sku' => $variant?->sku ?? $product->sku
            ];
        }

        self::addCartItemsToCookie($cart_items);
        return count($cart_items);
    }

    static public function removeCartItems($product_id, $variant_id = null)
    {
        $cart_items = self::getCartItemsFromCookie();

        $cart_key = $variant_id 
            ? $product_id . '-' . $variant_id 
            : $product_id . '-default';

            foreach ($cart_items as $key => $item) {
                $item_variant_id = $item['variant_id'] ?? null;
                $item_key = $item_variant_id 
                    ? $item['product_id'] . '-' . $item_variant_id 
                    : $item['product_id'] . '-default';
                
                if ($item_key === $cart_key) {
                    unset($cart_items[$key]);
                }
            }

        self::addCartItemsToCookie(array_values($cart_items));

        return $cart_items;
    }

    static public function incrementQuantityToCartItem($product_id, $variant_id = null)
    {
        $cart_items = self::getCartItemsFromCookie();

        $cart_key = $variant_id 
            ? $product_id . '-' . $variant_id 
            : $product_id . '-default';

            foreach ($cart_items as $key => $item) {
                // Use null coalescing to handle cases where variant_id might be missing
                $item_variant_id = $item['variant_id'] ?? null;
                $item_key = $item_variant_id 
                    ? $item['product_id'] . '-' . $item_variant_id 
                    : $item['product_id'] . '-default';
                
                if ($item_key === $cart_key) {
                    // Check stock availability before incrementing
                    $product = Product::find($product_id);
                    $variant = $variant_id ? ProductVariant::find($variant_id) : null;
    
                    $max_stock = $variant 
                        ? $variant->stock_quantity 
                        : $product->variants()->max('stock_quantity') ?? PHP_INT_MAX;
    
                    if ($item['quantity'] < $max_stock) {
                        $cart_items[$key]['quantity']++;
                        $cart_items[$key]['total_amount'] = 
                            $cart_items[$key]['quantity'] * 
                            $cart_items[$key]['unit_amount'];
                    }
                }
            }

        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    static public function decrementQuantityToCartItem($product_id, $variant_id = null)
    {
        $cart_items = self::getCartItemsFromCookie();

        $cart_key = $variant_id 
            ? $product_id . '-' . $variant_id 
            : $product_id . '-default';

            foreach ($cart_items as $key => $item) {
                // Use null coalescing to handle cases where variant_id might be missing
                $item_variant_id = $item['variant_id'] ?? null;
                $item_key = $item_variant_id 
                    ? $item['product_id'] . '-' . $item_variant_id 
                    : $item['product_id'] . '-default';
                
                if ($item_key === $cart_key) {
                    if ($cart_items[$key]['quantity'] > 1) {
                        $cart_items[$key]['quantity']--;
                        $cart_items[$key]['total_amount'] = 
                            $cart_items[$key]['quantity'] * 
                            $cart_items[$key]['unit_amount'];
                    }
                }
            }

        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    static public function calculateGrandTotal($items)
    {
        return array_sum(array_column($items, 'total_amount'));
    }

    static public function addCartItemsToCookie($cart_items)
    {
        Cookie::queue('cart_items', json_encode(array_values($cart_items)), 60 * 24 * 30);
    }

    static public function getCartItemsFromCookie()
    {
        $cart_items = json_decode(Cookie::get('cart_items'), true) ?? [];
        return $cart_items;
    }

    static public function clearCartItems()
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }
}