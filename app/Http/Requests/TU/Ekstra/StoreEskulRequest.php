<?php

namespace App\Http\Requests\TU\Ekstra;

use Illuminate\Foundation\Http\FormRequest;

class StoreEskulRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_eskul' => 'required|string|max:200',
            'keterangan' => 'nullable|string',
        ];
    }
}
