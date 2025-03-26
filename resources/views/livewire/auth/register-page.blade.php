<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Buat Akun Baru</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form class="space-y-6" wire:submit.prevent="register">
      <div>
        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nama Lengkap</label>
        <div class="mt-2">
          <input wire:model="name" id="name" name="name" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
      </div>
      
      <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Alamat Email</label>
        <div class="mt-2">
          <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
      </div>

      <div>
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        <div class="mt-2">
          <input wire:model="password" id="password" name="password" type="password" autocomplete="new-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
      </div>
      
      <div>
        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Konfirmasi Password</label>
        <div class="mt-2">
          <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Register</button>
      </div>
    </form>

    <p class="mt-10 text-center text-sm text-gray-500">
      Sudah punya akun?
      <a href="{{ route('login') }}" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Masuk</a>
    </p>
  </div>
</div>