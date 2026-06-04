<?php

namespace App\Http\Requests\TU\Prestasi;

use Illuminate\Foundation\Http\FormRequest;

class StorePrestasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => 'required|integer|exists:siswa,id',
            'nama_prestasi' => 'required|string|max:200',
            'tingkat' => 'nullable|string|max:50',
            'penyelenggara' => 'nullable|string|max:200',
            'tahun' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'keterangan' => 'nullable|string',
        ];
    }
}
