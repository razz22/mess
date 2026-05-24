<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogLike;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::published()->with(['user', 'category', 'tags', 'likes', 'allComments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('tag')) {
            $tags = (array) $request->tag;
            $query->whereHas('tags', fn($q) => $q->whereIn('slug', $tags));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'latest');
        $query->when($sort === 'popular', fn($q) => $q->orderByDesc('views'))
              ->when($sort === 'liked',   fn($q) => $q->withCount('likes')->orderByDesc('likes_count'))
              ->when($sort !== 'popular' && $sort !== 'liked', fn($q) => $q->orderByDesc('published_at'));

        $blogs      = $query->paginate(9)->withQueryString();
        $categories = BlogCategory::withCount(['blogs' => fn($q) => $q->published()])->orderBy('sort_order')->get();
        $recentPosts = Blog::published()->orderByDesc('published_at')->limit(5)->get();
        $tags       = BlogTag::withCount('blogs')->having('blogs_count', '>', 0)->orderByDesc('blogs_count')->get();
        $featured   = Blog::published()->where('featured', true)->orderByDesc('published_at')->first();

        return view('public.blog.index', compact('blogs', 'categories', 'recentPosts', 'tags', 'featured'));
    }

    public function show(string $slug)
    {
        $blog = Blog::published()
            ->with(['user', 'category', 'tags', 'images', 'likes', 'comments.user', 'comments.replies.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views (simple, no session dedup for now)
        $blog->increment('views');

        $related = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where(function ($q) use ($blog) {
                if ($blog->category_id) {
                    $q->where('category_id', $blog->category_id);
                }
            })
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $isLiked = $blog->isLikedBy(auth()->user());

        return view('public.blog.show', compact('blog', 'related', 'isLiked'));
    }

    public function like(Request $request, Blog $blog)
    {
        $user = auth()->user();
        $existing = BlogLike::where('blog_id', $blog->id)->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            BlogLike::create([
                'blog_id'    => $blog->id,
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'count' => $blog->likes()->count(),
        ]);
    }

    public function comment(Request $request, Blog $blog)
    {
        if (!$blog->allow_comments) {
            return back()->with('error', 'Comments are disabled for this post.');
        }

        $rules = [
            'content'   => 'required|string|min:2|max:2000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ];

        if (!auth()->check()) {
            $rules['guest_name']  = 'required|string|max:100';
            $rules['guest_email'] = 'required|email|max:255';
        }

        $validated = $request->validate($rules);

        BlogComment::create([
            'blog_id'     => $blog->id,
            'user_id'     => auth()->id(),
            'parent_id'   => $validated['parent_id'] ?? null,
            'guest_name'  => auth()->check() ? null : $validated['guest_name'],
            'guest_email' => auth()->check() ? null : ($validated['guest_email'] ?? null),
            'content'     => $validated['content'],
            'status'      => 'approved',
        ]);

        return back()->with('success', 'Your comment has been posted.');
    }
}
