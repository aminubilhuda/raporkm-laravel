<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'urutan'])]
class RefBulan extends Model
{
    protected $table = 'ref_bulan';

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }
}
