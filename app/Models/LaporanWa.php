<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tujuan', 'pesan', 'status', 'respon', 'dikirim_pada'])]
class LaporanWa extends Model
{
    protected $table = 'laporan_wa';

    protected function casts(): array
    {
        return [
            'dikirim_pada' => 'datetime',
        ];
    }
}
