<?php

namespace App\Services;

use App\Models\PostReaction;

class PostReactionService
{
    public function create($payload)
    {
        $foundReaction = PostReaction::where(
            "user_id",
            $payload["auth_user"]["id"]
        )
            ->where("post_id", $payload["post_id"])
            ->where("name", $payload["name"])
            ->first();
        $reaction = null;
        if (empty($foundReaction)) {
            $reaction = PostReaction::create([
                "user_id" => $payload["auth_user"]["id"],
                "post_id" => $payload["post_id"],
                "name" => $payload["name"],
            ]);
        }

        return $reaction;
    }

    public function delete($payload)
    {
        $reaction = PostReaction::where("user_id", $payload["auth_user"]["id"])
            ->where("post_id", $payload["post_id"])
            ->where("name", $payload["name"])
            ->first();
        if (empty($reaction)) {
            return null;
        }
        $reaction->delete();

        return $reaction;
    }

    public function check($payload)
    {
        $reaction = PostReaction::where("user_id", $payload["auth_user"]["id"])
            ->where("post_id", $payload["post_id"])
            ->where("name", $payload["name"])
            ->first();
        if (empty($reaction)) {
            return false;
        }

        return true;
    }
}
