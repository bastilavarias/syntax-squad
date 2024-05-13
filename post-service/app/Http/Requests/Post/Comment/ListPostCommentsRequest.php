<?php

namespace App\Http\Requests\Post\Comment;

use App\Http\Requests\FormRequest;

class ListPostCommentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "post_id" => "required|exists:posts,id",
            "user_id" => "nullable|exists:users,id",
            "page" => "nullable|integer",
            "per_page" => "nullable|integer",
        ];
    }
}
