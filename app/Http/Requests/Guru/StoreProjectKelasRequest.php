<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'kelas_id' => 'required|exists:kelas,id',
            'proyek_tema_id' => 'required|exists:proyek_tema,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ];
    }
}
