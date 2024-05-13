<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFollower;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
{
    public function list($payload)
    {
        $page = $payload["page"] ?? 1;
        $perPage = $paylouser_follower_idad["per_page"] ?? 5;
        $search = $payload["search"] ?? null;
        $userID = $payload["user_id"] ?? null;
        $query = User::query();
        $query->withCount(["followings", "followers", "posts"]);
        if (isset($userID)) {
            $query->whereNot("id", $userID);
        }
        if (isset($search)) {
            $search = Str::lower($search);
            $query
                ->where(DB::raw("lower(username)"), "like", "%{$search}%")
                ->orWhere(DB::raw("lower(name)"), "like", "%{$search}%")
                ->orWhere(DB::raw("lower(name)"), "like", "%{$search}%");
        }

        return $query->paginate($perPage, ["*"], "page", $page);
    }

    public function getByUsername($payload)
    {
        $user = User::where("username", $payload["username"])
            ->withCount(["followings", "followers", "posts"])
            ->first();
        if (empty($user)) {
            throw new Exception("User not found");
        }

        return $user;
    }
}
