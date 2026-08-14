<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        return view('site.blog.index', [
            'categories' => PostCategory::withCount('posts')->get(),
            'featured' => Post::published()->with('category', 'author')->latest('published_at')->first(),
            'posts' => Post::published()->with('category', 'author')
                ->when($request->category, fn ($q, $slug) => $q->whereRelation('category', 'slug', $slug))
                ->when($request->q, fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
                ->latest('published_at')->paginate(9)->withQueryString(),
            'active' => $request->category,
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->status === 'published', 404);

        $post->increment('views');
        $post->load('category', 'author');

        return view('site.blog.show', [
            'post' => $post,
            'related' => Post::published()->where('id', '!=', $post->id)
                ->where('post_category_id', $post->post_category_id)->take(3)->get(),
        ]);
    }
}
