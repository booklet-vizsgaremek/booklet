<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'book_id' => ['required_if:genre_id,null', 'uuid', 'exists:books,id'],
            'genre_id' => ['required_if:book_id,null', 'uuid', 'exists:genres,id'],
            'user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'discount' => ['required', 'integer', 'min:0', 'max:100'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'code' => ['nullable', 'string', 'max:255', 'unique:coupons,code']
        ];
    }
}
