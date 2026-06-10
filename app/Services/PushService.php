<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Minishlink\WebPush\WebPush;

class PushService
{
    private WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('push.vapid.subject'),
                'publicKey' => config('push.vapid.public_key'),
                'privateKey' => config('push.vapid.private_key'),
            ],
        ]);
    }

    public function subscribe(User $user, string $endpoint, string $publicKey, string $authToken): PushSubscription
    {
        return PushSubscription::updateOrCreate(
            ['user_id' => $user->id, 'endpoint' => $endpoint],
            [
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    public function unsubscribe(User $user, string $endpoint): bool
    {
        return (bool) PushSubscription::where('user_id', $user->id)
            ->where('endpoint', $endpoint)
            ->update(['is_active' => false]);
    }

    public function unsubscribeAll(User $user): int
    {
        return PushSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    public function sendToUser(User $user, string $title, string $body, ?string $url = null): array
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        $payload = [
            'title' => $title,
            'body' => $body,
            'icon' => '/icons/icon-192.png',
            'badge' => '/icons/icon-192.png',
        ];

        if ($url) {
            $payload['url'] = $url;
        }

        $results = [];
        foreach ($subscriptions as $subscription) {
            $report = $this->webPush->sendOneNotification(
                $subscription->getSubscriptionPayload(),
                json_encode($payload)
            );

            if ($report->isSuccess()) {
                $subscription->update(['last_used_at' => now()]);
                $results[] = ['subscription_id' => $subscription->id, 'success' => true];
            } else {
                // Subscription expired or invalid, deactivate
                $subscription->update(['is_active' => false]);
                $results[] = ['subscription_id' => $subscription->id, 'success' => false, 'error' => $report->getReason()];
            }
        }

        return $results;
    }

    public function sendToUsers($users, string $title, string $body, ?string $url = null): array
    {
        if (! $users instanceof Collection) {
            $users = collect($users);
        }

        $allResults = [];
        foreach ($users as $user) {
            $results = $this->sendToUser($user, $title, $body, $url);
            $allResults = array_merge($allResults, $results);
        }

        return $allResults;
    }

    public function sendToRole(int $jabatan, string $title, string $body, ?string $url = null): array
    {
        $users = User::where('jabatan', $jabatan)->get();

        return $this->sendToUsers($users, $title, $body, $url);
    }

    public function sendToAll(string $title, string $body, ?string $url = null): array
    {
        $users = User::whereHas('pushSubscriptions', function ($q) {
            $q->where('is_active', true);
        })->get();

        return $this->sendToUsers($users, $title, $body, $url);
    }

    public function getVapidPublicKey(): string
    {
        return config('push.vapid.public_key');
    }

    public function isConfigured(): bool
    {
        return ! empty(config('push.vapid.public_key'))
            && ! empty(config('push.vapid.private_key'));
    }
}
