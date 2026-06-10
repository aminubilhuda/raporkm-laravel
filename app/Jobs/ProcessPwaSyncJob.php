<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;

class ProcessPwaSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public int $userId,
        public string $url,
        public array $payload
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            Log::warning("PwaSyncJob: User {$this->userId} not found, skipping.");

            return;
        }

        // Parse relative URL to internal request
        $path = parse_url($this->url, PHP_URL_PATH);
        if (! $path) {
            Log::warning("PwaSyncJob: Invalid URL {$this->url}, skipping.");

            return;
        }

        // Build internal request based on URL path
        $response = $this->simulateInternalRequest($user, $path, $this->payload);

        if (! $response) {
            Log::warning("PwaSyncJob: Failed to process sync for URL {$this->url}");
            $this->fail(new \Exception("Failed to sync: {$this->url}"));
        }
    }

    private function simulateInternalRequest(User $user, string $path, array $payload): ?bool
    {
        try {
            // Impersonate user for internal request
            auth()->login($user);

            $request = Request::create($path, 'POST', $payload);
            $request->setUser($user);

            $router = app(Router::class);
            $routes = $router->getRoutes();

            $route = $routes->match($request);
            if (! $route) {
                return null;
            }

            $request->setRouteResolver(fn () => $route);

            $response = app(Kernel::class)->handle($request);

            auth()->logout();

            return $response->isSuccessful();
        } catch (\Throwable $e) {
            Log::error("PwaSyncJob error: {$e->getMessage()}");
            auth()->logout();

            return null;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("PwaSyncJob permanently failed for user {$this->userId}: {$exception->getMessage()}");
    }
}
