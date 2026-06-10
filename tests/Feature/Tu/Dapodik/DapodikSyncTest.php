<?php

namespace Tests\Feature\Tu\Dapodik;

use App\Jobs\SyncDapodikJob;
use App\Models\DapodikSyncLog;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DapodikSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->tataUsaha()->create();
    }

    public function test_index_page_renders()
    {
        $this->actingAs($this->user)
            ->get(route('tu.dapodik.index'))
            ->assertOk();
    }

    public function test_update_config_saves_settings()
    {
        $this->actingAs($this->user)
            ->post(route('tu.dapodik.config'), [
                'url' => 'http://192.168.1.1:5774/WebService',
                'npsn' => '20505005',
                'token' => 'abc123',
            ])
            ->assertSessionHas('status')
            ->assertRedirect(route('tu.dapodik.index'));
    }

    public function test_update_config_validates_required()
    {
        $this->actingAs($this->user)
            ->post(route('tu.dapodik.config'), [
                'url' => '',
                'npsn' => '',
                'token' => '',
            ])
            ->assertSessionHasErrors(['url', 'npsn', 'token']);
    }

    public function test_sync_endpoint_dispatches_job_and_redirects()
    {
        Queue::fake();

        $this->actingAs($this->user)
            ->post(route('tu.dapodik.sync', 'sekolah'))
            ->assertSessionHas('status')
            ->assertRedirect(route('tu.dapodik.index'));

        Queue::assertPushed(fn (SyncDapodikJob $job) => $job->endpoint === 'sekolah');
    }

    public function test_sync_returns_404_for_unknown_endpoint()
    {
        $this->actingAs($this->user)
            ->post(route('tu.dapodik.sync', 'invalid-endpoint'))
            ->assertNotFound();
    }

    public function test_log_page_is_paginated()
    {
        TahunPelajaran::factory()->create(['status' => 1]);

        // Create specific log entry
        DapodikSyncLog::factory()->create([
            'endpoint' => 'test-pagination-endpoint',
        ]);
        DapodikSyncLog::factory()->count(4)->create();

        $this->actingAs($this->user)
            ->get(route('tu.dapodik.log'))
            ->assertOk()
            ->assertSee('test-pagination-endpoint');
    }

    public function test_status_endpoint_returns_json()
    {
        $response = $this->actingAs($this->user)
            ->get(route('tu.dapodik.status'))
            ->assertOk()
            ->assertJsonStructure([
                'running',
                'batch',
                'last_log',
                'recent_logs',
            ]);
    }

    public function test_cancel_returns_message_when_no_active_batch()
    {
        $this->actingAs($this->user)
            ->post(route('tu.dapodik.cancel'))
            ->assertSessionHas('error')
            ->assertRedirect(route('tu.dapodik.index'));
    }

    public function test_guru_cannot_access_dapodik()
    {
        $guru = User::factory()->guru()->create();

        $this->actingAs($guru)
            ->get(route('tu.dapodik.index'))
            ->assertStatus(403);
    }
}
