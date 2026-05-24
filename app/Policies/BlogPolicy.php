<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;

class BlogPolicy
{
    public function update(User $user, Blog $blog): bool
    {
        return $blog->user_id === $user->id;
    }

    public function delete(User $user, Blog $blog): bool
    {
        return $blog->user_id === $user->id;
    }
}
