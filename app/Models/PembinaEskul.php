<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['eskul_id', 'user_id', 'tahun_pelajaran_id'])]
class PembinaEskul extends Model
{
    use HasFactory;

    protected $table = 'pembina_eskul';

    public function eskul()
    {
        return $this->belongsTo(Eskul::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
