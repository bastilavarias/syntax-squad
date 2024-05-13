<?php
namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest as BaseRequest;

use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class FormRequest extends BaseRequest
{
    public function cannotBeGuest()
    {
        if (Auth::guard('api')->check()) {
            return true;
        } else {
            return false;
        }
    }

    public function mustBeGuest()
    {
        if (Auth::guard('api')->check()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Implement your Admin-checker
     */
    public function mustBeAdmin()
    {
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();

            return true;
        } else {
            return false;
        }
    }
    protected function failedAuthorization()
    {
        throw new UnauthorizedException();
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }
}
