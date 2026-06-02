<?php

namespace App\Http\Requests\TU\Mutasi;

use Illuminate\Foundation\Http\FormRequest;

class StoreMutasiMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => 'required|integer|exists:siswa,id',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'asal_sekolah' => 'required|string|max:200',
            'tanggal_masuk' => 'required|date',
            'alasan' => 'nullable|string',
            'tahun_pelajaran_id' => 'required|integer|exists:tahun_pelajaran,id',
            'semester_id' => 'required|integer|exists:semester,id',
        ];
    }
}
