<?php

namespace App\Http\Requests\TU\Mutasi;

use Illuminate\Foundation\Http\FormRequest;

class StoreMutasiKeluarRequest extends FormRequest
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
            'tujuan_sekolah' => 'nullable|string|max:200',
            'tanggal_keluar' => 'required|date',
            'alasan' => 'nullable|string',
            'jenis_keluar_id' => 'nullable|integer|exists:ref_jenis_keluar,id',
            'tahun_pelajaran_id' => 'required|integer|exists:tahun_pelajaran,id',
            'semester_id' => 'required|integer|exists:semester,id',
        ];
    }
}
