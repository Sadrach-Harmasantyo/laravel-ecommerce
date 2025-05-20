<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[Title('My Orders - TokoBajuSepatu')]
class MyOrdersPage extends Component
{

    public function confirmDelivery($orderId)
    {
        // Find the order and ensure it belongs to the current user
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'shipped')
            ->first();

        if ($order) {
            // Update the order status to delivered
            $order->status = 'delivered';
            $order->save();

            // Optional: Add a flash message to confirm the action
            session()->flash('success', 'Order successfully marked as delivered.');
        }

        // Refresh the page to show updated status
        return redirect()->route('my-orders');
    }

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
