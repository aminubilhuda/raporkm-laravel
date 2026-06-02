<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['elemen_id', 'nama', 'capaian', 'urutan'])]
class SubElemen extends Model
{
    protected $table = 'sub_elemen';

    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function elemen()
    {
        return $this->belongsTo(Elemen::class);
    }
}
