<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use App\Models\ProductVariant;
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
    public $selectedVariant = null;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->firstOrFail();
        $this->title = $this->product->name . " - TokoOnline";
    }

    public function updatedSelectedVariant($value)
    {
        // Reset quantity when variant changes
        $this->quantity = 1;
    }

    public function increaseQty()
    {
        // Check stock availability
        $max_stock = $this->selectedVariant
            ? $this->getSelectedVariantStock()
            : $this->product->variants()->max('stock_quantity') ?? PHP_INT_MAX;

        if ($this->quantity < $max_stock) {
            $this->quantity++;
        }
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    private function getSelectedVariantStock()
    {
        // Safely get stock quantity from selected variant
        if (is_array($this->selectedVariant)) {
            return $this->selectedVariant['stock_quantity'] ?? 0;
        }

        if (is_string($this->selectedVariant)) {
            try {
                $variant = json_decode($this->selectedVariant, true);
                return $variant['stock_quantity'] ?? 0;
            } catch (\Exception $e) {
                return 0;
            }
        }

        if (is_object($this->selectedVariant)) {
            return $this->selectedVariant->stock_quantity ?? 0;
        }

        return 0;
    }

    private function processSelectedVariant()
    {
        // Safely process the selected variant
        if (is_string($this->selectedVariant)) {
            try {
                return json_decode($this->selectedVariant, true) ?: null;
            } catch (\Exception $e) {
                return null;
            }
        }

        return is_array($this->selectedVariant)
            ? $this->selectedVariant
            : ($this->selectedVariant ? (array) $this->selectedVariant : null);
    }

    public function addToCart()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to add items to your cart.');
        }

        // Validate variant selection if product has variants
        if ($this->product->variants()->exists() && !$this->selectedVariant) {
            $this->alert('error', 'Please select a product variant!', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
            return;
        }

        // Get current cart items
        $cart_items = CartManagement::getCartItemsFromCookie() ?? [];

        // Process selected variant
        $variant_data = $this->processSelectedVariant();

        // Prepare cart key (unique for product + variant combination)
        $variant_id = $variant_data['id'] ?? null;
        $cart_key = $variant_id
            ? $this->product->id . '-' . $variant_id
            : $this->product->id . '-default';

        // Check if exact product-variant combination exists
        $existing_item_key = null;
        foreach ($cart_items as $key => $item) {
            $item_variant_id = $item['variant_id'] ?? null;
            $item_key = $item_variant_id
                ? $item['product_id'] . '-' . $item_variant_id
                : $item['product_id'] . '-default';

            if ($item_key === $cart_key) {
                $existing_item_key = $key;
                break;
            }
        }

        // Determine price and image
        $unit_price = $variant_data
            ? ($variant_data['price'] ?? $this->product->price)
            : $this->product->price;

        $product_image = $variant_data && isset($variant_data['image'])
            ? 'products/' . basename($variant_data['image'])
            : ($this->product->images[0] ?? null);

        if ($existing_item_key !== null) {
            // If product-variant combination exists, update quantity
            $cart_items[$existing_item_key]['quantity'] += $this->quantity;
            $cart_items[$existing_item_key]['total_amount'] =
                $cart_items[$existing_item_key]['quantity'] * $cart_items[$existing_item_key]['unit_amount'];
        } else {
            // Add new item with full details
            $cart_items[] = [
                'product_id' => $this->product->id,
                'variant_id' => $variant_data['id'] ?? null,
                'variant_name' => $this->product->name,
                'variant_value' => $variant_data['value'] ?? null,
                'name' => $this->product->name . ($variant_data ? ' - ' . ($variant_data['value'] ?? '') : ''),
                'image' => $product_image,
                'quantity' => $this->quantity,
                'unit_amount' => $unit_price,
                'price' => $unit_price,
                'total_amount' => $unit_price * $this->quantity,
                'sku' => $variant_data['sku'] ?? $this->product->sku
            ];
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

        // Reset quantity after adding to cart
        $this->quantity = 1;
    }

    public function render()
    {
        return view('livewire.product-detail-page', ['product' => $this->product])
            ->layoutData(['title' => $this->title]);
    }
}
