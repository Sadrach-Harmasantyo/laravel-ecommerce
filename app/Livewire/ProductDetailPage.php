<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProductDetailPage extends Component
{
    use LivewireAlert;

    public $product;
    public $title;
    public $quantity = 1;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->firstOrFail();
        $this->title = $this->product->name . " - TokoOnline";
    }

    public function increaseQty()
    {
        $this->quantity++;
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to add items to your cart.');
        }

        // Use the product ID from the loaded product
        $product_id = $this->product->id;
        
        // Get current cart items
        $cart_items = CartManagement::getCartItemsFromCookie();
        
        // Check if product already exists in cart
        $existing_item_key = null;
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $existing_item_key = $key;
                break;
            }
        }
        
        if ($existing_item_key !== null) {
            // If product exists, add the quantity
            $cart_items[$existing_item_key]['quantity'] += $this->quantity;
            $cart_items[$existing_item_key]['total_amount'] = 
                $cart_items[$existing_item_key]['quantity'] * $cart_items[$existing_item_key]['unit_amount'];
        } else {
            // If product doesn't exist, add it with the selected quantity
            $product = Product::find($product_id);
            if ($product) {
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->images[0] ?? null,
                    'quantity' => $this->quantity,
                    'unit_amount' => $product->price,
                    'price' => $product->price,
                    'total_amount' => $product->price * $this->quantity
                ];
            }
        }
        
        // Save updated cart items
        CartManagement::addCartItemsToCookie($cart_items);
        $total_count = count($cart_items);

        // Update both components
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        $this->dispatch('cart-updated');

        $this->alert('success', 'Product added to the cart successfully!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.product-detail-page', ['product' => $this->product])
            ->layoutData(['title' => $this->title]);
    }
}
