<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama_perusahaan', 'alamat', 'kontak', 'PIC', 'tahun_pelajaran_id', 'semester_id', 'tanggal_mulai', 'tanggal_selesai', 'keterangan'])]
class Prakerin extends Model
{
    use HasFactory;

    protected $table = 'prakerin';

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
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

    public function siswaPrakerin()
    {
        return $this->hasMany(SiswaPrakerin::class);
    }
}
