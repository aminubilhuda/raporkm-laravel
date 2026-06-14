<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\GuruMenuAkses;
use App\Models\User;
use App\Services\PegawaiService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function __construct(private PegawaiService $pegawaiService)
    {
    }

    public function index(Request $request)
    {
        $query = User::withTrashed()->with('ptk');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        $perPage = $request->input('per_page', 15);
        if ($perPage === 'all') {
            $pegawai = $query->latest()->get();
        } else {
            $pegawai = $query->latest()->paginate((int) $perPage)->withQueryString();
        }

        return view('tu.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        return view('tu.pegawai.form', ['pegawai' => new User]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePegawai($request);

        $user = $this->pegawaiService->createPegawai($validated);

        activity()
            ->performedOn($user)
            ->event('created')
            ->withProperties(['nama' => $user->nama])
            ->log('Pegawai ditambahkan');

        return redirect()->route('tu.pegawai.index')->with('status', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(User $pegawai)
    {
        $pegawai->load('ptk');

        $menuAkses = GuruMenuAkses::where('user_id', $pegawai->id)
            ->get()
            ->pluck('tipe', 'menu_slug')
            ->toArray();

        return view('tu.pegawai.form', compact('pegawai', 'menuAkses'));
    }

    public function update(Request $request, User $pegawai)
    {
        $validated = $this->validatePegawai($request, $pegawai);

        $this->pegawaiService->updatePegawai($pegawai, $validated);

        activity()
            ->performedOn($pegawai)
            ->event('updated')
            ->withProperties(['nama' => $pegawai->nama])
            ->log('Pegawai diperbarui');

        return redirect()->route('tu.pegawai.index')->with('status', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(User $pegawai)
    {
        $nama = $pegawai->nama;
        $pegawai->delete();

        activity()
            ->performedOn($pegawai)
            ->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Pegawai dihapus');

        return back()->with('status', 'Pegawai berhasil dinonaktifkan.');
    }

    public function restore($id)
    {
        User::withTrashed()->findOrFail($id)->restore();

        return back()->with('status', 'Pegawai berhasil diaktifkan kembali.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePegawai(Request $request, ?User $pegawai = null): array
    {
        $ignoreId = $pegawai?->id;

        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($ignoreId)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($ignoreId)],
            'password' => ['nullable', 'string', 'min:6'],
            'jabatan' => ['required', 'in:2,3,4'],
            'kontak' => ['nullable', 'string', 'max:20'],
            'id_tugas_tambahan' => ['nullable', 'integer'],
            'moto' => ['nullable', 'string'],
            // GTK fields
            'nip' => ['nullable', 'string', 'max:30'],
            'nuptk' => ['nullable', 'string', 'max:30'],
            'kelamin' => ['nullable', 'integer'],
            'agama' => ['nullable', 'integer'],
            // Menu access overrides
            'menu_akses' => ['nullable', 'array'],
        ]);
    }
}
