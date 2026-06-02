<?php

namespace App\Http\Requests\TU\P5bk;

use Illuminate\Foundation\Http\FormRequest;

class StoreTemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_tema' => 'required|string|max:200',
            'keterangan' => 'nullable|string',
            'tahun_pelajaran_id' => 'required|integer|exists:tahun_pelajaran,id',
            'semester_id' => 'required|integer|exists:semester,id',
        ];
    }
}
