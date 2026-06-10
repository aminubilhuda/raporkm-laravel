<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class PwaPushTest extends UatBase
{
    public function test_push_notification_subscribe_and_send(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->tuUser)
                ->visit('/tu/pengaturan')
                ->assertSee('Pengaturan')
                ->click('button#tab-push')
                ->waitFor('#panel-push:not(.hidden)', 5)
                ->assertSee('Kirim Push Notification')
                ->assertSee('Target Penerima')
                ->assertSee('Judul Notifikasi')
                ->assertSee('Isi Pesan');
        });
    }

    public function test_push_notification_target_selector(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->tuUser)
                ->visit('/tu/pengaturan')
                ->click('button#tab-push')
                ->waitFor('#panel-push:not(.hidden)', 5)
                ->select('#push_target', 'role')
                ->assertVisible('#push-role-section')
                ->assertVisible('#push_role')
                ->select('#push_target', 'user')
                ->assertVisible('#push-user-section')
                ->assertVisible('#push_user_ids')
                ->select('#push_target', 'all')
                ->assertHidden('#push-role-section')
                ->assertHidden('#push-user-section');
        });
    }

    public function test_vapid_key_endpoint_requires_pwa_auth(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/api/pwa/vapid-key')
                ->assertSee('PWA token tidak ditemukan');
        });
    }
}
