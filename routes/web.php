<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\BrandsPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\SuccessPage;
use App\Livewire\ProfilePage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', HomePage::class)->name('index');
Route::get('/products', ProductsPage::class)->name('all-products');
Route::get('/product/{slug}', ProductDetailPage::class)->name('product-detail'); // This is the correct route name
Route::get('/cart', CartPage::class)->name('cart');
Route::get('/categories', CategoriesPage::class)->name('product-categories');
Route::get('/brands', BrandsPage::class)->name('product-brands');
Route::get('/about-us', App\Livewire\AboutUsPage::class)->name('about-us');
Route::get('/blog', App\Livewire\BlogsPage::class)->name('blog');
Route::get('/blog/{blog:slug}', App\Livewire\BlogDetailPage::class)->name('blog.show');

// Authentication routes
Route::get('/login', LoginPage::class)->name('login');
Route::get('/register', RegisterPage::class)->name('register');
Route::get('/forgot-password', ForgotPasswordPage::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');

Route::post('/logout', function() {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('index');
})->name('logout');

// Protected routes (require authentication)
Route::middleware(['auth.custom'])->group(function () {
    Route::get('/checkout', CheckoutPage::class)->name('checkout');
    Route::get('/success', SuccessPage::class)->name('success');
    Route::get('/cancel', CancelPage::class)->name('cancel');
    Route::get('/my-orders', MyOrdersPage::class)->name('my-orders');
    // Add this route if it doesn't exist already
    Route::get('/my-orders/{id}', \App\Livewire\MyOrderDetailPage::class)->name('my-order-detail')->middleware('auth');
    Route::get('/profile', ProfilePage::class)->name('profile')->middleware('auth');
});

