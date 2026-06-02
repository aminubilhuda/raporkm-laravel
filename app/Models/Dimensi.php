<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nama', 'keterangan', 'urutan'])]
class Dimensi extends Model
{
    protected $table = 'dimensi';

    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function elemens()
    {
        return $this->hasMany(Elemen::class);
    }
}
