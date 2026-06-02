<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['kelas_id', 'proyek_tema_id', 'judul', 'deskripsi', 'tahun_pelajaran_id', 'semester_id', 'user_id'])]
class ProyekKelas extends Model
{
    protected $table = 'proyek_kelas';

    use HasFactory, SoftDeletes;

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function proyekTema()
    {
        return $this->belongsTo(ProyekTema::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subelemens()
    {
        return $this->hasMany(ProyekSubelemen::class);
    }

    public function tujuans()
    {
        return $this->hasMany(ProyekTujuan::class);
    }

    public function mapelProyek()
    {
        return $this->hasMany(MapelProyek::class);
    }

    public function nilaiProyek()
    {
        return $this->hasMany(NilaiProyek::class);
    }
}
