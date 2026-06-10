<?php

namespace Tests\Browser\Uat;

use Tests\Browser\UatBase;

class GuruProjectP5Test extends UatBase
{
    public function test_guru_buka_halaman_project_kelas(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/project-kelas')
                ->assertSee('Project Kelas');
        });
    }

    public function test_guru_buka_halaman_p5bk(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/p5bk')
                ->assertSee('P5BK');
        });
    }

    public function test_guru_buka_halaman_penilaian_profil_pancasila(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsGuru($browser);
            $browser->visit('/guru/penilaian-profil-pancasila')
                ->assertSee('Penilaian Profil Pancasila')
                ->assertSee('Pilih Kelas Wali');
        });
    }
}
