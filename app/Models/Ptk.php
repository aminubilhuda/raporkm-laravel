<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ptk extends Model
{
    use HasFactory;

    protected $table = 'ptk';

    protected $fillable = [
        'user_id',
        'ptk_id',
        'nuptk',
        'nik',
        'nip',
        'kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pendidikan_terakhir',
        'bidang_studi_terakhir',
        'pangkat_golongan',
        'status_kepegawaian',
        'jenis_ptk',
        'jabatan_ptk',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
