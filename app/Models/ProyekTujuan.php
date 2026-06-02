<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['proyek_kelas_id', 'dimensi_id', 'tujuan'])]
class ProyekTujuan extends Model
{
    protected $table = 'proyek_tujuan';

    use SoftDeletes;

    public function proyekKelas()
    {
        return $this->belongsTo(ProyekKelas::class);
    }

    public function dimensi()
    {
        return $this->belongsTo(Dimensi::class);
    }
}
