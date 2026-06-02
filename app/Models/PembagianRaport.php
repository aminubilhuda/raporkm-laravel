<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tahun_pelajaran_id', 'semester_id', 'tanggal_mid', 'tanggal_semester'])]
class PembagianRaport extends Model
{
    protected $table = 'pembagian_raport';

    protected function casts(): array
    {
        return [
            'tanggal_mid' => 'date',
            'tanggal_semester' => 'date',
        ];
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
