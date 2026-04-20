<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePickupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receipt_id' => ['required', 'uuid', 'exists:receipts,id'],
            'status' => ['sometimes', 'in:pending,ready,completed,cancelled'],
            'completed_at' => ['nullable', 'date']
        ];
    }
}
