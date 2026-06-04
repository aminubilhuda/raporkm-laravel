<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'nama_siswa', 'nik_pd', 'nkk', 'nisn', 'nis', 'tempat_lahir',
    'tanggal_lahir', 'kelamin', 'agama', 'kontak_siswa',
    'hub_keluarga', 'jumlah_saudara', 'anak_ke', 'nama_ayah',
    'nik_ayah', 'tahun_ayah', 'pendidikan_ayah', 'pekerjaan_ayah',
    'kontak_ayah', 'nama_ibu', 'nik_ibu', 'tahun_ibu',
    'pendidikan_ibu', 'pekerjaan_ibu', 'kontak_ibu', 'alamat',
    'alamat_orang_tua', 'nama_wali', 'alamat_wali', 'pekerjaan_wali',
    'kontak_wali', 'terima_tingkat', 'jurusan', 'sekolah_asal',
    'terima_tanggal', 'terima_kelas', 'foto', 'dapodik_pd_id', 'jenis_siswa', 'aktif',
])]
class Siswa extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'siswa';

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'terima_tanggal' => 'date',
            'aktif' => 'integer',
        ];
    }

    public function siswaKelas()
    {
        return $this->hasMany(SiswaKelas::class);
    }

    public function mapelSiswa()
    {
        return $this->hasMany(MapelSiswa::class);
    }

    public function kompetensiKeahlian()
    {
        return $this->belongsTo(KompetensiKeahlian::class, 'jurusan');
    }

    public function eskuls()
    {
        return $this->belongsToMany(Eskul::class, 'siswa_eskul')
            ->withTimestamps();
    }

    public function prakerin()
    {
        return $this->hasMany(SiswaPrakerin::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function catatanWali()
    {
        return $this->hasMany(CatatanWali::class);
    }

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class);
    }

    public function nilaiFormatif()
    {
        return $this->hasMany(NilaiFormatif::class);
    }

    public function nilaiSumatifPh()
    {
        return $this->hasMany(NilaiSumatifPh::class);
    }
}
