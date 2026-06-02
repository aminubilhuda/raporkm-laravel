<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'urutan', 'status'])]
class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'status' => 'integer',
        ];
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
