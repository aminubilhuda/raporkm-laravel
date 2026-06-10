<?php

namespace App\Http\Controllers\Api\V1\Tu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Tu\StorePegawaiRequest;
use App\Http\Requests\Api\V1\Tu\UpdatePegawaiRequest;
use App\Http\Resources\V1\PegawaiResource;
use App\Models\Ptk;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function __construct() {}

    public function index(Request $request): JsonResponse
    {
        $query = User::withTrashed()->with('ptk');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->integer('jabatan'));
        }

        $perPage = $request->input('per_page', 15);

        if ($perPage === 'all') {
            $pegawai = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data' => PegawaiResource::collection($pegawai),
            ]);
        }

        $paginator = $query->latest()->paginate((int) $perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => PegawaiResource::collection($paginator->getCollection()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => (int) $perPage,
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::withTrashed()->with('ptk')->find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PegawaiResource($user),
        ]);
    }

    public function store(StorePegawaiRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create($validated);

            if (! empty($validated['nip']) || ! empty($validated['nuptk'])) {
                $ptk = Ptk::create([
                    'user_id' => $user->id,
                    'nip' => $validated['nip'] ?? null,
                    'nuptk' => $validated['nuptk'] ?? null,
                    'nik' => $validated['nik'] ?? null,
                    'kelamin' => $validated['kelamin'] ?? null,
                    'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                    'agama' => $validated['agama'] ?? null,
                    'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                    'status_kepegawaian' => $validated['status_kepegawaian'] ?? null,
                ]);
                $user->update(['ptk_id' => $ptk->id]);
            }

            return $user;
        });

        $user->load('ptk');

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil dibuat.',
            'data' => new PegawaiResource($user),
        ], 201);
    }

    public function update(UpdatePegawaiRequest $request, string $id): JsonResponse
    {
        $user = User::withTrashed()->find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        DB::transaction(function () use ($user, $validated) {
            $user->update(collect($validated)->only([
                'nama', 'username', 'email', 'password', 'jabatan',
                'kontak', 'id_tugas_tambahan', 'moto',
            ])->toArray());

            if ($user->ptk) {
                $user->ptk->update(collect($validated)->only([
                    'nip', 'nuptk', 'nik', 'kelamin', 'tempat_lahir',
                    'tanggal_lahir', 'agama', 'pendidikan_terakhir',
                    'status_kepegawaian',
                ])->toArray());
            } elseif (! empty($validated['nip']) || ! empty($validated['nuptk'])) {
                $ptk = Ptk::create([
                    'user_id' => $user->id,
                    'nip' => $validated['nip'] ?? null,
                    'nuptk' => $validated['nuptk'] ?? null,
                    'nik' => $validated['nik'] ?? null,
                    'kelamin' => $validated['kelamin'] ?? null,
                    'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                    'agama' => $validated['agama'] ?? null,
                    'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                    'status_kepegawaian' => $validated['status_kepegawaian'] ?? null,
                ]);
                $user->update(['ptk_id' => $ptk->id]);
            }
        });

        $user->refresh()->load('ptk');

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil diperbarui.',
            'data' => new PegawaiResource($user),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil dihapus.',
        ]);
    }
}
