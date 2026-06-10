<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PwaPushController extends Controller
{
    public function __construct(
        private PushService $pushService
    ) {}

    public function vapidKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => $this->pushService->getVapidPublicKey(),
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'publicKey' => ['required', 'string'],
            'authToken' => ['required', 'string'],
        ]);

        $subscription = $this->pushService->subscribe(
            $request->user(),
            $validated['endpoint'],
            $validated['publicKey'],
            $validated['authToken']
        );

        return response()->json([
            'message' => 'Berhasil subscribe push notification.',
            'subscription_id' => $subscription->id,
        ]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        $this->pushService->unsubscribe($request->user(), $validated['endpoint']);

        return response()->json([
            'message' => 'Berhasil unsubscribe push notification.',
        ]);
    }

    public function unsubscribeAll(Request $request): JsonResponse
    {
        $this->pushService->unsubscribeAll($request->user());

        return response()->json([
            'message' => 'Berhasil unsubscribe semua push notification.',
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $count = $request->user()->pushSubscriptions()->active()->count();

        return response()->json([
            'subscribed' => $count > 0,
            'subscription_count' => $count,
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
            'url' => ['nullable', 'string', 'max:500'],
            'target_type' => ['required', 'in:all,role,user'],
            'target_role' => ['required_if:target_type,role', 'nullable', 'integer', 'in:2,3,4'],
            'target_user_ids' => ['required_if:target_type,user', 'nullable', 'array'],
            'target_user_ids.*' => ['exists:users,id'],
        ]);

        $results = match ($validated['target_type']) {
            'all' => $this->pushService->sendToAll(
                $validated['title'],
                $validated['body'],
                $validated['url'] ?? null
            ),
            'role' => $this->pushService->sendToRole(
                (int) $validated['target_role'],
                $validated['title'],
                $validated['body'],
                $validated['url'] ?? null
            ),
            'user' => $this->pushService->sendToUsers(
                User::whereIn('id', $validated['target_user_ids'])->get(),
                $validated['title'],
                $validated['body'],
                $validated['url'] ?? null
            ),
        };

        $successCount = count(array_filter($results, fn ($r) => $r['success']));
        $failCount = count($results) - $successCount;

        return response()->json([
            'message' => "Push notification terkirim: {$successCount} berhasil, {$failCount} gagal.",
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'total' => count($results),
        ]);
    }
}
