<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['sekolah_id', 'nama_eskul', 'keterangan'])]
class Eskul extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'eskul';

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id')->withDefault();
    }

    public function pembinaEskul()
    {
        return $this->hasMany(PembinaEskul::class);
    }

    public function siswaEskul()
    {
        return $this->hasMany(SiswaEskul::class);
    }
}
