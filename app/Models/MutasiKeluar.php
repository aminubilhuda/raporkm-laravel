<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['siswa_id', 'kelas_id', 'tujuan_sekolah', 'tanggal_keluar', 'alasan', 'jenis_keluar_id', 'tahun_pelajaran_id', 'semester_id'])]
class MutasiKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mutasi_keluar';

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
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

    public function jenisKeluar()
    {
        return $this->belongsTo(RefJenisKeluar::class, 'jenis_keluar_id');
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
