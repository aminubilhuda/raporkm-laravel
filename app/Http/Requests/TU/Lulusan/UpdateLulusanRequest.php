<?php

namespace App\Http\Requests\TU\Lulusan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLulusanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no_ijazah' => 'nullable|string|max:50|unique:lulusan,no_ijazah,' . $this->route('lulusan')?->id,
            'lanjut_ke' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string',
        ];
    }
}
