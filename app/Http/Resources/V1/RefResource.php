<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama ?? $this->nama_mapel ?? $this->nama_eskul ?? null,
            'keterangan' => $this->when(isset($this->keterangan), $this->keterangan),
            'urutan' => $this->when(isset($this->urutan), $this->urutan),
            'singkatan' => $this->when(isset($this->singkatan), $this->singkatan),
            'angka' => $this->when(isset($this->angka), $this->angka),
            'fase' => $this->when(isset($this->fase), $this->fase),
            'tahun' => $this->when(isset($this->tahun), $this->tahun),
            'status' => $this->when(isset($this->status), $this->status),
            'predikat' => $this->when(isset($this->predikat), $this->predikat),
            'deskripsi' => $this->when(isset($this->deskripsi), $this->deskripsi),
            'kktp' => $this->when(isset($this->kktp), $this->kktp),
            'dimensi_id' => $this->when(isset($this->dimensi_id), $this->dimensi_id),
        ];
    }
}
