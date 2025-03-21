<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <section class="py-10 bg-gray-50 font-poppins dark:bg-gray-800 rounded-lg">
      <div class="px-4 py-4 mx-auto max-w-7xl lg:py-6 md:px-6">
        <div class="flex flex-wrap mb-24 -mx-3">
          <div class="w-full pr-2 lg:w-1/4 lg:block">
            <div class="p-4 mb-5 bg-white border border-gray-200 dark:border-gray-900 dark:bg-gray-900">
              <h2 class="text-2xl font-bold dark:text-gray-400"> Categories</h2>
              <div class="w-16 pb-2 mb-6 border-b border-rose-600 dark:border-gray-400"></div>
              <ul>

                @foreach ($categories as $category)
                
                  <li class="mb-4" wire:key="{{ $category->id }}">
                    <label for="{{ $category->slug }}" class="flex items-center dark:text-gray-400 ">
                      <input type="checkbox" wire:model.live="selected_categories" id="{{ $category->slug }}" value="{{ $category->id }}" class="w-4 h-4 mr-2">
                      <span class="text-lg">{{ $category->name }}</span>
                    </label>
                  </li> 

                @endforeach 
              
              </ul>
  
            </div>
            <div class="p-4 mb-5 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-900">
              <h2 class="text-2xl font-bold dark:text-gray-400">Brand</h2>
              <div class="w-16 pb-2 mb-6 border-b border-rose-600 dark:border-gray-400"></div>
              <ul>

                @foreach ($brands as $brand)
                
                  <li class="mb-4" wire:key="{{ $brand->id }}">
                    <label for="{{ $brand->slug }}" class="flex items-center dark:text-gray-300">
                      <input type="checkbox" wire:model.live="selected_brands" value="{{ $brand->id }}" id="{{ $brand->slug }}" class="w-4 h-4 mr-2">
                      <span class="text-lg dark:text-gray-400">{{ $brand->name }}</span>
                    </label>
                  </li>

                @endforeach

              </ul>
            </div>
            <div class="p-4 mb-5 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-900">
              <h2 class="text-2xl font-bold dark:text-gray-400">Product Status</h2>
              <div class="w-16 pb-2 mb-6 border-b border-rose-600 dark:border-gray-400"></div>
              <ul>

                <li class="mb-4">
                  <label for="featured" class="flex items-center dark:text-gray-300">
                    <input type="checkbox" id="featured" wire:model.live="featured" value="1" class="w-4 h-4 mr-2">
                    <span class="text-lg dark:text-gray-400">Featured Products</span>
                  </label>
                </li>

                <li class="mb-4">
                  <label for="on_sale" class="flex items-center dark:text-gray-300">
                    <input type="checkbox" id="on_sale" value="1" wire:model.live="on_sale" class="w-4 h-4 mr-2">
                    <span class="text-lg dark:text-gray-400">On Sale</span>
                  </label>
                </li>

              </ul>
            </div>
  
            <div class="p-4 mb-5 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-900">
              <h2 class="text-2xl font-bold dark:text-gray-400">Price</h2>
              <div class="w-16 pb-2 mb-6 border-b border-rose-600 dark:border-gray-400"></div>
              <div class="flex flex-col gap-3">
                <label for="min-price" class="text-lg">Min. Price</label>
                <input id="min-price" type="number" wire:model.live="min_price" placeholder="Min Price" class="text-lg border border-gray-200 rounded-md py-2 px-4">
                <label for="max-price" class="text-lg">Max. Price</label>
                <input id="max-price" type="number" wire:model.live="max_price" placeholder="Max Price" class="text-lg border border-gray-200 rounded-md py-2 px-4">
              </div>
            </div>

          </div>

          <div class="w-full px-3 lg:w-3/4">
            <div class="px-3 mb-4">
              <div class="items-center justify-between hidden px-3 py-2 bg-gray-100 md:flex dark:bg-gray-900 ">
                <div class="flex items-center justify-between w-full gap-3">

                  <input type="text" name="search" placeholder="Search product" class="text-lg border border-gray-200 rounded-md py-2 px-4 w-full" wire:model.live="search">
                  
                  <select wire:model.live="sort" class="block w-40 text-base bg-gray-100 cursor-pointer dark:text-gray-400 dark:bg-gray-900">

                    <option value="latest">Sort by latest</option>
                    <option value="price">Sort by Price</option>

                  </select>
                  
                </div>
              </div>
            </div>
            <div class="flex flex-wrap items-center">
  
              @foreach ($products as $product)
              
                <div class="w-full px-3 mb-6 sm:w-1/2 md:w-1/3" wire:key="{{ $product->id }}">
                  <div class="border border-gray-300 dark:border-gray-700">
                    <div class="relative bg-gray-200">
                      <a wire:navigate href="{{ route('product-detail', $product->slug) }}" class="">
                        <img src="{{ url('storage', $product->images[0]) }}" alt="{{ $product->name }}" class="object-contain w-full h-56 mx-auto ">
                      </a>
                      @if($product->on_sale)
                        <div class="absolute top-2 right-2 bg-red-500 text-white text-sm font-bold px-2 py-1 rounded">
                          SALE
                        </div>
                      @endif
                      @if($product->is_featured)
                        <div class="absolute top-2 right-2 bg-blue-500 text-white text-sm font-bold px-2 py-1 rounded">
                          FEATURED
                        </div>
                      @endif
                    </div>
                    <div class="px-3 mt-3 flex flex-wrap gap-2">
                      @if($product->category)
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                          {{ $product->category->name }}
                        </span>
                      @endif
                      @if($product->brand)
                        <span class="bg-slate-200 text-black text-sm font-medium px-2.5 py-0.5 rounded">
                          {{ $product->brand->name }}
                        </span>
                      @endif
                    </div>
                    <div class="p-3">
                      <div class="flex items-center justify-between gap-2 mb-2">
                        <h3 class="text-xl font-medium dark:text-gray-400">
                          {{ $product->name }}
                        </h3>
                      </div>
                      <p class="text-lg">
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                      </p>
                    </div>
                    {{-- <div class="flex justify-center p-4 border-t border-gray-300 dark:border-gray-700">

                      @auth
                        <button wire:click="addToCart({{ $product->id }})" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add to cart</button>
                      @else
                        <a href="{{ route('login') }}" onclick="event.preventDefault(); window.location.href='{{ route('login') }}';" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Add to cart</a>
                      @endauth
    
                    </div> --}}
                    <div class="mt-auto flex border-t border-gray-200 divide-x divide-gray-200 dark:border-gray-700 dark:divide-gray-700">
                      <a href="{{ route('product-detail', $product->slug) }}" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-es-xl bg-white text-gray-800 shadow-sm hover:bg-gray-50 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                        View Details
                      </a>
                    </div>
                  </div>
                </div>

              @endforeach
  
            </div>

            <!-- pagination start -->
            <div class="flex justify-end mt-6">
              {{ $products->links() }}
            </div>
            <!-- pagination end -->
          </div>
        </div>
      </div>
    </section>
  
</div>