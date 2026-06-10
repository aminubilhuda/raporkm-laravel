<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mapel_id', 'kelas_id', 'kode_tp', 'nama_tp', 'tahun_pelajaran_id', 'semester_id'])]
class TujuanPembelajaran extends Model
{
    use HasFactory;

    protected $table = 'tujuan_pembelajaran';

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
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

    public function nilaiFormatif()
    {
        return $this->hasMany(NilaiFormatif::class);
    }
}
