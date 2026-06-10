<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'nama_mapel' => $this->nama_mapel,
            'kkm' => $this->kkm,
            'urutan' => $this->urutan,
            'kelompok_mapel_id' => $this->kelompok_mapel_id,
            'kurikulum_id' => $this->kurikulum_id,
            'kelompok_mapel' => $this->whenLoaded('kelompokMapel', fn () => [
                'id' => $this->kelompokMapel->id,
                'nama' => $this->kelompokMapel->nama,
            ]),
            'kurikulum' => $this->whenLoaded('kurikulum', fn () => [
                'id' => $this->kurikulum->id,
                'nama' => $this->kurikulum->nama,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
