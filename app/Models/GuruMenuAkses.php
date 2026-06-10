<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'menu_slug', 'tipe'])]
class GuruMenuAkses extends Model
{
    protected $table = 'guru_menu_akses';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
