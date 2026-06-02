<?php

namespace App\Http\Requests\TU\P5bk;

use Illuminate\Foundation\Http\FormRequest;

class StoreProyekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kelas_id' => 'required|integer|exists:kelas,id',
            'proyek_tema_id' => 'required|integer|exists:proyek_tema,id',
            'judul' => 'nullable|string|max:200',
            'deskripsi' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
            'tahun_pelajaran_id' => 'required|integer|exists:tahun_pelajaran,id',
            'semester_id' => 'required|integer|exists:semester,id',
        ];
    }
}
