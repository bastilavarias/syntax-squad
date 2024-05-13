<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollower extends Model
{
    use HasFactory;

    protected $table = "user_followers";
    protected $guarded = [];
    public $timestamps = true;

    public function following()
    {
        return $this->belongsTo(User::class, "user_following_id", "id");
    }

    public function follower()
    {
        return $this->belongsTo(User::class, "user_follower_id", "id");
    }
}
