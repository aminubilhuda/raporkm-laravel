<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'dapodik_id', 'npsn', 'nama_sekolah', 'id_jenjang', 'bentuk_sekolah', 'yayasan',
    'website', 'alamat', 'email', 'kontak', 'desa', 'kecamatan',
    'kabupaten', 'provinsi', 'logo_prov', 'logo', 'favicon', 'gambar1', 'lokasi',
    'visi', 'misi', 'frame_peta', 'tahun_aktif', 'semester_aktif', 'latitude', 'longitude',
    'radius_absen', 'jam_masuk', 'jam_pulang',
])]
class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';

    public function getRouteKeyName(): string
    {
        return 'npsn';
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_aktif');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_aktif');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon ? asset('storage/' . $this->favicon) : null;
    }
}
