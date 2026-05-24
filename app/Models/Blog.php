<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'thumbnail', 'status', 'rejection_reason', 'published_at',
        'approved_by', 'views', 'featured', 'allow_comments',
    ];

    protected $casts = [
        'published_at'  => 'datetime',
        'featured'      => 'boolean',
        'allow_comments'=> 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag', 'blog_id', 'tag_id');
    }

    public function images()
    {
        return $this->hasMany(BlogImage::class)->orderBy('sort_order');
    }

    public function likes()
    {
        return $this->hasMany(BlogLike::class);
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class)
            ->whereNull('parent_id')
            ->where('status', 'approved');
    }

    public function allComments()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function isLikedBy($user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getReadTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($wordCount / 200));
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (!$m->slug) {
                $m->slug = Str::slug($m->title) . '-' . Str::random(5);
            }
        });
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }
}
