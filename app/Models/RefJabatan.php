<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama'])]
class RefJabatan extends Model
{
    protected $table = 'ref_jabatan';
}
