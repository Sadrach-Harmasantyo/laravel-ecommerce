<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[Title('Profile - TokoOnline')]
class ProfilePage extends Component
{
    public function render()
    {
        // // Get all orders for the current authenticated user
        // $orders = Order::where('user_id', Auth::id())
        //     ->orderBy('created_at', 'desc')
        //     ->get();
            
        return view('livewire.profile-page');
    }
}
