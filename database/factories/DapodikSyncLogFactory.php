<?php

namespace Database\Factories;

use App\Models\DapodikSyncLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class DapodikSyncLogFactory extends Factory
{
    protected $model = DapodikSyncLog::class;

    public function definition(): array
    {
        return [
            'endpoint' => fake()->randomElement(['sekolah', 'peserta-didik', 'rombongan-belajar', 'gtk', 'pengguna']),
            'status' => fake()->randomElement(['success', 'error', 'running']),
            'records_count' => fake()->numberBetween(10, 500),
            'message' => fake()->sentence(),
            'batch_id' => fake()->uuid(),
            'progress_current' => fake()->numberBetween(0, 100),
            'progress_total' => 100,
        ];
    }
}
