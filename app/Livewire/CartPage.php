<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

#[Title('Cart - TokoOnline')]

class CartPage extends Component
{
    use LivewireAlert;

    public $cartItems = [];
    public $grandTotal = 0;
    public $taxAmount = 0;
    public $finalTotal = 0;
    public $subtotal = 0;
    public $shippingCost = 0;
    public $total = 0;

    public function mount()
    {
        // Redirect to login if not authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to view your cart.');
        }

        $this->loadCartItems();
    }

    #[On('cart-updated')]
    public function loadCartItems()
    {
        $this->cartItems = CartManagement::getCartItemsFromCookie();

        // Ensure each cart item has the necessary keys
        foreach ($this->cartItems as $key => $item) {
            // If product_id exists but name doesn't, fetch the product and add its name
            if (isset($item['product_id']) && !isset($item['name'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $this->cartItems[$key]['name'] = $product->name;
                } else {
                    // If product doesn't exist, provide a fallback name
                    $this->cartItems[$key]['name'] = 'Unknown Product';
                }
            }
            
            // Ensure price key exists (map from unit_amount if needed)
            if (!isset($item['price']) && isset($item['unit_amount'])) {
                $this->cartItems[$key]['price'] = $item['unit_amount'];
            } elseif (!isset($item['price'])) {
                // If neither price nor unit_amount exists, fetch from database
                if (isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $this->cartItems[$key]['price'] = $product->price;
                    } else {
                        // Default fallback price
                        $this->cartItems[$key]['price'] = 0;
                    }
                } else {
                    // Default fallback price
                    $this->cartItems[$key]['price'] = 0;
                }
            }
            
            // Ensure quantity exists
            if (!isset($item['quantity'])) {
                $this->cartItems[$key]['quantity'] = 1;
            }
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->grandTotal = CartManagement::calculateGrandTotal($this->cartItems);
        $this->taxAmount = $this->grandTotal * 0.1; // 10% tax
        $this->shippingCost = 20000; // You can set this based on your business logic
        $this->subtotal = $this->grandTotal;
        $this->total = $this->grandTotal + $this->taxAmount + $this->shippingCost;
        $this->finalTotal = $this->total;
    }

    public function incrementItem($productId)
    {
        CartManagement::incrementQuantityToCartItem($productId);
        $this->loadCartItems();
        $this->updateNavbarCartCount();

        $this->alert('success', 'Cart updated!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function decrementItem($productId)
    {
        CartManagement::decrementQuantityToCartItem($productId);
        $this->loadCartItems();
        $this->updateNavbarCartCount();

        $this->alert('success', 'Cart updated!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function removeItem($productId)
    {
        CartManagement::removeCartItems($productId);
        $this->loadCartItems();
        $this->updateNavbarCartCount();

        $this->alert('success', 'Item removed from cart!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    private function updateNavbarCartCount()
    {
        // First dispatch to Navbar directly
        $this->dispatch('update-cart-count', total_count: count($this->cartItems))->to(Navbar::class);

        // Then broadcast to all components
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}
