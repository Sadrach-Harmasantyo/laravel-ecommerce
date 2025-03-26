<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
  <h1 class="text-4xl font-bold text-slate-500">My Orders</h1>

  @if($orders->count() > 0)
  <div class="flex flex-col bg-white p-5 rounded mt-4 shadow-lg">
    <div class="-m-1.5 overflow-x-auto">
      <div class="p-1.5 min-w-full inline-block align-middle">
        <div class="overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
              <tr>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Order #</th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Date</th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Order Status</th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Payment Status</th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Order Amount</th>
                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orders as $order)
              <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-slate-900 dark:even:bg-slate-800">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">{{ $order->order_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $order->created_at->format('d-m-Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                  {{-- <span class="bg-{{ $order->status === 'new' ? 'orange' : ($order->status === 'processing' ? 'yellow' : ($order->status === 'completed' ? 'green' : 'red')) }}-500 py-1 px-3 rounded text-white shadow">
                  {{ ucfirst($order->status) }}
                  </span> --}}
                  <span>
                    {{ ucfirst($order->status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                  {{-- <span class="bg-{{ $order->payment_status === 'pending' ? 'orange' : ($order->payment_status === 'paid' ? 'green' : 'red') }}-500 py-1 px-3 rounded text-white shadow">
                  {{ ucfirst($order->payment_status) }}
                  </span> --}}
                  <span>
                    {{ ucfirst($order->payment_status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                  <a href="{{ route('my-order-detail', $order->id) }}" class="bg-slate-600 text-white py-2 px-4 rounded-md hover:bg-slate-500">View Details</a>
                  @if($order->status === 'shipped')
                  <button
                    wire:click="confirmDelivery({{ $order->id }})"
                    class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600"
                    onclick="confirm('Are you sure you have received this order?') || event.stopImmediatePropagation()">
                    Confirm Delivery
                  </button>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @else
  <div class="flex flex-col items-center justify-center bg-white p-10 rounded mt-4 shadow-lg">
    <svg class="w-16 h-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
    <h2 class="mt-4 text-xl font-semibold text-gray-600">You haven't placed any orders yet</h2>
    <p class="mt-2 text-gray-500">Start shopping to see your orders here</p>
    <a href="{{ route('home') }}" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Shop Now</a>
  </div>
  @endif
</div>