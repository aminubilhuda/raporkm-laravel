<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class PwaUpdatePromptTest extends UatBase
{
    public function test_update_prompt_component_exists_in_guru_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->guruUser)
                ->visit('/guru/dashboard')
                ->assertSee('E-Rapor');
        });
    }

    public function test_update_prompt_component_exists_in_tu_layout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->tuUser)
                ->visit('/tu/dashboard')
                ->assertSee('E-Rapor');
        });
    }
}
