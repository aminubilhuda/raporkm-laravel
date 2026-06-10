<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['siswa_id', 'eskul_id', 'tahun_pelajaran_id', 'predikat', 'keterangan'])]
class SiswaEskul extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswa_eskul';

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function eskul()
    {
        return $this->belongsTo(Eskul::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
