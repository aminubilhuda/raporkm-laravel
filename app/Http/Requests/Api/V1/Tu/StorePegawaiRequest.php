<?php

namespace App\Http\Requests\Api\V1\Tu;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'jabatan' => ['required', 'integer', 'in:2,3,4'],
            'kontak' => ['nullable', 'string', 'max:255'],
            'id_tugas_tambahan' => ['nullable', 'exists:ref_tugas_tambahan,id'],
            'nip' => ['nullable', 'string', 'max:50'],
            'nuptk' => ['nullable', 'string', 'max:50'],
            'nik' => ['nullable', 'string', 'max:50'],
            'kelamin' => ['nullable', 'integer', 'in:1,2'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'integer', 'exists:ref_agama,id'],
            'pendidikan_terakhir' => ['nullable', 'integer', 'exists:ref_pendidikan,id'],
            'status_kepegawaian' => ['nullable', 'integer', 'exists:ref_kepegawaian,id'],
        ];
    }
}
