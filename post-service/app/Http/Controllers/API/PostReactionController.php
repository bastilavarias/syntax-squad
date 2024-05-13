<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\Reaction\CreatePostReactionRequest;
use App\Services\PostReactionService;
use Exception;
use Illuminate\Http\Request;

class PostReactionController extends Controller
{
    public function store(
        CreatePostReactionRequest $request,
        PostReactionService $service
    ) {
        try {
            $data = $service->create($request->all());
            return customResponse()
                ->data($data)
                ->message("API request done.")
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function delete(
        Request $request,
        PostReactionService $service,
        $postID
    ) {
        try {
            $payload = $request->all();
            $payload["post_id"] = $postID;
            $data = $service->delete($payload);
            return customResponse()
                ->data($data)
                ->message("API request done.")
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }

    public function check(
        Request $request,
        PostReactionService $service,
        $postID
    ) {
        try {
            $payload = $request->all();
            $payload["post_id"] = $postID;
            $data = $service->check($payload);
            return customResponse()
                ->data($data)
                ->message("API request done.")
                ->success()
                ->generate();
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed()
                ->generate();
        }
    }
}
