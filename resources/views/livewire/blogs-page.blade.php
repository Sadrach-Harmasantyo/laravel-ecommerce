<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-100 to-cyan-100 rounded-xl p-8 md:p-12 mb-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Artikel Kami</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Ikuti terus berita, tips, dan wawasan terkini tentang baju dan sepatu.
            </p>
        </div>
    </div>

    <!-- Blog Posts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
        @forelse($blogs as $blog)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <a href="{{ route('blog.show', $blog->slug) }}" class="block">
                    @if($blog->featured_image)
                        <img src="{{ url('storage', $blog->featured_image) }}" alt="{{ $blog->title }}" class="w-full h-48 object-contain">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </a>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <span>{{ $blog->published_at->format('M d, Y') }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $blog->user->name }}</span>
                    </div>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="block">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 hover:text-blue-600 transition-colors">{{ $blog->title }}</h3>
                    </a>
                    <p class="text-gray-600 mb-4">
                        {{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 150) }}
                    </p>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="text-blue-600 font-medium hover:text-blue-800 transition-colors">
                        Baca lanjut →
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <h3 class="text-xl text-gray-600">Belum ada postingan artikel yang tersedia.</h3>
                <p class="mt-2 text-gray-500">Periksa kembali segera untuk konten baru!</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $blogs->links() }}
    </div>
</div>
