<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nomor', 'tanggal', 'pengirim', 'perihal', 'keterangan', 'file'])]
class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}
