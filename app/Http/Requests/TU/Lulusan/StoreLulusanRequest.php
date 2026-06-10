<?php

namespace App\Http\Requests\TU\Lulusan;

use Illuminate\Foundation\Http\FormRequest;

class StoreLulusanRequest extends FormRequest
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
            'tahun_pelajaran_id' => 'nullable|integer|exists:tahun_pelajaran,id',
            'tanggal_lulus' => 'nullable|date',
            'no_ijazah' => 'nullable|string|max:50|unique:lulusan,no_ijazah',
            'lanjut_ke' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
        ];
    }
}
