<?php

namespace App\Http\Requests\TU\Prakerin;

use Illuminate\Foundation\Http\FormRequest;

class StorePrakerinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_perusahaan' => 'required|string|max:200',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:50',
            'PIC' => 'nullable|string|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
            'tahun_pelajaran_id' => 'required|integer|exists:tahun_pelajaran,id',
            'semester_id' => 'required|integer|exists:semester,id',
        ];
    }
}
