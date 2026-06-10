<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class TuSetupKurikulumTest extends UatBase
{
    public function test_tu_buka_halaman_mapel(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/mapel')
                ->assertSee('Mata Pelajaran')
                ->assertSee('Matematika')
                ->assertSee('Jaringan Komputer');
        });
    }

    public function test_tu_buka_halaman_tingkat(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/tingkat')
                ->assertSee('Tingkat')
                ->assertSee('X')
                ->assertSee('XI')
                ->assertSee('XII');
        });
    }

    public function test_tu_buka_halaman_kompetensi(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/kompetensi')
                ->assertSee('Kompetensi Keahlian')
                ->assertSee('TKJ')
                ->assertSee('RPL');
        });
    }

    public function test_tu_buka_halaman_kelompok_mapel(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsTu($browser);
            $browser->visit('/tu/kelompok-mapel')
                ->assertSee('Kelompok Mapel')
                ->assertSee('Muatan Nasional');
        });
    }
}
