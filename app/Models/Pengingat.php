<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['judul', 'pesan', 'untuk_role', 'tanggal', 'waktu', 'dikirim'])]
class Pengingat extends Model
{
    protected $table = 'pengingat';

    use HasFactory;

    protected function casts(): array
    {
        return [
            'untuk_role' => 'integer',
            'dikirim' => 'integer',
            'tanggal' => 'date',
            'waktu' => 'datetime',
        ];
    }
}
