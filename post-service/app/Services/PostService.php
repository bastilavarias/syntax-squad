<?php

namespace App\Services;

use App\Models\Post;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostService
{
    public function create($payload)
    {
        $hasPostToday = Post::where("user_id", $payload["auth_user"]["id"])->whereDate('created_at', Carbon::today())->first();
        if (!empty($hasPostToday)) {
            throw new Exception('Try again tomorrow, spammer!');
        }
        $post = Post::create([
            "user_id" => $payload["auth_user"]["id"],
            "title" => $payload["title"],
            "is_draft" => $payload["is_draft"] ?? 0,
        ]);
        $post->content()->create([
            "value" => $payload["content"],
        ]);
        $coverImageURL = null;
        if (!empty($payload["cover_image"])) {
            try {
                $coverImageURL = Cloudinary::upload(
                    $payload["cover_image"]->getRealPath(),
                    [
                        "folder" =>
                            config("cloudinary.folder_destination") . "/posts",
                        "public_id" => $post->id,
                    ]
                )->getSecurePath();
            } catch (Exception $e) {
                info($e->getMessage());
            }
        }
        if (!empty($payload["tags"])) {
            foreach ($payload["tags"] as $tag) {
                $post->tags()->create([
                    "name" => $tag,
                ]);
            }
        }
        $slug =
            Str::slug($payload["title"], "-") .
            "_$post->id-" .
            "$post->user_id";

        return tap($post)->update([
            "cover_image_url" => $coverImageURL,
            "slug" => $slug,
        ]);
    }

    public function update($payload)
    {
        $post = Post::where("id", $payload["post_id"])
            ->where("user_id", $payload["auth_user"]["id"])
            ->first();
        if (empty($post)) {
            throw new Exception("You're not allowed to this action.");
        }
        $coverImageURL = $post->cover_image_url;
        if (!empty($payload["cover_image"])) {
            try {
                $coverImageURL = Cloudinary::upload(
                    $payload["cover_image"]->getRealPath(),
                    [
                        "folder" =>
                            config("cloudinary.folder_destination") . "/posts",
                        "public_id" => $post->id,
                        "overwrite" => true,
                    ]
                )->getSecurePath();
            } catch (Exception $e) {
                info($e->getMessage());
            }
        }
        $post->tags()->delete();
        if (!empty($payload["tags"])) {
            foreach ($payload["tags"] as $tag) {
                $post->tags()->create([
                    "name" => $tag,
                ]);
            }
        }
        $slug =
            Str::slug($payload["title"], "-") .
            "_$post->id-" .
            "$post->user_id";
        $post = tap($post)->update([
            "title" => $payload["title"],
            "is_draft" => $payload["is_draft"] ?? 0,
            "cover_image_url" => $coverImageURL,
            "slug" => $slug,
        ]);
        $post->content()->update([
            "value" => $payload["content"],
        ]);

        return $post;
    }

    public function list($payload)
    {
        $page = $payload["page"] ?? 1;
        $perPage = $payload["per_page"] ?? 5;
        $sortBy = $payload["sort_by"];
        $userID = $payload["user_id"] ?? null;
        $isDraft = $payload["is_draft"] ?? null;
        $search = $payload["search"] ?? null;
        $authUserID = $payload["auth_user"]["id"] ?? null;
        $query = Post::query();
        $query
            ->with([
                "user",
                "tags",
                "comments" => function ($q) {
                    $q->with(["user"])
                        ->orderBy("id", "desc")
                        ->take(3);
                },
            ])
            ->withCount(["reactions", "bookmarks", "comments"]);
        if ($sortBy === "latest") {
            $query->orderBy("id", "desc");
        } elseif ($sortBy === "touched") {
            $query->orderBy("updated_at", "desc");
        } elseif ($sortBy === "top") {
            $query->orderByRaw(
                "reactions_count + bookmarks_count + comments_count DESC"
            );
        } elseif ($sortBy === "relevance") {
            if (isset($authUserID)) {
                $query
                    ->leftJoin("user_followers", function ($join) use (
                        $authUserID
                    ) {
                        $join
                            ->on(
                                "posts.user_id",
                                "=",
                                "user_followers.user_following_id"
                            )
                            ->where(
                                "user_followers.user_follower_id",
                                "=",
                                $authUserID
                            );
                    })
                    ->select("posts.*")
                    ->orderByRaw(
                        '
        CASE
            WHEN user_followers.user_following_id IS NOT NULL THEN 1
            ELSE 2
        END,
        posts.reactions_count + posts.bookmarks_count + posts.comments_count DESC'
                    )
                    ->orderBy("posts.id", "desc");
            } else {
                $query
                    ->orderByRaw(
                        "reactions_count + bookmarks_count + comments_count DESC"
                    )
                    ->orderBy("id", "desc");
            }
        }
        if (isset($userID)) {
            $query->where("user_id", $userID);
        }
        if (isset($isDraft)) {
            $query->where("is_draft", $isDraft);
        }
        if (isset($search)) {
            $search = Str::lower($search);
            $query
                ->where(DB::raw("lower(title)"), "like", "%{$search}%")
                ->orWhereHas("content", function ($q) use ($search) {
                    $q->where(DB::raw("lower(value)"), "like", "%{$search}%");
                })
                ->orWhereHas("user", function ($q) use ($search) {
                    $q->where(DB::raw("lower(name)"), "like", "%{$search}%");
                })
                ->orWhereHas("tags", function ($q) use ($search) {
                    $q->where(DB::raw("lower(name)"), "like", "%{$search}%");
                });
        }

        return $query->paginate($perPage, ["*"], "page", $page);
    }

    public function getOne($postID)
    {
        return Post::with(["user", "tags"])
            ->with(["content"])
            ->withCount(["reactions", "bookmarks", "comments"])
            ->findOrFail($postID);
    }

    public function getOneBySlug($slug)
    {
        $post = Post::with([
            "user" => function ($q) {
                $q->withCount(["followings", "followers", "posts"]);
            },
            "tags",
        ])
            ->with(["content"])
            ->withCount(["reactions", "bookmarks", "comments"])
            ->where("slug", $slug)
            ->first();
        if (empty($post)) {
            throw new Exception("Post not found!");
        }

        return $post;
    }

    public function delete($payload)
    {
        $post = Post::where("id", $payload["post_id"])
            ->where("user_id", $payload["auth_user"]["id"])
            ->first();
        if (empty($post)) {
            throw new Exception("You're not allowed to this action.");
        }
        $post->delete();

        return $post;
    }
}
