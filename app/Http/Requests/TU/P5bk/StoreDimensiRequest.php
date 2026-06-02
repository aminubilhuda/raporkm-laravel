<?php

namespace App\Http\Requests\TU\P5bk;

use Illuminate\Foundation\Http\FormRequest;

class StoreDimensiRequest extends FormRequest
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
            'urutan' => 'nullable|integer|min:0',
        ];
    }
}
