<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $roleLabels = [2 => 'Tata Usaha', 3 => 'Guru', 4 => 'Kepala Sekolah'];

        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'jabatan' => $this->jabatan,
            'jabatan_label' => $roleLabels[$this->jabatan] ?? 'Unknown',
            'foto_url' => $this->foto ? asset('storage/'.$this->foto) : null,
            'kontak' => $this->kontak,
            'ptk' => new PtkResource($this->whenLoaded('ptk')),
        ];
    }
}
