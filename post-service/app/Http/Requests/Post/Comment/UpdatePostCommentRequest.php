<?php

namespace App\Http\Requests\Post\Comment;

use App\Http\Requests\FormRequest;

class UpdatePostCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "content" => "required|string|min:3|max:200",
        ];
    }
}
