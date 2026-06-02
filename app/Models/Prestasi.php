<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['siswa_id', 'nama_prestasi', 'tingkat', 'penyelenggara', 'tahun', 'keterangan'])]
class Prestasi extends Model
{
    protected $table = 'prestasi';

    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
