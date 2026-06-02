<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama'])]
class RefJenisKeluar extends Model
{
    protected $table = 'ref_jenis_keluar';

    use HasFactory;
}
