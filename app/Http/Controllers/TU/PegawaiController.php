<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = User::withTrashed()->latest()->paginate(15);

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
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('users', 'nip')],
            'nuptk' => ['nullable', 'string', 'max:30'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6'],
            'jabatan' => ['required', 'in:2,3,4'],
            'kelamin' => ['nullable', 'integer'],
            'agama' => ['nullable', 'integer'],
            'kontak' => ['nullable', 'string', 'max:20'],
            'id_kepegawaian' => ['nullable', 'integer'],
            'ijazah' => ['nullable', 'integer'],
            'id_tugas_tambahan' => ['nullable', 'integer'],
            'moto' => ['nullable', 'string'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('tu.pegawai.index')->with('status', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(User $pegawai)
    {
        return view('tu.pegawai.form', compact('pegawai'));
    }

    public function update(Request $request, User $pegawai)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('users', 'nip')->ignore($pegawai->id)],
            'nuptk' => ['nullable', 'string', 'max:30'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($pegawai->id)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($pegawai->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'jabatan' => ['required', 'in:2,3,4'],
            'kelamin' => ['nullable', 'integer'],
            'agama' => ['nullable', 'integer'],
            'kontak' => ['nullable', 'string', 'max:20'],
            'id_kepegawaian' => ['nullable', 'integer'],
            'ijazah' => ['nullable', 'integer'],
            'id_tugas_tambahan' => ['nullable', 'integer'],
            'moto' => ['nullable', 'string'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $pegawai->update($validated);

        return redirect()->route('tu.pegawai.index')->with('status', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(User $pegawai)
    {
        $pegawai->delete();

        return back()->with('status', 'Pegawai berhasil dinonaktifkan.');
    }

    public function restore($id)
    {
        User::withTrashed()->findOrFail($id)->restore();

        return back()->with('status', 'Pegawai berhasil diaktifkan kembali.');
    }
}
