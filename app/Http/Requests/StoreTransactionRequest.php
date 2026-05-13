<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreTransactionRequest
 * Admin membuat tagihan baru
 */
class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id'       => ['required', 'integer', 'exists:users,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'       => 'Siswa wajib dipilih.',
            'user_id.exists'         => 'Siswa tidak ditemukan.',
            'department_id.required' => 'Jurusan wajib dipilih.',
            'department_id.exists'   => 'Jurusan tidak ditemukan.',
        ];
    }
}
