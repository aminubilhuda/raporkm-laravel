<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SekolahResource;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;

class SekolahController extends Controller
{
    public function profile(): JsonResponse
    {
        $sekolah = Sekolah::with(['tahunPelajaran', 'semester'])->first();

        return response()->json([
            'success' => true,
            'data' => new SekolahResource($sekolah),
        ]);
    }

    public function publik(): JsonResponse
    {
        $sekolah = Sekolah::first();

        return response()->json([
            'success' => true,
            'data' => [
                'nama_sekolah' => $sekolah?->nama_sekolah,
                'logo_url' => $sekolah?->logo ? asset('storage/'.$sekolah->logo) : null,
            ],
        ]);
    }
}
