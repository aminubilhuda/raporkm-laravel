<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class KepsekTtdTest extends UatBase
{
    public function test_kepsek_login_dan_lihat_dashboard(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsKepsek($browser);
            $browser->assertSee('Dashboard')
                ->assertSee('Kepsek');
        });
    }

    public function test_kepsek_buka_panel_guru(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsKepsek($browser);
            $browser->visit('/guru/kelas-ku')
                ->assertSee('Kelas Saya');
        });
    }

    public function test_kepsek_buka_halaman_presensi(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsKepsek($browser);
            $browser->visit('/guru/presensi')
                ->assertSee('Presensi');
        });
    }
}
