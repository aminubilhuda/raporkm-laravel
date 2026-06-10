<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['prakerin_id', 'siswa_id', 'kelas_id', 'user_id', 'tahun_pelajaran_id', 'semester_id', 'status'])]
class SiswaPrakerin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswa_prakerin';

    public function prakerin()
    {
        return $this->belongsTo(Prakerin::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function nilaiPrakerin()
    {
        return $this->hasMany(NilaiPrakerin::class);
    }
}
