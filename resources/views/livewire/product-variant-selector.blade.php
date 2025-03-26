<div>
    <h3 class="text-lg font-bold">{{ $product->name }}</h3>

    <!-- Pilih Variant -->
    <label for="variant">Choose a variant:</label>
    <select wire:model="selectedVariant" wire:change="updatedSelectedVariant($event.target.value)">
        @foreach ($product->variants as $variant)
            <option value="{{ $variant->id }}">{{ $variant->name }} - {{ $variant->value }}</option>
        @endforeach
    </select>

    <!-- Harga -->
    <p class="text-xl font-semibold mt-2">Price: <span class="text-green-500">IDR {{ number_format($price, 0) }}</span></p>

    <!-- Tombol Tambah / Kurang -->
    <div class="mt-2">
        <button wire:click="decreaseQty" class="px-2 py-1 bg-gray-300">-</button>
        <span class="mx-2">{{ $quantity }}</span>
        <button wire:click="increaseQty" class="px-2 py-1 bg-gray-300">+</button>
    </div>

    <!-- Tombol Tambah ke Keranjang -->
    <button wire:click="addToCart" class="bg-blue-500 text-white px-4 py-2 mt-4">Add to Cart</button>

    <!-- Notifikasi -->
    @if (session()->has('success'))
        <p class="text-green-500 mt-2">{{ session('success') }}</p>
    @endif
</div>
