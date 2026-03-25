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
            'name' => ['required', 'string', 'max:255'],
            'author_id' => ['required', 'uuid', 'exists:authors,id'],
            'price' => ['required', 'integer', 'min:0'],
            'pages' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
            'publisher_id' => ['required', 'uuid', 'exists:publishers,id'],
            'genre_id' => ['required', 'uuid', 'exists:genres,id']
        ];
    }
}
