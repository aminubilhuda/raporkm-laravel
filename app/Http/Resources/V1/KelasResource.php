<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KelasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_kelas' => $this->nama_kelas,
            'tingkat_id' => $this->tingkat_id,
            'kompetensi_keahlian_id' => $this->kompetensi_keahlian_id,
            'tahun_pelajaran_id' => $this->tahun_pelajaran_id,
            'semester_id' => $this->semester_id,
            'tingkat' => $this->whenLoaded('tingkat', fn () => [
                'id' => $this->tingkat->id,
                'nama' => $this->tingkat->nama,
                'angka' => $this->tingkat->angka,
                'fase' => $this->tingkat->fase,
            ]),
            'kompetensi_keahlian' => $this->whenLoaded('kompetensiKeahlian', fn () => [
                'id' => $this->kompetensiKeahlian->id,
                'nama' => $this->kompetensiKeahlian->nama,
                'singkatan' => $this->kompetensiKeahlian->singkatan,
            ]),
            'tahun_pelajaran' => $this->whenLoaded('tahunPelajaran', fn () => [
                'id' => $this->tahunPelajaran->id,
                'tahun' => $this->tahunPelajaran->tahun,
            ]),
            'semester' => $this->whenLoaded('semester', fn () => [
                'id' => $this->semester->id,
                'nama' => $this->semester->nama,
            ]),
            'jumlah_siswa' => $this->whenCounted('siswaKelas'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
