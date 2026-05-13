<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UploadProofRequest
 * Siswa upload bukti bayar manual
 */
class UploadProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'proof_of_payment' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:3072', // max 3MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_of_payment.required' => 'Bukti pembayaran wajib diupload.',
            'proof_of_payment.image'    => 'File harus berupa gambar.',
            'proof_of_payment.mimes'    => 'Format file harus JPG atau PNG.',
            'proof_of_payment.max'      => 'Ukuran file maksimal 3MB.',
        ];
    }
}
