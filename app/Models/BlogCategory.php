<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'description', 'image', 'sort_order'];

    public function parent()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
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
