<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantSelector extends Component
{
    public $product;
    public $selectedVariant;
    public $price;
    public $quantity = 1;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->selectedVariant = $product->variants->first(); // Pilih variant pertama sebagai default
        $this->updatePrice();
    }

    public function updatePrice()
    {
        $this->price = $this->selectedVariant ? $this->selectedVariant->price : $this->product->price;
    }

    public function updatedSelectedVariant($variantId)
    {
        $this->selectedVariant = ProductVariant::find($variantId);
        $this->updatePrice();
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
        if (!$this->selectedVariant) {
            return session()->flash('error', 'Please select a variant.');
        }

        $cart = session()->get('cart', []);

        $cartItemKey = 'variant_' . $this->selectedVariant->id;

        if (isset($cart[$cartItemKey])) {
            $cart[$cartItemKey]['quantity'] += $this->quantity;
        } else {
            $cart[$cartItemKey] = [
                'variant_id' => $this->selectedVariant->id,
                'name' => $this->selectedVariant->name,
                'value' => $this->selectedVariant->value,
                'price' => $this->selectedVariant->price,
                'quantity' => $this->quantity,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');

        session()->flash('success', 'Product added to cart successfully!');
    }

    public function render()
    {
        return view('livewire.product-variant-selector');
    }
}
