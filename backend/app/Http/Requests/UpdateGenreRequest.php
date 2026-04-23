<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGenreRequest extends FormRequest
{
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
            'name_hu' => ['sometimes', 'string', 'max:255', 'unique:genres,name_hu,' . $this->route('genre')->id],
            'name_en' => ['sometimes', 'string', 'max:255', 'unique:genres,name_en,' . $this->route('genre')->id]
        ];
    }
}
