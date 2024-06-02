<?php

use App\Http\Controllers\API\PostController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostReactionController;
use App\Http\Controllers\API\PostCommentController;
use App\Http\Controllers\API\PostBookmarkController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserFollowerController;

Route::middleware(EnsureTokenIsValid::class)
    ->prefix("reaction")
    ->group(function () {
        Route::post("/", [PostReactionController::class, "store"]);
        Route::delete("/{postID}", [PostReactionController::class, "delete"]);
        Route::get("/check/{postID}", [PostReactionController::class, "check"]);
    });
Route::prefix("bookmark")->group(function () {
    Route::get("/", [PostBookmarkController::class, "index"]);
    Route::middleware(EnsureTokenIsValid::class)->post("/", [
        PostBookmarkController::class,
        "store",
    ]);
    Route::middleware(EnsureTokenIsValid::class)->delete("/{postID}", [
        PostBookmarkController::class,
        "delete",
    ]);
    Route::middleware(EnsureTokenIsValid::class)->get("/check/{postID}", [
        PostBookmarkController::class,
        "check",
    ]);
});
Route::prefix("comment")->group(function () {
    Route::get("/", [PostCommentController::class, "index"]);
    Route::middleware(EnsureTokenIsValid::class)->post("/", [
        PostCommentController::class,
        "store",
    ]);
    Route::middleware(EnsureTokenIsValid::class)->put("/{commentID}", [
        PostCommentController::class,
        "update",
    ]);
    Route::middleware(EnsureTokenIsValid::class)->delete("/{commentID}", [
        PostCommentController::class,
        "delete",
    ]);
});
Route::prefix("user")->group(function () {
    Route::prefix("follower")->group(function () {
        Route::get("/", [UserFollowerController::class, "index"]);
        Route::middleware(EnsureTokenIsValid::class)->post("/", [
            UserFollowerController::class,
            "store",
        ]);
        Route::middleware(EnsureTokenIsValid::class)->delete("/{userID}", [
            UserFollowerController::class,
            "delete",
        ]);
        Route::middleware(EnsureTokenIsValid::class)->get("/check/{userID}", [
            UserFollowerController::class,
            "check",
        ]);
    });
    Route::get("/{username}", [UserController::class, "getByUsername"]);
    Route::get("/", [UserController::class, "list"]);
});
Route::middleware(EnsureTokenIsValid::class)->put("/{postID}", [
    PostController::class,
    "update",
]);
Route::get("/public/{slug}", [PostController::class, "showBySlug"]);
Route::get("/{postID}", [PostController::class, "show"]);
Route::get("/", [PostController::class, "index"]);
Route::middleware(EnsureTokenIsValid::class)->delete("/{postID}", [
    PostController::class,
    "delete",
]);
Route::middleware(EnsureTokenIsValid::class)->post("/", [
    PostController::class,
    "store",
]);
