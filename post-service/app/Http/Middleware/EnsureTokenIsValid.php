<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $apiGatewayEndpoint = config("microservice.endpoint.api_gateway");
            $response = Http::withHeaders([
                "Authorization" => $request->header("Authorization"),
            ])->get("$apiGatewayEndpoint/auth/api/check");
            if (!$response->ok()) {
                throw new Exception(
                    "You do not have the necessary permission to access this resource."
                );
            }
            $request->merge(["auth_user" => $response->json("data")["user"]]);
        } catch (Exception $e) {
            return customResponse()
                ->data([])
                ->message($e->getMessage())
                ->failed(403)
                ->generate();
        }
        return $next($request);
    }
}
