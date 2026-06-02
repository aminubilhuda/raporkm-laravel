<?php

namespace App\Http\Requests\TU\Organisasi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganisasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_organisasi' => 'required|string|max:200',
            'keterangan' => 'nullable|string',
        ];
    }
}
