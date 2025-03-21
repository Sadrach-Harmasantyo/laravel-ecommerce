<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CartManagement;

class CartController extends Controller
{
    public function showCart()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);

        return view('livewire.cart-page', compact('cart_items', 'grand_total'));
    }
}
