<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['siswa_prakerin_id', 'mapel_id', 'nilai', 'deskripsi'])]
class NilaiPrakerin extends Model
{
    use HasFactory;

    protected $table = 'nilai_prakerin';

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    public function siswaPrakerin()
    {
        return $this->belongsTo(SiswaPrakerin::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
}
