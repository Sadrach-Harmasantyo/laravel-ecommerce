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
    public $hasVariants = false;
    public $stockQuantity = 0;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->firstOrFail();
        $this->title = $this->product->name . " - TokoBajuSepatu";

        // Check if product has variants
        $this->hasVariants = $this->product->variants()->exists();

        if ($this->hasVariants) {
            // Set first variant as default
            $firstVariant = $this->product->variants()->first();
            if ($firstVariant) {
                $this->selectedVariant = json_encode([
                    "id" => $firstVariant->id,
                    "value" => $firstVariant->value,
                    "price" => $firstVariant->price,
                    "image" => $firstVariant->image
                        ? str_replace("http://127.0.0.1:8000/storage/", "", $firstVariant->image)
                        : ($this->product->images[0] ?? null),
                    "stock_quantity" => $firstVariant->stock_quantity,
                    "sku" => $firstVariant->sku
                ]);
                $this->stockQuantity = $firstVariant->stock_quantity;
            }
        } else {
            // If no variants, use product stock directly
            $this->stockQuantity = $this->product->stock_quantity ?? 0;
        }
    }

    public function updatedSelectedVariant($value)
    {
        // Reset quantity when variant changes
        $this->quantity = 1;

        // Update stock quantity based on selected variant
        if ($value) {
            try {
                $variant = json_decode($value, true);
                $this->stockQuantity = $variant['stock_quantity'] ?? 0;
            } catch (\Exception $e) {
                $this->stockQuantity = 0;
            }
        }
    }

    public function increaseQty()
    {
        // Check stock availability
        if ($this->quantity < $this->stockQuantity) {
            $this->quantity++;
        }
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
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
            ->with('error', 'Log In untuk masukkan ke keranjang.');
    }

    // Validate variant selection if product has variants
    if ($this->hasVariants && !$this->selectedVariant) {
        $this->alert('error', 'Pilih varian produk!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
        return;
    }

    // Get current cart items
    $cart_items = CartManagement::getCartItemsFromCookie() ?? [];

    // Process selected variant
    $variant_data = $this->hasVariants ? $this->processSelectedVariant() : null;

    // Prepare cart key (unique for product + variant combination)
    $variant_id = $variant_data['id'] ?? null;
    $cart_key = $variant_id
        ? $this->product->id . '-' . $variant_id
        : $this->product->id . '-default';

    // Check if exact product-variant combination exists and get current quantity in cart
    $existing_item_key = null;
    $quantity_in_cart = 0;
    foreach ($cart_items as $key => $item) {
        $item_variant_id = $item['variant_id'] ?? null;
        $item_key = $item_variant_id
            ? $item['product_id'] . '-' . $item_variant_id
            : $item['product_id'] . '-default';

        if ($item_key === $cart_key) {
            $existing_item_key = $key;
            $quantity_in_cart = $item['quantity'];
            break;
        }
    }

    // Calculate total quantity (existing in cart + new addition)
    $total_requested_quantity = $quantity_in_cart + $this->quantity;
    
    // Flag to track if we adjusted the quantity
    $quantity_adjusted = false;

    // Check if total quantity exceeds available stock
    if ($total_requested_quantity > $this->stockQuantity) {
        $available_to_add = $this->stockQuantity - $quantity_in_cart;

        if ($available_to_add <= 0) {
            $this->alert('error', 'Produk ini sudah mencapai batas stok tersedia di keranjang Anda!', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
            return;
        } else {
            // Let the user know we're adjusting the quantity
            $this->quantity = $available_to_add;
            $quantity_adjusted = true;
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
            'variant_name' => $this->hasVariants ? ($variant_data['name'] ?? null) : null,
            'variant_value' => $this->hasVariants ? ($variant_data['value'] ?? null) : null,
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

    // Show appropriate alert message based on whether quantity was adjusted
    if ($quantity_adjusted) {
        $this->alert('warning', "Jumlah disesuaikan menjadi {$this->quantity} sesuai stok tersedia!", [
            'position' => 'top-end',
            'timer' => 4000,
            'toast' => true,
        ]);
    } else {
        $this->alert('success', 'Produk berhasil dimasukkan ke keranjang!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    // Reset quantity after adding to cart
    $this->quantity = 1;
}

    public function render()
    {
        return view('livewire.product-detail-page', [
            'product' => $this->product,
            'stockQuantity' => $this->stockQuantity
        ])->layoutData(['title' => $this->title]);
    }
}