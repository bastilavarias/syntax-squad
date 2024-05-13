<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\Comment\CreatePostCommentRequest;
use App\Http\Requests\Post\Comment\ListPostCommentsRequest;
use App\Http\Requests\Post\Comment\UpdatePostCommentRequest;
use App\Services\PostCommentService;
use Exception;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    public function store(
        CreatePostCommentRequest $request,
        PostCommentService $service
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

    public function index(
        ListPostCommentsRequest $request,
        PostCommentService $service
    ) {
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

    public function update(
        UpdatePostCommentRequest $request,
        PostCommentService $service,
        $commentID
    ) {
        try {
            $payload = $request->all();
            $payload["comment_id"] = $commentID;
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

    public function delete(
        Request $request,
        PostCommentService $service,
        $commentID
    ) {
        try {
            $payload = $request->all();
            $payload["comment_id"] = $commentID;
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
