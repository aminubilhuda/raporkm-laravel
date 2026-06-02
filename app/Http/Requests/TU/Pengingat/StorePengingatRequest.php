<?php

namespace App\Http\Requests\TU\Pengingat;

use Illuminate\Foundation\Http\FormRequest;

class StorePengingatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:200',
            'pesan' => 'required|string',
            'untuk_role' => 'required|integer|min:1|max:10',
            'tanggal' => 'required|date',
            'waktu' => 'nullable',
        ];
    }
}
