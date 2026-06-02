<?php

namespace App\Http\Requests\TU\Kokurikuler;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDimensiKokurikulerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:200',
            'keterangan' => 'nullable|string',
        ];
    }
}
