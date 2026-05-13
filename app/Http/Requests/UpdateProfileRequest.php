<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateProfileRequest
 * Siswa update biodata sendiri
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'scan_ijazah' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ];
    }
}
