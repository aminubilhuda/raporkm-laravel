<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\GuruMenuAkses;
use App\Models\Ptk;
use App\Models\User;
use App\Services\GuruMenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
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
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6'],
            'jabatan' => ['required', 'in:2,3,4'],
            'kontak' => ['nullable', 'string', 'max:20'],
            'id_tugas_tambahan' => ['nullable', 'integer'],
            'moto' => ['nullable', 'string'],
            // GTK fields
            'nip' => ['nullable', 'string', 'max:30'],
            'nuptk' => ['nullable', 'string', 'max:30'],
            'kelamin' => ['nullable', 'integer'],
            'agama' => ['nullable', 'integer'],
        ]);

        $password = Hash::make($validated['password']);

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password' => $password,
            'jabatan' => $validated['jabatan'],
            'kontak' => $validated['kontak'] ?? null,
            'id_tugas_tambahan' => $validated['id_tugas_tambahan'] ?? null,
            'moto' => $validated['moto'] ?? null,
        ]);

        // Buat GTK record jika ada data GTK
        if (! empty($validated['nip']) || ! empty($validated['nuptk'])) {
            Ptk::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'] ?? null,
                'nuptk' => $validated['nuptk'] ?? null,
                'kelamin' => $validated['kelamin'] ?? null,
                'agama' => $validated['agama'] ?? null,
            ]);
            $user->update(['ptk_id' => Ptk::where('user_id', $user->id)->first()->id]);
        }

        // Save menu overrides for guru/kepsek
        if ($user->isGuru() || $user->isKepsek()) {
            $this->syncMenuAkses($user, $request);
        }

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
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($pegawai->id)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($pegawai->id)],
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
        ]);

        // Update user
        $userData = [
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'jabatan' => $validated['jabatan'],
            'kontak' => $validated['kontak'] ?? null,
            'id_tugas_tambahan' => $validated['id_tugas_tambahan'] ?? null,
            'moto' => $validated['moto'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $pegawai->update($userData);

        // Update atau buat PTK record
        $ptkData = [
            'nip' => $validated['nip'] ?? null,
            'nuptk' => $validated['nuptk'] ?? null,
            'kelamin' => $validated['kelamin'] ?? null,
            'agama' => $validated['agama'] ?? null,
        ];

        if ($pegawai->ptk) {
            $pegawai->ptk->update($ptkData);
        } elseif (! empty($validated['nip']) || ! empty($validated['nuptk'])) {
            $ptk = Ptk::create(array_merge($ptkData, ['user_id' => $pegawai->id]));
            $pegawai->update(['ptk_id' => $ptk->id]);
        }

        // Save menu overrides for guru/kepsek
        if ($pegawai->isGuru() || $pegawai->isKepsek()) {
            $this->syncMenuAkses($pegawai, $request);
        } else {
            GuruMenuAkses::where('user_id', $pegawai->id)->delete();
        }

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

    private function syncMenuAkses(User $user, Request $request): void
    {
        $menuOverrides = $request->input('menu_akses', []);
        $records = [];

        foreach (GuruMenuService::MENU_SLUGS as $slug) {
            $tipe = $menuOverrides[$slug] ?? null;
            if ($tipe && in_array($tipe, ['grant', 'revoke'])) {
                $records[] = [
                    'user_id' => $user->id,
                    'menu_slug' => $slug,
                    'tipe' => $tipe,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        GuruMenuAkses::where('user_id', $user->id)->delete();

        if ($records) {
            GuruMenuAkses::insert($records);
        }
    }
}
