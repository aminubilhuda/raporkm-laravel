<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'kktp', 'predikat', 'deskripsi'])]
class DeskripsiRapor extends Model
{
    use HasFactory;

    protected $table = 'deskripsi_rapor';

    protected function casts(): array
    {
        return [
            'kktp' => 'integer',
        ];
    }
}
