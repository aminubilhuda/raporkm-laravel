<?php

namespace App\Http\Requests\TU\P5bk;

use Illuminate\Foundation\Http\FormRequest;

class UpdateElemenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dimensi_id' => 'required|integer|exists:dimensi,id',
            'nama' => 'required|string|max:200',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
        ];
    }
}
