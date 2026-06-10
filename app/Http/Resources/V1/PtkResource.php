<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PtkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nuptk' => $this->nuptk,
            'nip' => $this->nip,
            'nik' => $this->nik,
            'kelamin' => $this->kelamin,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'agama' => $this->agama,
            'pendidikan_terakhir' => $this->pendidikan_terakhir,
            'bidang_studi_terakhir' => $this->bidang_studi_terakhir,
            'pangkat_golongan' => $this->pangkat_golongan,
            'status_kepegawaian' => $this->status_kepegawaian,
        ];
    }
}
