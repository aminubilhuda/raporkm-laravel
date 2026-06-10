<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreNilaiPrakerinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mapel_id' => 'required|array|min:1',
            'mapel_id.*' => 'exists:mapel,id',
            'nilai' => 'required|array',
            'nilai.*' => 'nullable|integer|min:0|max:100',
            'deskripsi' => 'nullable|array',
            'deskripsi.*' => 'nullable|string|max:500',
        ];
    }
}
