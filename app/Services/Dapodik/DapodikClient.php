<?php

namespace App\Services\Dapodik;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DapodikClient
{
    public function __construct(
        private ?string $baseUrl = null,
        private ?string $npsn = null,
        private ?string $token = null,
    ) {
        $this->baseUrl = $this->baseUrl ?? $this->resolveBaseUrl();
        $this->npsn = $this->npsn ?? $this->config('dapodik_npsn', '');
        $this->token = $this->token ?? $this->config('dapodik_token', '');
    }

    public function get(string $endpoint): array
    {
        if ($this->baseUrl === '' || $this->npsn === '' || $this->token === '') {
            throw new \Exception('Konfigurasi Dapodik belum lengkap. Silakan isi URL, NPSN, dan Token di halaman Pengaturan Dapodik.');
        }

        $response = Http::timeout(60)
            ->withToken($this->token)
            ->get("{$this->baseUrl}/{$endpoint}?npsn={$this->npsn}");

        if (! $response->successful()) {
            throw new \Exception("HTTP {$response->status()}: {$response->body()}");
        }

        return $this->extractRows($response->json());
    }

    private function resolveBaseUrl(): string
    {
        $url = trim((string) $this->config('dapodik_url'));

        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return rtrim($url, '/');
        }

        return "http://{$url}:5774/WebService";
    }

    private function extractRows(?array $json): array
    {
        $rows = $json['rows'] ?? [];

        if (is_array($rows)) {
            $isAssoc = array_keys($rows) !== range(0, count($rows) - 1);

            return $isAssoc ? [$rows] : $rows;
        }

        return [];
    }

    private function config(string $key, mixed $default = null): mixed
    {
        return DB::table('settings')->where('key', $key)->value('value') ?? $default;
    }
}
