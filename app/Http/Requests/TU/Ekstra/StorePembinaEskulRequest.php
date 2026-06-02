<?php

namespace App\Http\Requests\TU\Ekstra;

use Illuminate\Foundation\Http\FormRequest;

class StorePembinaEskulRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eskul_id' => 'required|integer|exists:eskul,id',
            'user_id' => 'required|integer|exists:users,id',
            'tahun_pelajaran_id' => 'nullable|integer|exists:tahun_pelajaran,id',
        ];
    }
}
