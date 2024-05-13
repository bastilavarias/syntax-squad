<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Follower\FollowUserRequest;
use App\Services\UserFollowerService;
use Exception;
use Illuminate\Http\Request;

class UserFollowerController extends Controller
{
    public function store(
        FollowUserRequest $request,
        UserFollowerService $service
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
        UserFollowerService $service,
        $userID
    ) {
        try {
            $payload = $request->all();
            $payload["user_id"] = $userID;
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
        UserFollowerService $service,
        $userID
    ) {
        try {
            $payload = $request->all();
            $payload["user_id"] = $userID;
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

    public function index(Request $request, UserFollowerService $service)
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
