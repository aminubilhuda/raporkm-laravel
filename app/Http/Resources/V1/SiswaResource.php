<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_siswa' => $this->nama_siswa,
            'nisn' => $this->nisn,
            'nis' => $this->nis,
            'nik_pd' => $this->nik_pd,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->toDateString(),
            'kelamin' => $this->kelamin,
            'agama' => $this->agama,
            'kontak_siswa' => $this->kontak_siswa,
            'alamat' => $this->alamat,
            'aktif' => (bool) $this->aktif,
            'jenis_siswa' => $this->jenis_siswa,
            'jurusan' => $this->jurusan,
            'foto_url' => $this->foto ? asset('storage/'.$this->foto) : null,
            'kelas_aktif' => $this->whenLoaded('siswaKelas', function () {
                return $this->siswaKelas
                    ->filter(fn ($sk) => $sk->status === 'aktif')
                    ->map(fn ($sk) => [
                        'id' => $sk->kelas?->id,
                        'nama_kelas' => $sk->kelas?->nama_kelas,
                        'tingkat' => $sk->kelas?->tingkat?->nama,
                        'jurusan' => $sk->kelas?->kompetensiKeahlian?->nama,
                    ])
                    ->values();
            }),
            'nama_ayah' => $this->nama_ayah,
            'nama_ibu' => $this->nama_ibu,
            'sekolah_asal' => $this->sekolah_asal,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
