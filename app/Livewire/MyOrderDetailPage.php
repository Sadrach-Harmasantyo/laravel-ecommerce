<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[Title('My Order Detail - TokoOnline')]
class MyOrderDetailPage extends Component
{
    public $orderId;
    public $order;
    
    public function mount($id)
    {
        $this->orderId = $id;
        
        // Get the order with its relationships
        $this->order = Order::with(['items.product', 'address', 'user'])
            ->where('id', $this->orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }
    
    public function render()
    {
        return view('livewire.my-order-detail-page');
    }
}
