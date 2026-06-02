<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTujuanPembelajaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'kode_tp' => 'required|string|max:50',
            'nama_tp' => 'required|string',
        ];
    }
}
