<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama'])]
class RefTugasTambahan extends Model
{
    protected $table = 'ref_tugas_tambahan';
}
