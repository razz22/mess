<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_tag', 'tag_id', 'blog_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (!$m->slug) {
                $m->slug = Str::slug($m->name);
            }
        });
    }
}
