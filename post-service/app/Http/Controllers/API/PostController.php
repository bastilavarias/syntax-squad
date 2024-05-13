<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\ListPostsRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Services\PostService;
use Exception;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(CreatePostRequest $request, PostService $service)
    {
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

    public function update(
        UpdatePostRequest $request,
        PostService $service,
        $postID
    ) {
        try {
            $payload = $request->all();
            $payload["post_id"] = $postID;
            $data = $service->update($payload);
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

    public function index(ListPostsRequest $request, PostService $service)
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

    public function show(PostService $service, $postID)
    {
        try {
            $data = $service->getOne($postID);
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

    public function showBySlug(PostService $service, $slug)
    {
        try {
            $data = $service->getOneBySlug($slug);
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

    public function delete(Request $request, PostService $service, $postID)
    {
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
}
