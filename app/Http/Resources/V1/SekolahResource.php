<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SekolahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'npsn' => $this->npsn,
            'nama_sekolah' => $this->nama_sekolah,
            'alamat' => $this->alamat,
            'email' => $this->email,
            'kontak' => $this->kontak,
            'website' => $this->website,
            'visi' => $this->visi,
            'misi' => $this->misi,
            'logo_url' => $this->logo ? asset('storage/'.$this->logo) : null,
            'favicon_url' => $this->favicon ? asset('storage/'.$this->favicon) : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius_absen' => $this->radius_absen,
            'jam_masuk' => $this->jam_masuk,
            'jam_pulang' => $this->jam_pulang,
            'tahun_aktif' => $this->whenLoaded('tahunPelajaran'),
            'semester_aktif' => $this->whenLoaded('semester'),
        ];
    }
}
