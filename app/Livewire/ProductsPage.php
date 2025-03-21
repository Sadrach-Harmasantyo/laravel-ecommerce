<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Products - TokoOnline')]

class ProductsPage extends Component
{
    use LivewireAlert;

    use WithPagination;

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

    // Method for adding the product in the cart

    // Find the addToCart method and update it:

    // ... existing code ...

    public function addToCart($product_id)
    {
        
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to add items to your cart.');
        }

        $total_count = CartManagement::addItemToCart($product_id);

        // Update both components
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        $this->dispatch('cart-updated');

        $this->alert('success', 'Product added to the cart successfully!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    // ... existing code ...

    public function render()
    {
        $products = Product::query()->where('is_active', 1);

        $brands = Brand::where('is_active', 1)->get(['id', 'name', 'slug']);

        $categories = Category::where('is_active', 1)->get(['id', 'name', 'slug']);

        if ($this->search) {
            $products->where('name', 'like', '%' . $this->search . '%');

            // Ambil ID dari brand yang cocok dengan pencarian
            $brandIds = Brand::where('name', 'like', '%' . $this->search . '%')->pluck('id')->toArray();
            if (!empty($brandIds)) {
                $products->orWhereIn('brand_id', $brandIds);
            }

            // Ambil ID dari kategori yang cocok dengan pencarian
            $categoryIds = Category::where('name', 'like', '%' . $this->search . '%')->pluck('id')->toArray();
            if (!empty($categoryIds)) {
                $products->orWhereIn('category_id', $categoryIds);
            }
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
            $products->whereBetween('price', [0, $this->price_range]);
            // $products->where('price', '<=', $this->price_range)
            //     ->when(!empty($this->selected_categories), function ($query) {
            //         return $query->whereIn('category_id', $this->selected_categories);
            //     })
            //     ->when(!empty($this->selected_brands), function ($query) {
            //         return $query->whereIn('brand_id', $this->selected_brands);
            //     })
            //     ->orderBy('price', 'asc')
            //     ->get();
        }

        if ($this->min_price || $this->max_price) {
            $products->whereBetween('price', [$this->min_price, $this->max_price]);
        }

        if ($this->sort == 'latest') {
            $products->latest();
        }

        if ($this->sort == 'price') {
            $products->orderBy('price');
        }

        return view('livewire.products-page', [
            'products' => $products->paginate(12),
            'brands' => $brands,
            'categories' => $categories
        ]);
    }
}
