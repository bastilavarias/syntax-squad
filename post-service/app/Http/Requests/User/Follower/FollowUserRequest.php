<?php

namespace App\Http\Requests\User\Follower;

use App\Http\Requests\FormRequest;

class FollowUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "user_id" => "required|exists:users,id",
        ];
    }
}
