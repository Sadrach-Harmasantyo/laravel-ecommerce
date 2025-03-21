<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[Title('My Orders - TokoOnline')]
class MyOrdersPage extends Component
{
    public function render()
    {
        // Get all orders for the current authenticated user
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('livewire.my-orders-page', [
            'orders' => $orders
        ]);
    }
}
