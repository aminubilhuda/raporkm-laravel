@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-shield-check class="w-7 h-7" />
                Manajemen Role
            </h1>
            <p class="mt-1 text-sm text-gray-500">Kelola role dan permission untuk pengguna sistem.</p>
        </div>
        <button onclick="document.getElementById('create-role-modal').classList.remove('hidden')" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
            <x-heroicon-o-plus-circle class="w-5 h-5" />
            Tambah Role
        </button>
    </div>

    @if(session('success'))
        <div class="bg-success/10 border border-success/30 text-success px-4 py-3 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-coral/10 border border-coral/30 text-coral px-4 py-3 rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach($roles as $role)
        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full {{ $role->name === 'TU' ? 'bg-teal-primary/20 text-teal-primary' : ($role->name === 'Kepsek' ? 'bg-gold/20 text-gold-dark' : ($role->name === 'Guru' ? 'bg-coral/20 text-coral' : 'bg-sky/20 text-sky')) }} flex items-center justify-center">
                            <x-heroicon-o-shield-check class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="font-extrabold text-gray-800">{{ $role->name }}</h3>
                            <p class="text-xs text-gray-400">{{ $role->users_count }} pengguna</p>
                        </div>
                    </div>
                    @if(!in_array($role->name, ['TU', 'Guru', 'Kepsek']))
                    <form method="POST" action="{{ route('tu.roles.destroy', $role) }}" onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                        @csrf @method('DELETE')
                        <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg transition-colors" title="Hapus role">
                            <x-heroicon-o-trash class="w-4 h-4" />
                        </button>
                    </form>
                    @endif
                </div>

                <div class="mb-4">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Permissions ({{ $role->permissions_count }})
                    </p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($role->permissions->take(6) as $perm)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-pill bg-surface-base text-gray-600">
                                {{ $perm->name }}
                            </span>
                        @endforeach
                        @if($role->permissions_count > 6)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-pill bg-surface-base text-gray-400">
                                +{{ $role->permissions_count - 6 }} lainnya
                            </span>
                        @endif
                        @if($role->permissions_count === 0)
                            <span class="text-xs text-gray-400 italic">Tidak ada permission</span>
                        @endif
                    </div>
                </div>

                <a href="{{ route('tu.roles.edit', $role) }}" class="block w-full text-center px-4 py-2 bg-surface-base hover:bg-teal-bg rounded-lg text-sm font-bold text-teal-primary transition-colors">
                    Edit Permissions
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-extrabold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-key class="w-5 h-5 text-teal-primary" />
                Daftar Semua Permissions
            </h3>
        </div>
        <div class="p-5">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($permissions as $group => $perms)
                <div class="p-3 bg-surface-base rounded-lg">
                    <h4 class="font-bold text-gray-700 text-sm uppercase mb-2">{{ $group }}</h4>
                    <div class="flex flex-wrap gap-1">
                        @foreach($perms as $perm)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-pill bg-white text-gray-600 shadow-sm">
                                {{ $perm->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div id="create-role-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-card shadow-xl w-full max-w-md mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-extrabold text-gray-800">Tambah Role Baru</h3>
            <button onclick="document.getElementById('create-role-modal').classList.add('hidden')" class="p-1 rounded-lg hover:bg-gray-100">
                <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500" />
            </button>
        </div>
        <form method="POST" action="{{ route('tu.roles.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Role</label>
                <input type="text" name="name" required maxlength="50"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-primary/20 focus:border-teal-primary"
                    placeholder="Contoh: Bendahara, Staff Lab">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Permissions</label>
                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-1">
                    @foreach($permissions as $group => $perms)
                        <p class="text-xs font-bold text-gray-500 uppercase pt-2">{{ $group }}</p>
                        @foreach($perms as $perm)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="rounded border-gray-300 text-teal-primary focus:ring-teal-primary/20">
                            <span>{{ $perm->name }}</span>
                        </label>
                        @endforeach
                    @endforeach
                </div>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('create-role-modal').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="btn-primary">
                    Simpan Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
