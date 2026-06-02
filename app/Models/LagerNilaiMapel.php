<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tahun_pelajaran_id', 'semester_id', 'kelas_id', 'mapel_id', 'siswa_id', 'rata_formatif', 'rata_sumatif_ph', 'sumatif_as', 'nilai_akhir', 'predikat', 'deskripsi'])]
class LagerNilaiMapel extends Model
{
    protected $table = 'lager_nilai_mapel';

    protected function casts(): array
    {
        return [
            'rata_formatif' => 'decimal:5,2',
            'rata_sumatif_ph' => 'decimal:5,2',
            'sumatif_as' => 'integer',
            'nilai_akhir' => 'decimal:5,2',
        ];
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
