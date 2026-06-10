<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class GuruCatatanRaporTest extends UatBase
{
    public function test_guru_buka_halaman_catatan_rapor(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/catatan-rapor')
                ->assertSee('Catatan Rapor');
        });
    }

    public function test_guru_buka_halaman_tujuan_pembelajaran(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/tujuan-pembelajaran')
                ->assertSee('Tujuan Pembelajaran');
        });
    }
}
