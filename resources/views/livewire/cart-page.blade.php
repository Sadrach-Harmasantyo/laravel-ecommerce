<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto" wire:poll.10s>
  <div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Shopping Cart</h1>
    <div class="flex flex-col md:flex-row gap-4">
      <div class="md:w-3/4">
        <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
          <table class="w-full">
            <thead>
              <tr>
                <th class="text-left font-semibold">Product</th>
                <th class="text-left font-semibold">Price</th>
                <th class="text-left font-semibold">Quantity</th>
                <th class="text-left font-semibold">Total</th>
                <th class="text-left font-semibold">Remove</th>
              </tr>
            </thead>
            <tbody>
              @if(count($cartItems) > 0)
              @foreach($cartItems as $item)
              <tr wire:key="{{ $item['product_id'] }}-{{ $item['variant_id'] ?? 'default' }}">
                <td class="py-4">
                  <div class="flex items-center">
                    <img class="h-16 w-16 mr-4 object-contain" src="{{ isset($item['image']) ? asset('storage/' . $item['image']) : asset('images/placeholder.jpg') }}" alt="Product image">
                    <div>
                      <span class="font-semibold">{{ $item['name'] }}</span>
                      @if(isset($item['variant_id']))
                      <div class="text-sm text-gray-500">
                        Variant: {{ $item['variant_name'] ?? 'Default' }}
                        @if($item['variant_value'])
                        ({{ $item['variant_value'] }})
                        @endif
                      </div>
                      <div class="text-xs text-gray-400">SKU : {{ $item['sku'] }}</div>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="py-4">Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                <td class="py-4">
                  <div class="flex items-center">
                    <button wire:click="decrementItem('{{ $item['product_id'] }}', '{{ $item['variant_id'] ?? '' }}')" class="border rounded-md py-2 px-4 mr-2 hover:bg-gray-100">-</button>
                    <span class="text-center w-8">{{ $item['quantity'] }}</span>
                    <button wire:click="incrementItem('{{ $item['product_id'] }}', '{{ $item['variant_id'] ?? '' }}')" class="border rounded-md py-2 px-4 ml-2 hover:bg-gray-100">+</button>
                  </div>
                </td>
                <td class="py-4">Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                <td>
                  <button wire:click="removeItem('{{ $item['product_id'] }}', '{{ $item['variant_id'] ?? '' }}')" class="bg-slate-300 border-2 border-slate-400 rounded-lg px-3 py-1 hover:bg-red-500 hover:text-white hover:border-red-700">Remove</button>
                </td>
              </tr>
              @endforeach

              @else
              <tr>
                <td colspan="5" class="py-4 text-center">Your cart is empty</td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
      <div class="md:w-1/4">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold mb-4">Summary</h2>
          <div class="flex justify-between mb-2">
            <span>Subtotal</span>
            <span>Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
          </div>
          <div class="flex justify-between mb-2">
            <span>Taxes</span>
            <span>Rp{{ number_format($taxAmount,  0, ',', '.') }}</span>
          </div>
          <div class="flex justify-between mb-2">
            <span>Shipping</span>
            <span>Rp{{ number_format($shippingCost, 0, ',', '.') }}</span>
          </div>
          <hr class="my-2">
          <div class="flex justify-between mb-2">
            <span class="font-semibold">Total</span>
            <span class="font-semibold">Rp{{ number_format($total, 0, ',', '.') }}</span>
          </div>
          <!-- In the cart summary section, update the checkout button -->
          <div class="mt-6">
            @auth
            <a href="{{ route('checkout') }}" class="flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">Checkout</a>
            @else
            <div class="text-center mb-3 text-sm text-gray-600">
              Please login to proceed with checkout
            </div>
            <a href="{{ route('login') }}" class="flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">Login to Checkout</a>
            @endauth
          </div>
          <a href="{{ route('all-products') }}" class="block text-center bg-gray-200 text-gray-800 py-2 px-4 rounded-lg mt-2 w-full hover:bg-gray-300">
            Continue Shopping
          </a>
        </div>
      </div>
    </div>
  </div>
</div>