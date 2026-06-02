<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['kelas_id', 'mapel_id', 'tahun_pelajaran_id', 'semester_id', 'rata_rata', 'nilai_tertinggi', 'nilai_terendah'])]
class NilaiKelas extends Model
{
    protected $table = 'nilai_kelas';

    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'rata_rata' => 'decimal:5,2',
            'nilai_tertinggi' => 'integer',
            'nilai_terendah' => 'integer',
        ];
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
