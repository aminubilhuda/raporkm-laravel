<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class TuAssignGuruTest extends UatBase
{
    public function test_tu_buka_halaman_mapel_kelas(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/mapel-kelas')
                ->assertSee('Peta Kelas')
                ->assertSee('X TKJ 1');
        });
    }

    public function test_tu_lihat_guru_terassign(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/mapel-kelas')
                ->assertSee('Pak Deni');
        });
    }

    public function test_tu_buka_halaman_pegawai(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/pegawai')
                ->assertSee('Pegawai')
                ->assertSee('Operator TU')
                ->assertSee('Pak Budi');
        });
    }
}
