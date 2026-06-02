<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['siswa_id', 'kelas_id', 'tahun_pelajaran_id', 'tanggal_lulus', 'no_ijazah', 'lanjut_ke', 'keterangan'])]
class Lulusan extends Model
{
    protected $table = 'lulusan';

    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal_lulus' => 'date',
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

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
