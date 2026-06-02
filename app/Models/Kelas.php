<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'tingkat_id', 'kompetensi_keahlian_id', 'nama_kelas',
    'tahun_pelajaran_id', 'semester_id',
])]
class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class);
    }

    public function kompetensiKeahlian()
    {
        return $this->belongsTo(KompetensiKeahlian::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function siswaKelas()
    {
        return $this->hasMany(SiswaKelas::class);
    }

    public function wali()
    {
        return $this->hasMany(KelasWali::class);
    }

    public function mapelKelas()
    {
        return $this->hasMany(MapelKelas::class);
    }
}
