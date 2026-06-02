<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['proyek_kelas_id', 'siswa_id', 'dimensi_id', 'elemen_id', 'nilai', 'deskripsi', 'tahun_pelajaran_id', 'semester_id'])]
class NilaiProyek extends Model
{
    protected $table = 'nilai_proyek';

    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    public function proyekKelas()
    {
        return $this->belongsTo(ProyekKelas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function dimensi()
    {
        return $this->belongsTo(Dimensi::class);
    }

    public function elemen()
    {
        return $this->belongsTo(Elemen::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
