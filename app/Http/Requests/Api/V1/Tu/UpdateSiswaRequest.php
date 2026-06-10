<?php

namespace App\Http\Requests\Api\V1\Tu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $siswaId = $this->route('id');

        return [
            'nama_siswa' => ['sometimes', 'string', 'max:255'],
            'nisn' => ['sometimes', 'string', 'max:50', 'unique:siswa,nisn,'.$siswaId],
            'nis' => ['sometimes', 'string', 'max:50', 'unique:siswa,nis,'.$siswaId],
            'nik_pd' => ['nullable', 'string', 'max:50'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'kelamin' => ['sometimes', 'integer', 'in:1,2'],
            'agama' => ['nullable', 'integer', 'exists:ref_agama,id'],
            'kontak_siswa' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'sekolah_asal' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'integer', 'exists:kompetensi_keahlian,id'],
            'jenis_siswa' => ['nullable', 'integer', 'exists:ref_jenis_siswa,id'],
            'aktif' => ['sometimes', 'integer', 'in:0,1'],
        ];
    }
}
