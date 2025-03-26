<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <div class="grid lg:grid-cols-3 gap-y-8 lg:gap-y-0 lg:gap-x-12">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                @if($blog->featured_image)
                    <img src="{{ url('storage', $blog->featured_image) }}" alt="{{ $blog->title }}" class="w-full h-64 md:h-96 object-contain">
                @endif
                
                <div class="p-6 md:p-8">
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span>{{ $blog->published_at->format('M d, Y') }}</span>
                        <span class="mx-2">•</span>
                        <span>Oleh {{ $blog->user->name }}</span>
                    </div>
                    
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">{{ $blog->title }}</h1>
                    
                    <div class="prose max-w-none">
                        {!! $blog->content !!}
                    </div>
                    
                    <!-- Author Info -->
                    <div class="mt-12 pt-6 border-t border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <img class="h-12 w-12 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($blog->user->name) }}&background=random" alt="{{ $blog->user->name }}">
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-800">{{ $blog->user->name }}</h3>
                                <p class="text-sm text-gray-500">Author</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div>
            <!-- Related Posts -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Artikel Terkait</h3>
                    
                    @if($relatedBlogs->count() > 0)
                        <div class="space-y-4">
                            @foreach($relatedBlogs as $relatedBlog)
                                <div class="flex items-start">
                                    @if($relatedBlog->featured_image)
                                        <img src="{{ url('storage', $relatedBlog->featured_image) }}" alt="{{ $relatedBlog->title }}" class="w-16 h-16 object-contain rounded-md">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <a href="{{ route('blog.show', $relatedBlog->slug) }}" class="text-base font-medium text-gray-800 hover:text-blue-600 transition-colors">
                                            {{ $relatedBlog->title }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">{{ $relatedBlog->published_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Tidak ada artikel terkait yang ditemukan.</p>
                    @endif
                </div>
            </div>
            
            <!-- Back to Blog -->
            <a href="{{ route('blog') }}" class="inline-flex items-center justify-center w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Semua Artikel
            </a>
        </div>
    </div>
</div>
