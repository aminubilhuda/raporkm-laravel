<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['proyek_kelas_id', 'sub_elemen_id', 'siswa_id', 'nilai', 'deskripsi', 'tahun_pelajaran_id', 'semester_id'])]
class NilaiAssesmenSubelemen extends Model
{
    protected $table = 'nilai_assesmen_subelemen';

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

    public function subElemen()
    {
        return $this->belongsTo(SubElemen::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
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
