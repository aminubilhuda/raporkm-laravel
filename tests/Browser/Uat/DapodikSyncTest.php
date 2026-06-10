<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class DapodikSyncTest extends UatBase
{
    public function test_tu_buka_halaman_dapodik(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/dapodik')
                ->assertSee('Dapodik');
        });
    }

    public function test_tu_buka_halaman_pengaturan(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/pengaturan')
                ->assertSee('Pengaturan')
                ->assertSee('Tahun Pelajaran');
        });
    }

    public function test_tu_buka_halaman_sekolah(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/sekolah')
                ->assertSee('Sekolah')
                ->assertSee('SMK Abdinegara');
        });
    }
}
