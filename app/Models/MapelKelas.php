<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['mapel_id', 'kelas_id', 'user_id', 'tahun_pelajaran_id', 'semester_id', 'kkm'])]
class MapelKelas extends Model
{
    use HasFactory;

    protected $table = 'mapel_kelas';

    protected function casts(): array
    {
        return [
            'kkm' => 'integer',
        ];
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
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
}
