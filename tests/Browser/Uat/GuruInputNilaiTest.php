<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class GuruInputNilaiTest extends UatBase
{
    public function test_guru_login_dan_lihat_dashboard(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->assertSee('Dashboard')
                ->assertSee('Panel Guru');
        });
    }

    public function test_guru_buka_halaman_penilaian(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/penilaian')
                ->assertSee('Penilaian');
        });
    }

    public function test_guru_buka_halaman_lager_nilai(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/lager-nilai-kelas')
                ->assertSee('Lager Nilai');
        });
    }

    public function test_guru_buka_halaman_kelas_ku(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/kelas-ku')
                ->assertSee('Kelas Saya');
        });
    }
}
