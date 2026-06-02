<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['dimensi_id', 'nama', 'keterangan', 'urutan'])]
class Elemen extends Model
{
    protected $table = 'elemen';

    use HasFactory;

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function dimensi()
    {
        return $this->belongsTo(Dimensi::class);
    }

    public function subElemens()
    {
        return $this->hasMany(SubElemen::class);
    }
}
