<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PegawaiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'jabatan' => $this->jabatan,
            'jabatan_label' => match ($this->jabatan) {
                2 => 'Tata Usaha',
                3 => 'Guru',
                4 => 'Kepala Sekolah',
                default => '-',
            },
            'kontak' => $this->kontak,
            'foto_url' => $this->foto ? asset('storage/'.$this->foto) : null,
            'ptk' => new PtkResource($this->whenLoaded('ptk')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
