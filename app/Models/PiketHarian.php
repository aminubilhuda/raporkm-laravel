<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'hari_id', 'tahun_pelajaran_id', 'semester_id'])]
class PiketHarian extends Model
{
    protected $table = 'piket_harian';

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hari()
    {
        return $this->belongsTo(RefHari::class, 'hari_id');
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
