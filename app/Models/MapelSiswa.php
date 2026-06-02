<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['siswa_id', 'mapel_kelas_id'])]
class MapelSiswa extends Model
{
    protected $table = 'mapel_siswa';

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mapelKelas()
    {
        return $this->belongsTo(MapelKelas::class);
    }
}
