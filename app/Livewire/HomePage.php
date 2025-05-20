<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Home - TokoBajuSepatu')]

class HomePage extends Component
{
    public function render()
    {
        $brands = Brand::where('is_active', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        
        // Get featured products
        $featuredProducts = Product::where('is_active', 1)
            ->where('is_featured', 1)
            ->latest()
            ->take(8)
            ->get();
            
        // Get on sale products
        $onSaleProducts = Product::where('is_active', 1)
            ->where('on_sale', 1)
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.home-page', compact('brands', 'categories', 'featuredProducts', 'onSaleProducts'));
    }
}
