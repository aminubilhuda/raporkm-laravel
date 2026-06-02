<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama_organisasi', 'keterangan'])]
class Organisasi extends Model
{
    protected $table = 'organisasi';

    use HasFactory, SoftDeletes;
}
