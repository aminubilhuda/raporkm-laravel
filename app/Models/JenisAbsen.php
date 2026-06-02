<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'keterangan'])]
class JenisAbsen extends Model
{
    protected $table = 'jenis_absen';

    protected function casts(): array
    {
        return [
            'keterangan' => 'string',
        ];
    }
}
