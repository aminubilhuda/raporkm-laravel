<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['dimensi_kokurikuler_id', 'predikat', 'deskripsi'])]
class DeskripsiKokurikuler extends Model
{
    protected $table = 'deskripsi_kokurikuler';

    use HasFactory, SoftDeletes;

    public function dimensiKokurikuler()
    {
        return $this->belongsTo(DimensiKokurikuler::class);
    }
}
