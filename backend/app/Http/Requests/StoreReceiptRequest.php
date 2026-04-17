<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceiptRequest extends FormRequest
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
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'date' => ['required', 'date'],
            'coupons' => ['nullable', 'array'],
            'coupons.*' => ['exists:coupons,id'],
            'books' => ['required', 'array', 'min:1'],
            'books.*.id' => ['required', 'uuid', 'exists:books,id'],
            'books.*.quantity' => ['required', 'integer', 'min:1']
        ];
    }
}
