<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Bank;
use App\Services\AdminNotificationService;

#[Title('Checkout - TokoOnline')]
class CheckoutPage extends Component
{
    use WithFileUploads;

    public $cartItems = [];
    public $grandTotal = 0;
    public $taxAmount = 0;
    public $finalTotal = 0;
    public $shippingCost = 0;
    public $subtotal = 0;
    public $total = 0;

    // Form fields
    public $first_name = '';
    public $last_name = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $zip = '';
    public $payment_method = '';
    public $payment_proof = null;
    public $payment_proof_preview = null;

    public $banks = [];

    public function mount()
    {
        // Redirect to login if user is not authenticated
        if (!Auth::check()) {
            session()->flash('error', 'Log In untuk melanjutkan pembayaran');
            return redirect()->route('login');
        }

        // Pre-fill user information if available
        if (Auth::check()) {
            $user = Auth::user();
            $this->first_name = $user->name; // Assuming name can be used as first name
            $this->email = $user->email;
        }

        $this->loadCartItems();
        $this->banks = Bank::where('is_active', true)->get();
    }

    #[On('cart-updated')]
    public function loadCartItems()
    {
        $this->cartItems = CartManagement::getCartItemsFromCookie();

        // Ensure each cart item has the necessary keys
        foreach ($this->cartItems as $key => $item) {
            // If product_id exists but name doesn't, fetch the product and add its name
            if (isset($item['product_id']) && !isset($item['name'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $this->cartItems[$key]['name'] = $product->name;
                } else {
                    // If product doesn't exist, provide a fallback name
                    $this->cartItems[$key]['name'] = 'Produk tidak dikenal';
                }
            }

            // Ensure price key exists (map from unit_amount if needed)
            if (!isset($item['price']) && isset($item['unit_amount'])) {
                $this->cartItems[$key]['price'] = $item['unit_amount'];
            } elseif (!isset($item['price'])) {
                // If neither price nor unit_amount exists, fetch from database
                if (isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $this->cartItems[$key]['price'] = $product->price;
                    } else {
                        // Default fallback price
                        $this->cartItems[$key]['price'] = 0;
                    }
                } else {
                    // Default fallback price
                    $this->cartItems[$key]['price'] = 0;
                }
            }

            // Ensure quantity exists
            if (!isset($item['quantity'])) {
                $this->cartItems[$key]['quantity'] = 1;
            }
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->grandTotal = CartManagement::calculateGrandTotal($this->cartItems);
        $this->taxAmount = $this->grandTotal * 0.1; // 10% tax
        $this->shippingCost = 20000; // You can set this based on your business logic
        $this->subtotal = $this->grandTotal;
        $this->total = $this->grandTotal + $this->taxAmount + $this->shippingCost;
        $this->finalTotal = $this->total;
    }

    public function updatedPaymentProof()
    {
        $this->validate([
            'payment_proof' => 'image|max:2048', // 2MB Max
        ]);

        $this->payment_proof_preview = $this->payment_proof->temporaryUrl();
    }

    /**
     * Check if all items in cart have sufficient stock
     * 
     * @return bool|array Returns true if all items have stock, otherwise returns array with error details
     */
    private function checkStockAvailability()
    {
        foreach ($this->cartItems as $item) {
            $quantity = $item['quantity'] ?? 1;
            
            // Check variant stock if variant_id exists
            if (isset($item['variant_id'])) {
                $variant = ProductVariant::find($item['variant_id']);
                
                if ($variant) {
                    if ($variant->stock_quantity < $quantity) {
                        return [
                            'status' => false,
                            'message' => "Stok tidak cukup untuk produk {$item['name']} varian {$variant->name}: {$variant->value}. Tersedia: {$variant->stock_quantity}, dibutuhkan: {$quantity}."
                        ];
                    }
                }
            } else {
                // For non-variant products, check if product has variants
                $product = Product::find($item['product_id'] ?? $item['id'] ?? null);
                
                if ($product) {
                    // If product has variants, we should not allow purchase without selecting a variant
                    if ($product->variants()->count() > 0) {
                        return [
                            'status' => false,
                            'message' => "Mohon pilih varian untuk produk {$item['name']}."
                        ];
                    }
                    
                    // If product has no variants but is out of stock
                    if (!$product->in_stock) {
                        return [
                            'status' => false,
                            'message' => "Produk {$item['name']} sudah tidak tersedia."
                        ];
                    }
                }
            }
        }
        
        return true;
    }

    public function placeOrder()
    {
        // Tambahkan debugging
        try {
            // Validate the form
            $this->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
                'payment_method' => 'required',
                'payment_proof' => 'required|image|max:2048',
            ]);

            // Check stock availability before proceeding
            $stockCheck = $this->checkStockAvailability();
            if ($stockCheck !== true) {
                session()->flash('error', $stockCheck['message']);
                return null;
            }

            // Process the order and payment proof
            // Store the payment proof
            $payment_proof_path = $this->payment_proof->store('payment_proofs', 'public');

            // Generate order number
            $orderNumber = 'ORD-' . time() . '-' . rand(1000, 9999);

            // Begin database transaction
            DB::beginTransaction();

            try {
                // Create order in database
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'grand_total' => $this->finalTotal,
                    'payment_method' => $this->payment_method,
                    'payment_status' => 'pending',
                    'status' => 'new',
                    'currency' => 'IDR',
                    'shipping_amount' => $this->shippingCost,
                    'shipping_method' => 'standard',
                    'notes' => 'Order placed via website. Payment proof uploaded.',
                    'payment_proof' => $payment_proof_path,
                ]);

                // Create address for the order
                $order->address()->create([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'city' => $this->city,
                    'state' => $this->state,
                    'zip' => $this->zip,
                    'type' => 'shipping',
                ]);

                $productsToUpdateStock = [];

                foreach ($this->cartItems as $item) {
                    // Ambil data produk langsung dari database untuk memastikan harga yang benar
                    $product = Product::find($item['product_id'] ?? $item['id'] ?? null);

                    if ($product) {
                        $unitPrice = ($item['price'] ?? 0) > 0 ? $item['price'] : $product->price;
                        $quantity = $item['quantity'] ?? 1;

                        $orderItemData = [
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'unit_amount' => $unitPrice,
                            'total_amount' => $unitPrice * $quantity,
                        ];

                        // Handle variant stock reduction if variant exists
                        if (isset($item['variant_id'])) {
                            $variant = ProductVariant::find($item['variant_id']);
                            if ($variant) {
                                $orderItemData['product_variant_id'] = $variant->id;
                                $orderItemData['variant_name'] = $variant->name;
                                $orderItemData['variant_value'] = $variant->value;
                                $orderItemData['sku'] = $variant->sku;
                                
                                // Reduce variant stock
                                $variant->stock_quantity -= $quantity;
                                $variant->save();
                                
                                // Add product to update stock status list
                                if (!in_array($product->id, $productsToUpdateStock)) {
                                    $productsToUpdateStock[] = $product->id;
                                }
                                
                                // Log stock change
                                \Log::info("Reduced stock for variant {$variant->id} ({$variant->name}: {$variant->value}) by {$quantity}. New stock: {$variant->stock_quantity}");
                            }
                        }

                        $order->items()->create($orderItemData);
                    } else {
                        \Log::error('Produk yang Anda pesan tidak ditemukan!', ['item' => $item]);
                    }
                }

                // Update stock status for all affected products
                foreach ($productsToUpdateStock as $productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $product->updateStockStatus();
                    }
                }

                // Commit transaction
                DB::commit();

                // Send notification to admins about the new order
                AdminNotificationService::notifyAdminsAboutNewOrder($order);

                // Store order information in session for success page
                session([
                    'customer_info' => [
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'phone' => $this->phone,
                        'address' => $this->address,
                        'city' => $this->city,
                        'state' => $this->state,
                        'zip' => $this->zip,
                    ],
                    'order_info' => [
                        'payment_proof' => $payment_proof_path,
                        'payment_method' => $this->payment_method,
                    ],
                    'order_items' => $this->cartItems,
                    'grand_total' => $this->grandTotal,
                    'tax_amount' => $this->taxAmount,
                    'shipping_cost' => $this->shippingCost,
                    'final_total' => $this->finalTotal,
                    'order_number' => $orderNumber,
                    'order_date' => now()->format('d-m-Y'),
                    'payment_method' => $this->payment_method,
                ]);

                // Clear the cart after successful order
                CartManagement::clearCartItems();

                // Redirect to success page
                return redirect()->route('success');
            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollBack();

                // Add error message with detailed exception
                session()->flash('error', 'Gagal melakukan pemesanan: ' . $e->getMessage());

                return null;
            }
        } catch (\Exception $e) {
            // Tangkap error validasi atau error lainnya
            session()->flash('error', 'Gagal mengisi form: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.checkout-page');
    }
}