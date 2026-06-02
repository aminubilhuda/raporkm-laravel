<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['proyek_kelas_id', 'sub_elemen_id'])]
class ProyekSubelemen extends Model
{
    protected $table = 'proyek_subelemen';

    use SoftDeletes;

    public function proyekKelas()
    {
        return $this->belongsTo(ProyekKelas::class);
    }

    public function subElemen()
    {
        return $this->belongsTo(SubElemen::class);
    }
}
