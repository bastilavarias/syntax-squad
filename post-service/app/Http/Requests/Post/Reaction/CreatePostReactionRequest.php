<?php

namespace App\Http\Requests\Post\Reaction;

use App\Http\Requests\FormRequest;

class CreatePostReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "post_id" => "required|exists:posts,id",
            "name" => "required|string|in:heart,bookmark",
        ];
    }
}
