<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'singkatan', 'keterangan'])]
class KompetensiKeahlian extends Model
{
    use HasFactory;

    protected $table = 'kompetensi_keahlian';

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
