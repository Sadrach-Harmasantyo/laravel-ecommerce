<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

#[Title('Success - TokoBajuSepatu')]
class SuccessPage extends Component
{
    public $orderInfo = [];
    public $customerInfo = [];
    public $cartItems = [];
    public $grandTotal = 0;
    public $taxAmount = 0;
    public $shippingCost = 0;
    public $finalTotal = 0;
    public $orderNumber = 'N/A';
    public $orderDate = '';
    public $paymentMethod = 'N/A';

    public function mount()
    {
        // Get order information from session
        $this->orderInfo = Session::get('order_info', []);
        $this->customerInfo = Session::get('customer_info', []);
        $this->cartItems = Session::get('order_items', []);
        
        // Set financial information
        $this->grandTotal = Session::get('grand_total', 0);
        $this->taxAmount = Session::get('tax_amount', 0);
        $this->shippingCost = Session::get('shipping_cost', 0);
        $this->finalTotal = Session::get('final_total', 0);
        
        // Set order details
        $this->orderNumber = Session::get('order_number', 'N/A');
        $this->orderDate = Session::get('order_date', now()->format('d-m-Y'));
        $this->paymentMethod = Session::get('payment_method', 'N/A');
        
        // If no order information is available, redirect to home
        if (empty($this->orderInfo) && empty($this->cartItems)) {
            return redirect()->route('index');
        }
    }

    public function render()
    {
        return view('livewire.success-page');
    }
}
