<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['proyek_kelas_id', 'mapel_id'])]
class MapelProyek extends Model
{
    protected $table = 'mapel_proyek';

    use SoftDeletes;

    public function proyekKelas()
    {
        return $this->belongsTo(ProyekKelas::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
}
