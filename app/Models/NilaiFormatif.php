<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tahun_pelajaran_id', 'semester_id', 'kelas_id', 'mapel_id', 'tujuan_pembelajaran_id', 'siswa_id', 'nilai', 'middle', 'nas'])]
class NilaiFormatif extends Model
{
    use HasFactory;

    protected $table = 'nilai_formatif';

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
            'middle' => 'integer',
            'nas' => 'integer',
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

    public function tujuanPembelajaran()
    {
        return $this->belongsTo(TujuanPembelajaran::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
