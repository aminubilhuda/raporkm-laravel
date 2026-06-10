<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class PwaBackgroundSyncTest extends UatBase
{
    public function test_background_sync_api_requires_auth(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/api/pwa/sync')
                ->assertSee('PWA token tidak ditemukan');
        });
    }

    public function test_nilai_form_loads_for_guru(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->guruUser)
                ->visit('/guru/penilaian')
                ->assertSee('Penilaian');
        });
    }
}
