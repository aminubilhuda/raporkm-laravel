<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama_tema', 'keterangan', 'tahun_pelajaran_id', 'semester_id'])]
class ProyekTema extends Model
{
    protected $table = 'proyek_tema';

    use HasFactory, SoftDeletes;

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function proyekKelas()
    {
        return $this->hasMany(ProyekKelas::class);
    }
}
