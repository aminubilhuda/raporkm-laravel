<?php

namespace App\Http\Requests\Api\V1\Tu;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_siswa' => ['required', 'string', 'max:255'],
            'nisn' => ['nullable', 'string', 'max:50', 'unique:siswa,nisn'],
            'nis' => ['nullable', 'string', 'max:50', 'unique:siswa,nis'],
            'nik_pd' => ['nullable', 'string', 'max:50'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'kelamin' => ['required', 'integer', 'in:1,2'],
            'agama' => ['nullable', 'integer', 'exists:ref_agama,id'],
            'kontak_siswa' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'sekolah_asal' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'integer', 'exists:kompetensi_keahlian,id'],
            'jenis_siswa' => ['nullable', 'integer', 'exists:ref_jenis_siswa,id'],
        ];
    }
}
