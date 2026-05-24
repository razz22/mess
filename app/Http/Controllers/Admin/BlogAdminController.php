<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogImage;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['user', 'category'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $blogs = $query->paginate(20)->withQueryString();
        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('sort_order')->get();
        $tags       = BlogTag::orderBy('name')->get();
        return view('admin.blog.form', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'excerpt'        => 'nullable|string|max:500',
            'content'        => 'required|string',
            'category_id'    => 'nullable|exists:blog_categories,id',
            'status'         => 'required|in:draft,published',
            'featured'       => 'boolean',
            'allow_comments' => 'boolean',
            'thumbnail'      => 'nullable|image|max:2048',
            'images.*'       => 'nullable|image|max:2048',
            'tags'           => 'nullable|string',
        ]);

        $data = $validated;
        $data['user_id']     = auth()->id();
        $data['featured']    = $request->boolean('featured');
        $data['allow_comments'] = $request->boolean('allow_comments', true);
        $data['slug']        = Str::slug($validated['title']) . '-' . Str::random(5);

        if ($validated['status'] === 'published') {
            $data['published_at'] = now();
            $data['approved_by']  = auth()->id();
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('blog', 'public');
        }

        $blog = Blog::create($data);

        // Tags
        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if ($name) {
                    $tag = BlogTag::firstOrCreate(['name' => $name], ['slug' => Str::slug($name)]);
                    $tagIds[] = $tag->id;
                }
            }
            $blog->tags()->sync($tagIds);
        }

        // Additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $img) {
                BlogImage::create([
                    'blog_id'    => $blog->id,
                    'image'      => $img->store('blog', 'public'),
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::orderBy('sort_order')->get();
        $tags       = BlogTag::orderBy('name')->get();
        $blogTags   = $blog->tags->pluck('name')->implode(', ');
        return view('admin.blog.form', compact('blog', 'categories', 'tags', 'blogTags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'excerpt'        => 'nullable|string|max:500',
            'content'        => 'required|string',
            'category_id'    => 'nullable|exists:blog_categories,id',
            'status'         => 'required|in:draft,published,pending,rejected',
            'featured'       => 'boolean',
            'allow_comments' => 'boolean',
            'thumbnail'      => 'nullable|image|max:2048',
            'images.*'       => 'nullable|image|max:2048',
            'tags'           => 'nullable|string',
        ]);

        $data = $validated;
        $data['featured']    = $request->boolean('featured');
        $data['allow_comments'] = $request->boolean('allow_comments', true);

        if ($validated['status'] === 'published' && $blog->status !== 'published') {
            $data['published_at'] = now();
            $data['approved_by']  = auth()->id();
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('blog', 'public');
        }

        $blog->update($data);

        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if ($name) {
                    $tag = BlogTag::firstOrCreate(['name' => $name], ['slug' => Str::slug($name)]);
                    $tagIds[] = $tag->id;
                }
            }
            $blog->tags()->sync($tagIds);
        } else {
            $blog->tags()->detach();
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $img) {
                BlogImage::create([
                    'blog_id'    => $blog->id,
                    'image'      => $img->store('blog', 'public'),
                    'sort_order' => $blog->images()->max('sort_order') + $i + 1,
                ]);
            }
        }

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return back()->with('success', 'Blog post deleted.');
    }

    public function approve(Blog $blog)
    {
        $blog->update([
            'status'       => 'published',
            'published_at' => now(),
            'approved_by'  => auth()->id(),
            'rejection_reason' => null,
        ]);
        return back()->with('success', 'Blog post approved and published.');
    }

    public function reject(Request $request, Blog $blog)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $blog->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);
        return back()->with('success', 'Blog post rejected.');
    }

    // ── Categories ──────────────────────────────────────────────

    public function categories()
    {
        $categories = BlogCategory::with('parent')->orderBy('sort_order')->get();
        $parents    = BlogCategory::whereNull('parent_id')->get();
        return view('admin.blog.categories', compact('categories', 'parents'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'parent_id'   => 'nullable|exists:blog_categories,id',
            'description' => 'nullable|string|max:500',
            'sort_order'  => 'nullable|integer',
        ]);
        BlogCategory::create($validated);
        return back()->with('success', 'Category created.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    // ── Tags ────────────────────────────────────────────────────

    public function tags()
    {
        $tags = BlogTag::withCount('blogs')->orderBy('name')->get();
        return view('admin.blog.tags', compact('tags'));
    }

    public function storeTag(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:100|unique:blog_tags,name']);
        BlogTag::create($validated);
        return back()->with('success', 'Tag created.');
    }

    public function destroyTag(BlogTag $tag)
    {
        $tag->delete();
        return back()->with('success', 'Tag deleted.');
    }

    public function uploadImage(\Illuminate\Http\Request $request)
    {
        $request->validate(['file' => 'required|image|max:4096']);
        $path = $request->file('file')->store('blog/content', 'public');
        return response()->json(['location' => \Storage::url($path)]);
    }
}
