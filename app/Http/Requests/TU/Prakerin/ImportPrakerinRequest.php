<?php

namespace App\Http\Requests\TU\Prakerin;

use Illuminate\Foundation\Http\FormRequest;

class ImportPrakerinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,csv|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'File harus berupa XLSX atau CSV.',
            'file.max' => 'File maksimal 5MB.',
        ];
    }
}
