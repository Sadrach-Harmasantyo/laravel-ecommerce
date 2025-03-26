<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
  <section class="overflow-hidden bg-white py-11 font-poppins dark:bg-gray-800">
    <div class="max-w-6xl px-4 py-4 mx-auto lg:py-8 md:px-6">
      <div class="flex flex-wrap -mx-4">
        <div class="w-full mb-8 md:w-1/2 md:mb-0" x-data="{ mainImage: '{{ url('storage', $product->images[0]) }}', selectedVariant: null }">
          <div class="sticky top-0 z-10 overflow-hidden ">
            <div class="relative mb-6 lg:mb-10 lg:h-2/4 ">
              <img x-bind:src="mainImage" alt="" class="object-cover w-full lg:h-full ">
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

            <!-- Product Variant Selector -->
            <div class="mt-4">
              <label class="block mb-2 text-lg font-semibold text-gray-700 dark:text-gray-400">Choose Variant</label>
              <select wire:model="selectedVariant" class="w-full p-2 border border-gray-300 rounded-md dark:bg-gray-900 dark:text-white">
                <option value="" disabled selected>Select a variant</option>
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

            <!-- Quantity Selector -->
            <div class="w-32 mt-4 mb-8">
              <label class="block pb-1 text-xl font-semibold text-gray-700 border-b border-blue-300 dark:border-gray-600 dark:text-gray-400">Quantity</label>
              <div class="relative flex flex-row w-full h-10 mt-2 bg-transparent rounded-lg">
                <button wire:click='decreaseQty' class="w-20 h-full text-gray-600 bg-gray-300 rounded-l dark:bg-gray-900 hover:bg-gray-400">-</button>
                <input type="number" wire:model='quantity' readonly class="flex items-center w-full text-center bg-gray-300 dark:bg-gray-900" placeholder="1">
                <button wire:click='increaseQty' class="w-20 h-full text-gray-600 bg-gray-300 rounded-r dark:bg-gray-900 hover:bg-gray-400">+</button>
              </div>
            </div>

            <!-- Add to Cart Button -->
            @auth
            <button wire:click="addToCart({{ $product->id }}, {{ $selectedVariant ?? 'null' }})" class="flex ml-auto text-white bg-blue-700 hover:bg-blue-800 px-5 py-2.5 rounded-lg">Add to cart</button>
            @else
            <a href="{{ route('login') }}" class="flex ml-auto text-white bg-blue-700 hover:bg-blue-800 px-5 py-2.5 rounded-lg">Login to add to cart</a>
            @endauth
          </div>
        </div>
      </div>
    </div>
  </section>
</div>