<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['siswa_kelas_id', 'mapel_kelas_id', 'nilai', 'deskripsi', 'predikat'])]
class NilaiMataPelajaran extends Model
{
    protected $table = 'nilai_mata_pelajaran';

    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    public function siswaKelas()
    {
        return $this->belongsTo(SiswaKelas::class);
    }

    public function mapelKelas()
    {
        return $this->belongsTo(MapelKelas::class);
    }
}
