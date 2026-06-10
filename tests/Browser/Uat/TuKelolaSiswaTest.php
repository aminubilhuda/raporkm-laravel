<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class TuKelolaSiswaTest extends UatBase
{
    public function test_tu_login_dan_lihat_dashboard(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->assertSee('Dashboard')
                ->assertSee('Tata Usaha');
        });
    }

    public function test_tu_buka_halaman_kesiswaan(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/kesiswaan')
                ->assertSee('Kesiswaan')
                ->assertSee('Siswa');
        });
    }

    public function test_tu_lihat_daftar_siswa(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/kesiswaan')
                ->assertSee('Siswa 1')
                ->assertSee('Siswa 30');
        });
    }

    public function test_tu_buka_halaman_rombel(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/rombel')
                ->assertSee('Rombel')
                ->assertSee('X TKJ 1');
        });
    }
}
