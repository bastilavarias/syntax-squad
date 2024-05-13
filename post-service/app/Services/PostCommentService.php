<?php

namespace App\Services;

use App\Models\PostComment;
use Exception;

class PostCommentService
{
    public function create($payload)
    {
        $comment = PostComment::create([
            "user_id" => $payload["auth_user"]["id"],
            "post_id" => $payload["post_id"],
            "content" => $payload["content"],
        ]);

        return PostComment::with("user")->find($comment->id);
    }

    public function list($payload)
    {
        $postID = $payload["post_id"];
        $userID = $payload["userID"] ?? null;
        $page = $payload["page"] ?? 1;
        $perPage = $payload["per_page"] ?? 5;
        $query = PostComment::query();
        $query->with("user")->where("post_id", $postID);
        if (isset($userID)) {
            $query->where("user_id", $userID);
        }

        return $query
            ->orderBy("id", "desc")
            ->paginate($perPage, ["*"], "page", $page);
    }

    public function update($payload)
    {
        $comment = PostComment::where("id", $payload["comment_id"])
            ->where("user_id", $payload["auth_user"]["id"])
            ->first();
        if (empty($comment)) {
            throw new Exception("You're not allowed to this action.");
        }

        return tap($comment)->update([
            "content" => $payload["content"],
        ]);
    }

    public function delete($payload)
    {
        $comment = PostComment::where("id", $payload["comment_id"])
            ->where("user_id", $payload["auth_user"]["id"])
            ->first();
        if (empty($comment)) {
            throw new Exception("You're not allowed to this action.");
        }
        $comment->delete();

        return $comment;
    }
}
