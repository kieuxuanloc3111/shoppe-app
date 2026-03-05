<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'blog_id',
        'user_id',
        'parent_id',
        'level',
        'content',
        'user_name',
        'user_avatar',
    ];

    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
