<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'img_path' => ['sometimes', 'nullable', 'string', 'max:255'],
            'title' => ['sometimes', 'string', 'max:255'],
            'author_ids' => ['sometimes', 'array', 'min:1'],
            'author_ids.*' => ['uuid', 'exists:authors,id'],
            'price' => ['sometimes', 'integer', 'min:0'],
            'pages' => ['sometimes', 'integer', 'min:1'],
            'release_year' => ['sometimes', 'integer', 'min:1800', 'max:' . now()->year],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'publisher_id' => ['sometimes', 'uuid', 'exists:publishers,id'],
            'genre_id' => ['sometimes', 'uuid', 'exists:genres,id']
        ];
    }
}
