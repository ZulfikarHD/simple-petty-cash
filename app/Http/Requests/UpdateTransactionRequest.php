<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('transaction')?->user_id === $this->user()?->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['sometimes', 'string', 'max:200'],
            'transaction_date' => ['sometimes', 'date', 'before_or_equal:today'],
            'category_id' => ['sometimes', 'exists:categories,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'amount.min' => 'Jumlah minimal adalah 0.01.',
            'description.max' => 'Deskripsi maksimal 200 karakter.',
            'transaction_date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'category_id.exists' => 'Kategori tidak valid.',
        ];
    }
}
