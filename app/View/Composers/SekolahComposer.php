<?php

namespace App\View\Composers;

use App\Models\Sekolah;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SekolahComposer
{
    public function compose(View $view): void
    {
        if (Schema::hasTable('sekolah')) {
            $sekolah = Sekolah::first();
        } else {
            $sekolah = null;
        }

        $view->with('sekolah', $sekolah);
    }
}
