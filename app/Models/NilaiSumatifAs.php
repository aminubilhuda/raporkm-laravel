<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tahun_pelajaran_id', 'semester_id', 'kelas_id', 'mapel_id', 'siswa_id', 'nilai', 'deskripsi'])]
class NilaiSumatifAs extends Model
{
    use HasFactory;

    protected $table = 'nilai_sumatif_as';

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
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
