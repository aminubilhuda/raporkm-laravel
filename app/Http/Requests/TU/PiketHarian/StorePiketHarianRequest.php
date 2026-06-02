<?php

namespace App\Http\Requests\TU\PiketHarian;

use Illuminate\Foundation\Http\FormRequest;

class StorePiketHarianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'hari_id' => 'required|integer|exists:ref_hari,id',
            'tahun_pelajaran_id' => 'required|integer|exists:tahun_pelajaran,id',
            'semester_id' => 'required|integer|exists:semester,id',
        ];
    }
}
