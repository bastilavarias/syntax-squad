<?php

namespace App\Services;

use App\Models\PostBookmark;

class PostBookmarkService
{
    public function create($payload)
    {
        $foundBookmark = PostBookmark::where(
            "user_id",
            $payload["auth_user"]["id"]
        )
            ->where("post_id", $payload["post_id"])
            ->first();
        $reaction = null;
        if (empty($foundBookmark)) {
            $reaction = PostBookmark::create([
                "user_id" => $payload["auth_user"]["id"],
                "post_id" => $payload["post_id"],
            ]);
        }

        return $reaction;
    }

    public function delete($payload)
    {
        $bookmark = PostBookmark::where("user_id", $payload["auth_user"]["id"])
            ->where("post_id", $payload["post_id"])
            ->first();
        if (empty($bookmark)) {
            return null;
        }
        $bookmark->delete();

        return $bookmark;
    }

    public function check($payload)
    {
        $bookmark = PostBookmark::where("user_id", $payload["auth_user"]["id"])
            ->where("post_id", $payload["post_id"])
            ->first();
        if (empty($bookmark)) {
            return false;
        }

        return true;
    }

    public function list($payload)
    {
        $page = $payload["page"] ?? 1;
        $perPage = $payload["per_page"] ?? 5;
        $userID = $payload["user_id"] ?? null;
        $query = PostBookmark::query();
        $query->with([
            "user",
            "post" => function ($q) {
                $q->with([
                    "user",
                    "tags",
                    "comments" => function ($q2) {
                        $q2->with(["user"])
                            ->orderBy("id", "desc")
                            ->take(3);
                    },
                ])->withCount(["reactions", "bookmarks", "comments"]);
            },
        ]);
        if (isset($userID)) {
            $query->whereHas("user", function ($q) use ($userID) {
                $q->where("user_id", $userID);
            });
        }

        return $query
            ->orderBy("id", "desc")
            ->paginate($perPage, ["*"], "page", $page);
    }
}
