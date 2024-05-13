<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public function render($request)
    {
        return customResponse()
            ->data([])
            ->message(
                "You do not have the necessary permission to access this resource."
            )
            ->failed(403)
            ->generate();
    }
}
