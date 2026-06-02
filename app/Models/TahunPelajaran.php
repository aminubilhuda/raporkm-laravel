<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tahun', 'status'])]
class TahunPelajaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_pelajaran';

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function siswaKelas()
    {
        return $this->hasMany(SiswaKelas::class);
    }
}
