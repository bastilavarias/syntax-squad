<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\FormRequest;

class ListPostsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "page" => "nullable|integer",
            "per_page" => "nullable|integer",
            "sort_by" => "required|string|in:relevance,top,latest,touched",
            "user_id" => "nullable|exists:users,id",
            "search" => "nullable|string",
            "is_draft" => "nullable|integer",
        ];
    }
}
