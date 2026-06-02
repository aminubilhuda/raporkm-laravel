<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['siswa_id', 'kelas_id', 'dimensi_kokurikuler_id', 'proyek_kelas_id', 'nilai', 'deskripsi', 'tahun_pelajaran_id', 'semester_id'])]
class NilaiKokurikuler extends Model
{
    protected $table = 'nilai_kokurikuler';

    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function dimensiKokurikuler()
    {
        return $this->belongsTo(DimensiKokurikuler::class);
    }

    public function proyekKelas()
    {
        return $this->belongsTo(ProyekKelas::class);
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
