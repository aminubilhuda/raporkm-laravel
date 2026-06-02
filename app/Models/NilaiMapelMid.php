<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['siswa_id', 'kelas_id', 'mapel_id', 'tahun_pelajaran_id', 'semester_id', 'nilai', 'deskripsi', 'kktp', 'predikat'])]
class NilaiMapelMid extends Model
{
    protected $table = 'nilai_mapel_mid';

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

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
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
