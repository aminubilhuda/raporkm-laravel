<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatatanRaporRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'kelas_id' => 'required|exists:kelas,id',
            'siswa_id' => 'required|exists:siswa,id',
            'catatan' => 'nullable|string|max:1000',
        ];
    }
}
