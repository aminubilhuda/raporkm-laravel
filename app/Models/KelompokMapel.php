<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'keterangan'])]
class KelompokMapel extends Model
{
    use HasFactory;

    protected $table = 'kelompok_mapel';

    public function mapel()
    {
        return $this->hasMany(Mapel::class);
    }
}
