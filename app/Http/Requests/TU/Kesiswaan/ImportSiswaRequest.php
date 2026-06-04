<?php

namespace App\Http\Requests\TU\Kesiswaan;

use Illuminate\Foundation\Http\FormRequest;

class ImportSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,csv|max:5120',
            'kelas_id' => 'required|integer|exists:kelas,id',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'File harus berupa XLSX atau CSV.',
            'file.max' => 'File maksimal 5MB.',
            'kelas_id.required' => 'Pilih kelas tujuan.',
        ];
    }
}
