<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'keterangan'])]
class RefKurikulum extends Model
{
    protected $table = 'ref_kurikulum';
}
