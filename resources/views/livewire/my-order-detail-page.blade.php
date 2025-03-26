<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
  <h1 class="text-4xl font-bold text-slate-500">Detail Pesanan</h1>

  <!-- Grid -->
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-5">
    <!-- Card -->
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-800">
      <div class="p-4 md:p-5 flex gap-x-4">
        <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-gray-800">
          <svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        </div>

        <div class="grow">
          <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500">
              Pelanggan
            </p>
          </div>
          <div class="mt-1 flex items-center gap-x-2">
            <div>{{ $order->user->name }}</div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Card -->

    <!-- Card -->
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-800">
      <div class="p-4 md:p-5 flex gap-x-4">
        <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-gray-800">
          <svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 22h14" />
            <path d="M5 2h14" />
            <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22" />
            <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2" />
          </svg>
        </div>

        <div class="grow">
          <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500">
              Tanggal Pemesanan
            </p>
          </div>
          <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">
              {{ $order->created_at->format('d-m-Y') }}
            </h3>
          </div>
        </div>
      </div>
    </div>
    <!-- End Card -->

    <!-- Card -->
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-800">
      <div class="p-4 md:p-5 flex gap-x-4">
        <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-gray-800">
          <svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6" />
            <path d="m12 12 4 10 1.7-4.3L22 16Z" />
          </svg>
        </div>

        <div class="grow">
          <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500">
              Status Pemesanan
            </p>
          </div>
          <div class="mt-1 flex items-center gap-x-2">
            {{-- <span class="bg-{{ $order->status === 'new' ? 'orange' : ($order->status === 'processing' ? 'yellow' : ($order->status === 'completed' ? 'green' : 'red')) }}-500 py-1 px-3 rounded text-white shadow">
            {{ ucfirst($order->status) }}
            </span> --}}
            <span>
              {{ ucfirst($order->status) }}
            </span>
          </div>
        </div>
      </div>
    </div>
    <!-- End Card -->

    <!-- Add this after the order status card -->
    @if($order->status === 'shipped' && $order->tracking_number)
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-800">
      <div class="p-4 md:p-5 flex gap-x-4">
        <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-gray-800">
          <svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
        </div>

        <div class="grow">
          <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500">
              Nomor Resi
            </p>
          </div>
          <div class="mt-1 flex items-center gap-x-2">
            <span id="tracking-number" class="font-medium cursor-pointer hover:text-blue-600 flex items-center"
              onclick="copyTrackingAndRedirect('{{ $order->tracking_number }}')">
              {{ $order->tracking_number }}
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </span>
            <span id="copy-message" class="text-green-500 text-xs hidden ml-2">Copied!</span>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Card -->
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-800">
      <div class="p-4 md:p-5 flex gap-x-4">
        <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-gray-800">
          <svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12s2.545-5 7-5c4.454 0 7 5 7 5s-2.546 5-7 5c-4.455 0-7-5-7-5z" />
            <path d="M12 13a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
            <path d="M21 17v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2" />
            <path d="M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2" />
          </svg>
        </div>

        <div class="grow">
          <div class="flex items-center gap-x-2">
            <p class="text-xs uppercase tracking-wide text-gray-500">
              Status Pembayaran
            </p>
          </div>
          <div class="mt-1 flex items-center gap-x-2">
            {{-- <span class="bg-{{ $order->payment_status === 'pending' ? 'orange' : ($order->payment_status === 'paid' ? 'green' : 'red') }}-500 py-1 px-3 rounded text-white shadow">
            {{ ucfirst($order->payment_status) }}
            </span> --}}
            <span>
              {{ ucfirst($order->payment_status) }}
            </span>
          </div>
        </div>
      </div>
    </div>
    <!-- End Card -->
  </div>
  <!-- End Grid -->

  <div class="flex flex-col md:flex-row gap-4 mt-4">
    <div class="md:w-3/4">
      <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
        <table class="w-full">
          <thead>
            <tr>
              <th class="text-left font-semibold">Produk</th>
              <th class="text-left font-semibold">Harga</th>
              <th class="text-left font-semibold">Jumlah</th>
              <th class="text-left font-semibold">Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr wire:key="{{ $item->id }}">
              <td class="py-4">
                <div class="flex items-center">
                  <img class="h-16 w-16 mr-4 object-contain" src="{{ asset('storage/' . $item->product->images[0]) }}" alt="{{ $item->product->name }}">
                  <span class="font-semibold">{{ $item->product->name }}</span>
                </div>
              </td>
              <td class="py-4">Rp{{ number_format($item->unit_amount, 0, ',', '.') }}</td>
              <td class="py-4">
                <span class="text-center w-8">{{ $item->quantity }}</span>
              </td>
              <td class="py-4">Rp{{ number_format($item->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
        <h1 class="font-3xl font-bold text-slate-500 mb-3">Alamat Pengiriman</h1>
        <div class="flex justify-between items-center">
          <div>
            <p>{{ $order->address->address }}, {{ $order->address->city }}, {{ $order->address->state }}, {{ $order->address->zip }}</p>
          </div>
          <div>
            <p class="font-semibold">Telp. :</p>
            <p>{{ $order->address->phone }}</p>
          </div>
        </div>
      </div>
    </div>
    <div class="md:w-1/4">
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Ringkasan</h2>
        <div class="flex justify-between mb-2">
          <span>Subtotal</span>
          <span>Rp{{ number_format($order->grand_total - $order->shipping_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between mb-2">
          <span>Pajak</span>
          <span>Rp{{ number_format($order->grand_total * 0.10, 0, ',', '.')}}</span>
        </div>
        <div class="flex justify-between mb-2">
          <span>Pengiriman</span>
          <span>Rp{{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
        </div>
        <hr class="my-2">
        <div class="flex justify-between mb-2">
          <span class="font-semibold">Total Keseluruhan</span>
          <span class="font-semibold">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</span>
        </div>
      </div>
      @if($order->status === 'shipped')
      <div class="bg-white rounded-lg shadow-md p-6">
        <button
          wire:click="confirmDelivery({{ $order->id }})"
          class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600"
          onclick="confirm('Apakah Anda yakin pesanan telah diterima?') || event.stopImmediatePropagation()">
          Konfirmasi Pesanan Diterima
        </button>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Add this at the bottom of the file, before the closing </div> tag -->
<script>
  function copyTrackingAndRedirect(trackingNumber) {
    // Copy to clipboard
    navigator.clipboard.writeText(trackingNumber).then(function() {
      // Show the "Copied!" message
      const copyMessage = document.getElementById('copy-message');
      copyMessage.classList.remove('hidden');

      // Hide the message after 2 seconds
      setTimeout(function() {
        copyMessage.classList.add('hidden');
      }, 2000);

      // Redirect to cekresi.com
      window.open('https://cekresi.com/?noresi=' + trackingNumber, '_blank');
    }).catch(function(err) {
      console.error('Could not copy text: ', err);
    });
  }
</script>