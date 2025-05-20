<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[Title('My Order Detail - TokoBajuSepatu')]
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

            // Redirect with success message
            session()->flash('success', 'Pesanan berhasil dikonfirmasi diterima.');
            return redirect()->route('my-order-detail', ['id' => $orderId]);
        }

        // If order not found or cannot be updated
        session()->flash('error', 'Gagal mengkonfirmasi pesanan.');
        return redirect()->route('my-order-detail', ['id' => $orderId]);
    }

    public function render()
    {
        return view('livewire.my-order-detail-page');
    }
}
