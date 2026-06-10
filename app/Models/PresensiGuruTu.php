<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id', 'tanggal', 'tahun_pelajaran_id', 'semester_id',
    'check_in', 'latitude_in', 'longitude_in', 'foto_selfie_in', 'status_check_in',
    'check_out', 'latitude_out', 'longitude_out', 'foto_selfie_out', 'status_check_out',
    'keterangan',
])]
class PresensiGuruTu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'presensi_guru_tu';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date:Y-m-d',
            'check_in' => 'datetime:H:i:s',
            'check_out' => 'datetime:H:i:s',
            'latitude_in' => 'decimal:7',
            'longitude_in' => 'decimal:7',
            'latitude_out' => 'decimal:7',
            'longitude_out' => 'decimal:7',
        ];
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
}
