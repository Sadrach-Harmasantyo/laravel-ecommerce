<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Products - TokoBajuSepatu')]
class ProductsPage extends Component
{
    use LivewireAlert, WithPagination;

    #[Url]
    public $search = '';
    #[Url]
    public $selected_categories = [];
    #[Url]
    public $selected_brands = [];
    #[Url]
    public $featured = [];
    #[Url]
    public $on_sale = [];
    #[Url]
    public $price_range = 0;
    #[Url]
    public $min_price = 0;
    #[Url]
    public $max_price = 50000000;
    #[Url]
    public $sort = 'latest';

    public $selectedVariant = null;

    public function addToCart($product_id, $variant_id = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Log In untuk masukkan ke keranjang.');
        }

        $total_count = CartManagement::addItemToCart($product_id, $variant_id);

        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        $this->dispatch('cart-updated');

        $this->alert('success', 'Produk berhasil dimasukkan ke keranjang!!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        $products = Product::query()->where('is_active', 1);
        $brands = Brand::where('is_active', 1)->get(['id', 'name', 'slug']);
        $categories = Category::where('is_active', 1)->get(['id', 'name', 'slug']);

        if ($this->search) {
            $products->where('name', 'like', '%' . $this->search . '%')
                ->orWhereIn('brand_id', Brand::where('name', 'like', '%' . $this->search . '%')->pluck('id'))
                ->orWhereIn('category_id', Category::where('name', 'like', '%' . $this->search . '%')->pluck('id'));
        }

        if (!empty($this->selected_categories)) {
            $products->whereIn('category_id', $this->selected_categories);
        }

        if (!empty($this->selected_brands)) {
            $products->whereIn('brand_id', $this->selected_brands);
        }

        if ($this->featured) {
            $products->where('is_featured', 1);
        }

        if ($this->on_sale) {
            $products->where('on_sale', 1);
        }

        if ($this->price_range) {
            $products->where('price', '<=', $this->price_range);
        }

        if ($this->min_price || $this->max_price) {
            $products->whereBetween('price', [$this->min_price, $this->max_price]);
        }

        if ($this->sort == 'latest') {
            $products->latest();
        } elseif ($this->sort == 'price') {
            $products->orderBy('price');
        }

        return view('livewire.products-page', [
            'products' => $products->paginate(12),
            'brands' => $brands,
            'categories' => $categories
        ]);
    }
}
