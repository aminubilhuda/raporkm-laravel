<?php

namespace App\Http\Requests\TU\Kokurikuler;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeskripsiKokurikulerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dimensi_kokurikuler_id' => 'required|integer|exists:dimensi_kokurikuler,id',
            'predikat' => 'required|string|in:A,B,C,D',
            'deskripsi' => 'required|string',
        ];
    }
}
