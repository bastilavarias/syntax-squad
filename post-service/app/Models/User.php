<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";
    protected $guarded = [];
    public $timestamps = true;

    public function posts()
    {
        return $this->hasMany(Post::class, "user_id", "id");
    }

    public function followings()
    {
        return $this->hasMany(UserFollower::class, "user_follower_id", "id");
    }

    public function followers()
    {
        return $this->hasMany(UserFollower::class, "user_following_id", "id");
    }
}
