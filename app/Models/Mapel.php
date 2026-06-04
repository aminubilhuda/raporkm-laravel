<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['dapodik_id', 'kelompok_mapel_id', 'kode', 'nama_mapel', 'urutan', 'kkm', 'kurikulum_id'])]
class Mapel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mapel';

    protected function casts(): array
    {
        return [
            'kkm' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function kelompokMapel()
    {
        return $this->belongsTo(KelompokMapel::class);
    }

    public function kurikulum()
    {
        return $this->belongsTo(RefKurikulum::class, 'kurikulum_id');
    }

    public function tujuanPembelajaran()
    {
        return $this->hasMany(TujuanPembelajaran::class);
    }

    public function mapelKelas()
    {
        return $this->hasMany(MapelKelas::class);
    }
}
