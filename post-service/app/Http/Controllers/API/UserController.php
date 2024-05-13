<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
    public function list(Request $request, UserService $service)
    {
        try {
            $payload = $request->all();
            $data = $service->list($payload);
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

    public function getByUsername(UserService $service, $username)
    {
        try {
            $payload = [
                "username" => $username,
            ];
            $data = $service->getByUsername($payload);
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
