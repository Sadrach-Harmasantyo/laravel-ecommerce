<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
  <section class="overflow-hidden bg-white py-11 font-poppins dark:bg-gray-800">
    <div class="max-w-6xl px-4 py-4 mx-auto lg:py-8 md:px-6">
      <div class="flex flex-wrap -mx-4">
        <div class="w-full mb-8 md:w-1/2 md:mb-0" x-data="{ 
          mainImage: '{{ url('storage', $product->images[0]) }}', 
          selectedVariant: null,
          updateImageFromVariant() {
            if (this.selectedVariant) {
              try {
                const variant = JSON.parse(this.selectedVariant);
                if (variant.image) {
                  this.mainImage = '{{ url('storage') }}/' + variant.image;
                }
              } catch (e) {
                console.error('Error parsing variant JSON', e);
              }
            }
          }
        }" x-init="$watch('$wire.selectedVariant', function(value) {
          selectedVariant = value;
          updateImageFromVariant();
        })">
          <div class="sticky top-0 z-10 overflow-hidden">
            <div class="relative mb-6 lg:mb-10 lg:h-2/4">
              <img x-bind:src="mainImage" alt="{{ $product->name }}" class="object-cover w-full lg:h-full">
            </div>
            <div class="flex-wrap hidden md:flex">
              @foreach ($product->images as $image)
              <div class="w-1/2 p-2 sm:w-1/4" x-on:click="mainImage='{{ url('storage', $image) }}'">
                <img src="{{ url('storage', $image) }}" alt="{{ $product->name }}" class="object-contain w-full lg:h-20 cursor-pointer hover:border hover:border-blue-500">
              </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="w-full px-4 md:w-1/2">
          <div class="lg:pl-20">
            <h2 class="max-w-xl mb-6 text-2xl font-bold dark:text-gray-400 md:text-4xl">{{ $product->name }}</h2>

            <p class="max-w-md text-gray-700 dark:text-gray-400">
              {!! Str::markdown($product->short_description) !!}
            </p>

            <!-- Price Display -->
            <div class="mb-6">
              <span class="text-2xl font-bold text-blue-600">
                @if($selectedVariant)
                @php
                try {
                $variantData = json_decode($selectedVariant, true);
                $price = $variantData['price'] ?? $product->price;
                } catch (\Exception $e) {
                $price = $product->price;
                }
                @endphp
                Rp{{ number_format($price, 0, ',', '.') }}
                @else
                Rp{{ number_format($product->price, 0, ',', '.') }}
                @endif
              </span>
            </div>

            <!-- Product Variant Selector - Only Show if Has Variants -->
            @if($hasVariants)
            <div class="mt-4">
              <label class="block mb-2 text-lg font-semibold text-gray-700 dark:text-gray-400">Pilih varian</label>
              <select wire:model.live="selectedVariant" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-900 dark:text-white">
                @foreach ($product->variants as $variant)
                <option value='{{ json_encode([
                  "id" => $variant->id, 
                  "value" => $variant->value, 
                  "price" => $variant->price, 
                  "image" => $variant->image 
                    ? str_replace("http://127.0.0.1:8000/storage/", "", $variant->image)
                    : ($product->images[0] ?? null),
                  "stock_quantity" => $variant->stock_quantity,
                  "sku" => $variant->sku
                ]) }}'>
                  {{ $variant->value }} - Rp{{ number_format($variant->price, 0, ',', '.') }}
                </option>
                @endforeach
              </select>
            </div>
            @endif

            <!-- Stock Information -->
            <div class="mt-4">
              <p class="text-md text-gray-700 dark:text-gray-400">
                Stok tersedia:
                <span class="font-semibold {{ $stockQuantity <= 5 ? 'text-red-500' : 'text-green-500' }}">
                  @if($stockQuantity > 0)
                  {{ $stockQuantity }}
                  @else
                  <span class="text-red-500">Habis</span>
                  @endif
                </span>
              </p>
            </div>

            <!-- Quantity Selector -->
            <div class="w-32 mt-4 mb-8">
              <label class="block pb-1 text-xl font-semibold text-gray-700 border-b border-blue-300 dark:border-gray-600 dark:text-gray-400">Jumlah</label>
              <div class="relative flex flex-row w-full h-10 mt-2 bg-transparent rounded-lg">
                <button wire:click='decreaseQty' class="w-20 h-full text-gray-600 bg-gray-300 rounded-l dark:bg-gray-900 hover:bg-gray-400"
                  {{ $stockQuantity <= 0 ? 'disabled' : '' }}>-</button>
                <input type="number" wire:model='quantity' readonly class="flex items-center w-full text-center bg-gray-300 dark:bg-gray-900" placeholder="1">
                <button wire:click='increaseQty' class="w-20 h-full text-gray-600 bg-gray-300 rounded-r dark:bg-gray-900 hover:bg-gray-400"
                  {{ $stockQuantity <= 0 || $quantity >= $stockQuantity ? 'disabled' : '' }}>+</button>
              </div>
            </div>
            <!-- Add to Cart Button -->
            @auth
            <button wire:click="addToCart"
              class="flex ml-auto text-white {{ $stockQuantity <= 0 ? 'bg-gray-500 cursor-not-allowed' : 'bg-blue-700 hover:bg-blue-800' }} px-5 py-2.5 rounded-lg"
              {{ $stockQuantity <= 0 ? 'disabled' : '' }}>
              @if($stockQuantity > 0)
              Masukkan ke keranjang
              @else
              Stok Habis
              @endif
            </button>
            @else
            <a href="{{ route('login') }}" class="flex ml-auto text-white bg-blue-700 hover:bg-blue-800 px-5 py-2.5 rounded-lg">Login Untuk Belanja</a>
            @endauth
          </div>
        </div>
      </div>
    </div>
  </section>
</div>