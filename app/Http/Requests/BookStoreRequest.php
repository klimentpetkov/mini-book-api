<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="BookStoreRequest",
 *   required={"title", "author", "published_at"},
 *   @OA\Property(property="title", type="string", example="Clean Code"),
 *   @OA\Property(property="author", type="string", example="Robert C. Martin"),
 *   @OA\Property(property="published_at", type="string", format="date", example="2008-08-01")
 * )
 */
class BookStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
    * @return array<string, array<int, string>>
    */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'author'       => ['required', 'string', 'max:255'],
            'published_at' => ['required', 'date'],
        ];
    }
}
