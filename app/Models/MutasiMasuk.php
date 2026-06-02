<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['siswa_id', 'kelas_id', 'asal_sekolah', 'tanggal_masuk', 'alasan', 'tahun_pelajaran_id', 'semester_id'])]
class MutasiMasuk extends Model
{
    protected $table = 'mutasi_masuk';

    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
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

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
