<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StorePenilaianKokurikulerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'kelas_id' => 'required|exists:kelas,id',
            'siswa_id' => 'required|array',
            'siswa_id.*' => 'exists:siswa,id',
            'dimensi_id' => 'required|exists:dimensi_kokurikuler,id',
            'nilai.*' => 'nullable|integer|min:0|max:100',
        ];
    }
}
