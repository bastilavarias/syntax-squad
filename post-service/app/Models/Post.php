<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = "posts";
    protected $guarded = [];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function tags()
    {
        return $this->hasMany(PostTag::class, "post_id", "id");
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class, "post_id", "id");
    }

    public function bookmarks()
    {
        return $this->hasMany(PostBookmark::class, "post_id", "id");
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, "post_id", "id");
    }

    public function content()
    {
        return $this->hasOne(PostContent::class, "post_id", "id");
    }
}
