<?php

namespace App\Http\Requests\Post\Bookmark;

use App\Http\Requests\FormRequest;

class CreatePostBookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "post_id" => "required|exists:posts,id",
        ];
    }
}
