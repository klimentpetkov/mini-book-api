<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *   schema="BookUpdateRequest",
 *   description="Fields to update a Book (all optional for PATCH)",
 *   @OA\Property(property="title", type="string", example="Clean Code (2nd Ed)"),
 *   @OA\Property(property="author", type="string", example="Robert C. Martin"),
 *   @OA\Property(property="published_at", type="string", format="date‑time", example="2025‑11‑19T12:27:14Z")
 * )
 */
class BookUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title'        => ['string', 'max:255'],
            'author'       => ['string', 'max:255'],
            'published_at' => ['date'],
        ];

        if ($this->isMethod('put')) {
            foreach ($rules as $field => &$rule) {
                array_unshift($rule, 'required');
            }
        }

        return $rules;
    }
}
