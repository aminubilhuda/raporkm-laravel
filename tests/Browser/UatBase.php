<?php

namespace Tests\Browser;

use Laravel\Dusk\TestCase;

abstract class UatBase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function loginAs($browser, string $username, string $password = 'password'): void
    {
        $browser->visit('/login')
            ->type('email', $username)
            ->type('password', $password)
            ->press('Log in')
            ->waitForLocation('/dashboard', 10);
    }

    protected function loginAsTu($browser): void
    {
        $this->loginAs($browser, 'tu');
    }

    protected function loginAsGuru($browser): void
    {
        $this->loginAs($browser, 'guru.wali.x');
    }

    protected function loginAsKepsek($browser): void
    {
        $this->loginAs($browser, 'kepsek');
    }
}
