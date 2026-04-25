<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'img_path' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'author_ids' => ['required', 'array', 'min:1'],
            'author_ids.*' => ['uuid', 'exists:authors,id'],
            'price' => ['required', 'integer', 'min:0'],
            'pages' => ['required', 'integer', 'min:1'],
            'release_year' => ['required', 'integer', 'min:1800', 'max:' . now()->year],
            'stock' => ['required', 'integer', 'min:0'],
            'publisher_id' => ['required', 'uuid', 'exists:publishers,id'],
            'genre_id' => ['required', 'uuid', 'exists:genres,id'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }
}
