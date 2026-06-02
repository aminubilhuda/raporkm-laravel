<?php

namespace App\Http\Requests\TU\Prakerin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaPrakerinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prakerin_id' => 'required|integer|exists:prakerin,id',
            'siswa_id' => 'required|integer|exists:siswa,id',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'tahun_pelajaran_id' => 'required|integer|exists:tahun_pelajaran,id',
            'semester_id' => 'required|integer|exists:semester,id',
        ];
    }
}
