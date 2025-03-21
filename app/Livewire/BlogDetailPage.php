<?php

namespace App\Livewire;

use App\Models\Blog;
use Livewire\Attributes\Title;
use Livewire\Component;

class BlogDetailPage extends Component
{
    public Blog $blog;

    #[Title('Blog - TokoOnline')]
    public function mount(Blog $blog)
    {
        if (!$blog->is_published || $blog->published_at > now()) {
            abort(404);
        }
        
        $this->blog = $blog;
    }

    public function render()
    {
        // Get related blog posts
        $relatedBlogs = Blog::where('id', '!=', $this->blog->id)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.blog-detail-page', [
            'relatedBlogs' => $relatedBlogs,
        ]);
    }
}
