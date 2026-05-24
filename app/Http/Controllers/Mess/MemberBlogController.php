<?php

namespace App\Http\Controllers\Mess;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogImage;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberBlogController extends Controller
{
    public function myBlogs()
    {
        $blogs = Blog::where('user_id', auth()->id())->latest()->paginate(15);
        return view('mess.blog.my-blogs', compact('blogs'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('sort_order')->get();
        return view('mess.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'excerpt'     => 'nullable|string|max:500',
            'content'     => 'required|string|min:50',
            'category_id' => 'nullable|exists:blog_categories,id',
            'thumbnail'   => 'nullable|image|max:2048',
            'images.*'    => 'nullable|image|max:2048',
            'tags'        => 'nullable|string',
        ]);

        $data = $validated;
        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';
        $data['slug']    = Str::slug($validated['title']) . '-' . Str::random(5);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('blog', 'public');
        }

        $blog = Blog::create($data);

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

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $img) {
                BlogImage::create([
                    'blog_id'    => $blog->id,
                    'image'      => $img->store('blog', 'public'),
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('member.blog.index')->with('success', 'Your blog post has been submitted for review.');
    }

    public function edit(Blog $blog)
    {
        $this->authorize('update', $blog);

        if (!in_array($blog->status, ['pending', 'rejected', 'draft'])) {
            return back()->with('error', 'You can only edit pending or rejected posts.');
        }

        $categories = BlogCategory::orderBy('sort_order')->get();
        $blogTags   = $blog->tags->pluck('name')->implode(', ');
        return view('mess.blog.create', compact('blog', 'categories', 'blogTags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $this->authorize('update', $blog);

        if (!in_array($blog->status, ['pending', 'rejected', 'draft'])) {
            return back()->with('error', 'You can only edit pending or rejected posts.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'excerpt'     => 'nullable|string|max:500',
            'content'     => 'required|string|min:50',
            'category_id' => 'nullable|exists:blog_categories,id',
            'thumbnail'   => 'nullable|image|max:2048',
            'images.*'    => 'nullable|image|max:2048',
            'tags'        => 'nullable|string',
        ]);

        $data = $validated;
        $data['status'] = 'pending'; // resubmit for review

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

        return redirect()->route('member.blog.index')->with('success', 'Post updated and resubmitted for review.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['file' => 'required|image|max:4096']);
        $path = $request->file('file')->store('blog/content', 'public');
        return response()->json(['location' => \Storage::url($path)]);
    }
}
