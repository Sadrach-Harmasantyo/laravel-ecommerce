<?php

namespace App\Livewire;

use App\Models\Blog;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Blog - TokoBajuSepatu')]
class BlogsPage extends Component
{
    use WithPagination;

    public function render()
    {
        $blogs = Blog::where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('livewire.blogs-page', [
            'blogs' => $blogs,
        ]);
    }
}
