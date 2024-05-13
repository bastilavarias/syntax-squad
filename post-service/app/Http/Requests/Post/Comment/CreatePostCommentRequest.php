<?php

namespace App\Http\Requests\Post\Comment;

use App\Http\Requests\FormRequest;

class CreatePostCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "post_id" => "required|exists:posts,id",
            "content" => "required|string|min:3|max:200",
        ];
    }
}
