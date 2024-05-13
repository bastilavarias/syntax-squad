<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\Bookmark\CreatePostBookmarkRequest;
use App\Services\PostBookmarkService;
use Exception;
use Illuminate\Http\Request;

class PostBookmarkController extends Controller
{
    public function store(
        CreatePostBookmarkRequest $request,
        PostBookmarkService $service
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
        PostBookmarkService $service,
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
        PostBookmarkService $service,
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

    public function index(Request $request, PostBookmarkService $service)
    {
        try {
            $data = $service->list($request->all());
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
