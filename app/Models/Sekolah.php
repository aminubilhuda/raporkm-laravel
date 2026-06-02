<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'npsn', 'nama_sekolah', 'id_jenjang', 'bentuk_sekolah', 'yayasan',
    'website', 'alamat', 'email', 'kontak', 'desa', 'kecamatan',
    'kabupaten', 'provinsi', 'logo_prov', 'logo', 'gambar1', 'lokasi',
    'visi', 'misi', 'frame_peta', 'tahun_aktif', 'semester_aktif',
])]
class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';
}
