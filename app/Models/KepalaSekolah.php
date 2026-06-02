<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'tahun_pelajaran_id', 'nip', 'nama', 'ttd'])]
class KepalaSekolah extends Model
{
    protected $table = 'kepala_sekolah';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
