<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\FormRequest;

class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title" => "required|string|min:3|max:120",
            "content" => "required|string",
            "tags" => "nullable|array",
            "tags.*" => "required|string|min:3|max:25",
            "is_draft" => "required|integer|in:1,0",
            "cover_image" =>
                "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1000", // 1MB only
        ];
    }
}
