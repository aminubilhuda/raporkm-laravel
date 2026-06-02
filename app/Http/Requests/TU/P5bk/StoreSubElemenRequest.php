<?php

namespace App\Http\Requests\TU\P5bk;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubElemenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'elemen_id' => 'required|integer|exists:elemen,id',
            'nama' => 'required|string|max:200',
            'capaian' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
        ];
    }
}
