<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGenreRequest extends FormRequest
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
            'name_hu' => ['required', 'string', 'max:255', 'unique:genres,name_hu'],
            'name_en' => ['required', 'string', 'max:255', 'unique:genres,name_en']
        ];
    }
}
