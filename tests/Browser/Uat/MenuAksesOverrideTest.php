<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class MenuAksesOverrideTest extends UatBase
{
    public function test_tu_buka_halaman_pegawai(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/pegawai')
                ->assertSee('Pegawai');
        });
    }

    public function test_tu_buka_edit_pegawai_guru(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/pegawai')
                ->assertSee('Pak Budi');
        });
    }

    public function test_sidebar_guru_muncul_menu(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->assertSee('Dashboard')
                ->assertSee('Penilaian')
                ->assertSee('P5')
                ->assertSee('Prakerin');
        });
    }
}
