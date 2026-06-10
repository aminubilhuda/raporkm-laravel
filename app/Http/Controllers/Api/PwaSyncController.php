<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPwaSyncJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PwaSyncController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'max:20'],
            'items.*.url' => ['required', 'string'],
            'items.*.payload' => ['required', 'array'],
            'items.*.timestamp' => ['required', 'integer'],
        ]);

        $syncedCount = 0;

        foreach ($validated['items'] as $item) {
            ProcessPwaSyncJob::dispatch(
                $request->user()->id,
                $item['url'],
                $item['payload']
            );
            $syncedCount++;
        }

        return response()->json([
            'message' => "{$syncedCount} item akan diproses.",
            'queued' => $syncedCount,
        ]);
    }
}
