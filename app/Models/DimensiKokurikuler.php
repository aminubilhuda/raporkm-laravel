<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama', 'keterangan'])]
class DimensiKokurikuler extends Model
{
    protected $table = 'dimensi_kokurikuler';

    use HasFactory, SoftDeletes;

    public function deskripsiKokurikuler()
    {
        return $this->hasMany(DeskripsiKokurikuler::class);
    }
}
