<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto" wire:poll.10s>
	<h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
		Pembayaran
	</h1>
	<div class="grid grid-cols-12 gap-4">
		<div class="md:col-span-12 lg:col-span-8 col-span-12">
			<!-- Card -->
			<div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
				<!-- Shipping Address -->
				<div class="mb-6">
					<h2 class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
						Alamat Pengiriman
					</h2>

					<!-- Tambahkan ini di bagian atas halaman, setelah tag div pembuka -->
					@if(session()->has('error'))
						<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
							<strong class="font-bold">Error!</strong>
							<span class="block sm:inline">{{ session('error') }}</span>
						</div>
					@endif

					<div class="grid grid-cols-2 gap-4">
						<div>
							<label class="block text-gray-700 dark:text-white mb-1" for="first_name">
								Nama Depan
							</label>
							<input wire:model="first_name" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="first_name" type="text">
							@error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
						</div>
						<div>
							<label class="block text-gray-700 dark:text-white mb-1" for="last_name">
								Nama Belakang
							</label>
							<input wire:model="last_name" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="last_name" type="text">
							@error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
						</div>
					</div>
					<div class="mt-4">
						<label class="block text-gray-700 dark:text-white mb-1" for="phone">
							Telp.
						</label>
						<input wire:model="phone" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="phone" type="text">
						@error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>
					<div class="mt-4">
						<label class="block text-gray-700 dark:text-white mb-1" for="address">
							Alamat
						</label>
						<input wire:model="address" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="address" type="text">
						@error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>
					<div class="mt-4">
						<label class="block text-gray-700 dark:text-white mb-1" for="city">
							Kota
						</label>
						<input wire:model="city" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="city" type="text">
						@error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
					</div>
					<div class="grid grid-cols-2 gap-4 mt-4">
						<div>
							<label class="block text-gray-700 dark:text-white mb-1" for="state">
								Provinsi
							</label>
							<input wire:model="state" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="state" type="text">
							@error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
						</div>
						<div>
							<label class="block text-gray-700 dark:text-white mb-1" for="zip">
								Kode Pos
							</label>
							<input wire:model="zip" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="zip" type="text">
							@error('zip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
						</div>
					</div>
				</div>
				<!-- Replace the existing bank selection section with this -->
				<div class="text-lg font-semibold mb-4">
				    Pilih Pembayaran Bank
				</div>
				<ul class="grid w-full gap-6 md:grid-cols-2">
				    @forelse($banks as $bank)
				    <li>
				        <input wire:model="payment_method" class="hidden peer" id="bank_{{ $bank->id }}" name="bank" type="radio" value="{{ $bank->name }}">
				        <label onclick="copyToClipboard('{{ $bank->account_number }}', this)" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 peer-checked:border-blue-600 peer-checked:text-blue-600" for="bank_{{ $bank->id }}">
				            <div class="block">
				                <div class="w-full text-lg font-semibold">{{ $bank->name }}</div>
				                <p>{{ $bank->account_number }}</p>
				                @if($bank->account_name)
				                <p class="text-sm text-gray-500">{{ $bank->account_name }}</p>
				                @endif
				                @if($bank->description)
				                <p class="text-xs text-gray-500 mt-1">{{ $bank->description }}</p>
				                @endif
				            </div>
				        </label>
				    </li>
				    @empty
				    <li class="col-span-2 text-center text-gray-500">
				        No payment methods available. Please contact support.
				    </li>
				    @endforelse
				</ul>
				@error('payment_method') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror

				<!-- Payment Proof Upload Section -->
				<div class="mt-6">
					<h2 class="text-lg font-semibold mb-3 text-gray-700 dark:text-white">Bukti Pembayaran</h2>
					<p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Silakan upload screenshot atau foto bukti pembayaran Anda</p>
					
					<div class="flex items-center justify-center w-full">
						<label for="payment_proof" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
							<div class="flex flex-col items-center justify-center pt-5 pb-6">
								@if($payment_proof_preview)
									<img src="{{ $payment_proof_preview }}" alt="Payment Proof Preview" class="max-h-52 mb-2">
									<p class="text-xs text-gray-500 dark:text-gray-400">Klik untuk mengubah gambar</p>
								@else
									<svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
										<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
									</svg>
									<p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk mengunggah</span> atau tarik dan lepas</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or JPEG (Maks. 2MB)</p>
								@endif
							</div>
							<input wire:model="payment_proof" id="payment_proof" type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" />
						</label>
					</div>
					<div wire:loading wire:target="payment_proof" class="mt-2 text-sm text-blue-600">
						Mengunggah...
					</div>
					@error('payment_proof') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
				</div>
			</div>
			<!-- End Card -->
		</div>
		<div class="md:col-span-12 lg:col-span-4 col-span-12">
			<div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
				<div class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
					RINGKASAN PESANAN
				</div>
				<div class="flex justify-between mb-2 font-bold">
					<span>
						Subtotal
					</span>
					<span>
						Rp{{ number_format($grandTotal, 0, ',', '.') }}
					</span>
				</div>
				<div class="flex justify-between mb-2 font-bold">
					<span>
						Pajak
					</span>
					<span>
						Rp{{ number_format($taxAmount, 0, ',', '.') }}
					</span>
				</div>
				<div class="flex justify-between mb-2 font-bold">
					<span>
						Biaya Pengiriman
					</span>
					<span>
						Rp{{ number_format($shippingCost, 0, ',', '.') }}
					</span>
				</div>
				<hr class="bg-slate-400 my-4 h-1 rounded">
				<div class="flex justify-between mb-2 font-bold">
					<span>
						Total Keseluruhan
					</span>
					<span>
						Rp{{ number_format($finalTotal, 0, ',', '.') }}
					</span>
				</div>
			</div>
			
			<!-- Tambahkan ini sebelum tombol Place Order -->
			<div wire:loading wire:target="placeOrder" class="text-blue-600 mt-2">
			    Pesanan Anda sedang diproses, harap tunggu...
			</div>
			<button wire:click="placeOrder" class="bg-green-500 mt-4 w-full p-3 rounded-lg text-lg text-white hover:bg-green-600 {{ count($cartItems) == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($cartItems) == 0 ? 'disabled' : '' }}>
				Lakukan Pemesanan
			</button>
			<div class="bg-white mt-4 rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
				<div class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
					RINGKASAN KERANJANG
				</div>
				<ul class="divide-y divide-gray-200 dark:divide-gray-700" role="list">
					@if(count($cartItems) > 0)
						@foreach($cartItems as $item)
						<li class="py-3 sm:py-4">
							<div class="flex items-center">
								<div class="flex-shrink-0">
									<img alt="{{ $item['name'] ?? 'Product' }}" class="w-12 h-12 rounded-md object-contain" src="{{ isset($item['image']) ? asset('storage/' . $item['image']) : asset('images/placeholder.jpg') }}">
								</div>
								<div class="flex-1 min-w-0 ms-4">
									<p class="text-sm font-medium text-gray-900 truncate dark:text-white">
										{{ $item['name'] ?? 'Unknown Product' }}
									</p>
									<p class="text-sm text-gray-500 truncate dark:text-gray-400">
										Jumlah: {{ $item['quantity'] ?? 1 }}
									</p>
								</div>
								<div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
									<!-- Replace all instances of ₹ with Rp and update number formatting -->
									<td class="py-4">Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
								</div>
							</div>
						</li>
						@endforeach
					@else
						<li class="py-3 sm:py-4 text-center">
							Keranjang Belanja Kosong
						</li>
					@endif
				</ul>
			</div>
		</div>
	</div>
</div>

<script>
    function copyToClipboard(text, element) {
        navigator.clipboard.writeText(text).then(() => {
            // Menampilkan notifikasi kecil di dalam label
            let originalText = element.innerHTML;
            element.innerHTML += " <span class='text-green-600 text-sm'>(Copied!)</span>";
            
            // Mengembalikan teks asli setelah 1.5 detik
            setTimeout(() => {
                element.innerHTML = originalText;
            }, 1500);
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }
</script>