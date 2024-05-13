<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFollower;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserFollowerService
{
    public function create($payload)
    {
        $foundFollower = UserFollower::where(
            "user_following_id",
            $payload["user_id"]
        )
            ->where("user_follower_id", $payload["auth_user"]["id"])
            ->first();
        $follower = null;
        if (empty($foundFollower)) {
            $follower = UserFollower::create([
                "user_following_id" => $payload["user_id"],
                "user_follower_id" => $payload["auth_user"]["id"],
            ]);
        }

        return $follower;
    }

    public function delete($payload)
    {
        $foundFollower = UserFollower::where(
            "user_following_id",
            $payload["user_id"]
        )
            ->where("user_follower_id", $payload["auth_user"]["id"])
            ->first();
        if (empty($foundFollower)) {
            return null;
        }
        $foundFollower->delete();

        return $foundFollower;
    }

    public function check($payload)
    {
        $foundFollower = UserFollower::where(
            "user_following_id",
            $payload["user_id"]
        )
            ->where("user_follower_id", $payload["auth_user"]["id"])
            ->first();
        if (empty($foundFollower)) {
            return false;
        }

        return true;
    }

    public function list($payload)
    {
        $page = $payload["page"] ?? 1;
        $perPage = $payload["per_page"] ?? 5;
        $userID = $payload["user_id"] ?? null;
        $filterBy = $payload["filter_by"] ?? null;
        $query = UserFollower::query();
        $query->with([
            "follower" => function ($q) {
                $q->withCount(["followings", "followers", "posts"]);
            },
            "following" => function ($q) {
                $q->withCount(["followings", "followers", "posts"]);
            },
        ]);
        if (isset($filterBy)) {
            if (isset($userID)) {
                if ($filterBy === "following") {
                    $query->where("user_follower_id", $userID);
                }
                if ($filterBy === "follower") {
                    $query->where("user_following_id", $userID);
                }
            }
        }

        return $query
            ->orderBy("id", "desc")
            ->paginate($perPage, ["*"], "page", $page);
    }
}
