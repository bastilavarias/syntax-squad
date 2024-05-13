<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostBookmark extends Model
{
    use HasFactory;

    protected $table = "post_bookmarks";
    protected $guarded = [];
    public $timestamps = true;

    public function post()
    {
        return $this->belongsTo(Post::class, "post_id", "id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
