<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class GuruCetakRaporTest extends UatBase
{
    public function test_guru_buka_halaman_cetak_rapor(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/cetak-rapor')
                ->assertSee('Cetak Rapor');
        });
    }

    public function test_guru_buka_halaman_kokurikuler(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/kokurikuler')
                ->assertSee('Kokurikuler');
        });
    }
}
