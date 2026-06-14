@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-shield-check class="w-7 h-7" />
                Edit Role: {{ $role->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">Atur permission untuk role ini.</p>
        </div>
        <a href="{{ route('tu.roles.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Kembali
        </a>
    </div>

    @if($role->name === 'TU')
        <div class="bg-gold/10 border border-gold/30 text-gold-dark px-4 py-3 rounded-lg text-sm font-medium">
            Role TU memiliki semua permission secara default dan tidak dapat diubah.
        </div>
    @endif

    <form method="POST" action="{{ route('tu.roles.update', $role) }}">
        @csrf @method('PUT')

        <div class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full {{ $role->name === 'TU' ? 'bg-teal-primary/20 text-teal-primary' : ($role->name === 'Kepsek' ? 'bg-gold/20 text-gold-dark' : ($role->name === 'Guru' ? 'bg-coral/20 text-coral' : 'bg-sky/20 text-sky')) }} flex items-center justify-center">
                        <x-heroicon-o-shield-check class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-extrabold text-gray-800">{{ $role->name }}</h3>
                        <p class="text-xs text-gray-400">{{ $role->permissions->count() }} permissions aktif</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleAll(true)" class="text-xs font-bold text-teal-primary hover:underline">Pilih Semua</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="toggleAll(false)" class="text-xs font-bold text-coral hover:underline">Batal Semua</button>
                </div>
            </div>

            <div class="p-5">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($permissions as $group => $perms)
                    <div class="p-4 bg-surface-base rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-bold text-gray-700 text-sm uppercase">
                                @switch($group)
                                    @case('user')
                                        <x-heroicon-o-users class="w-4 h-4 inline-block mr-1" />
                                        Pengguna
                                        @break
                                    @case('siswa')
                                        <x-heroicon-o-academic-cap class="w-4 h-4 inline-block mr-1" />
                                        Siswa
                                        @break
                                    @case('kelas')
                                        <x-heroicon-o-building-office class="w-4 h-4 inline-block mr-1" />
                                        Kelas
                                        @break
                                    @case('mapel')
                                        <x-heroicon-o-book-open class="w-4 h-4 inline-block mr-1" />
                                        Mata Pelajaran
                                        @break
                                    @case('nilai')
                                        <x-heroicon-o-pencil-square class="w-4 h-4 inline-block mr-1" />
                                        Nilai
                                        @break
                                    @case('p5bk')
                                        <x-heroicon-o-star class="w-4 h-4 inline-block mr-1" />
                                        P5BK
                                        @break
                                    @case('ekskul')
                                        <x-heroicon-o-trophy class="w-4 h-4 inline-block mr-1" />
                                        Ekstrakurikuler
                                        @break
                                    @case('presensi')
                                        <x-heroicon-o-clipboard-document-check class="w-4 h-4 inline-block mr-1" />
                                        Presensi
                                        @break
                                    @case('prakerin')
                                        <x-heroicon-o-building-office-2 class="w-4 h-4 inline-block mr-1" />
                                        Prakerin
                                        @break
                                    @case('rapor')
                                        <x-heroicon-o-document-text class="w-4 h-4 inline-block mr-1" />
                                        Rapor
                                        @break
                                    @case('sekolah')
                                        <x-heroicon-o-building-office-2 class="w-4 h-4 inline-block mr-1" />
                                        Sekolah
                                        @break
                                    @case('dapodik')
                                        <x-heroicon-o-cloud-arrow-down class="w-4 h-4 inline-block mr-1" />
                                        Dapodik
                                        @break
                                    @case('laporan')
                                        <x-heroicon-o-document-chart-bar class="w-4 h-4 inline-block mr-1" />
                                        Laporan
                                        @break
                                    @default
                                        {{ ucfirst($group) }}
                                @endswitch
                            </h4>
                            <label class="flex items-center">
                                <input type="checkbox" onclick="toggleGroup('{{ $group }}', this.checked)"
                                    class="rounded border-gray-300 text-teal-primary focus:ring-teal-primary/20">
                                <span class="ml-1 text-xs text-gray-500">Semua</span>
                            </label>
                        </div>
                        <div class="space-y-2">
                            @foreach($perms as $perm)
                            <label class="flex items-center gap-2 text-sm hover:bg-white p-1.5 rounded-lg transition-colors cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                    data-group="{{ $group }}"
                                    {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}
                                    {{ $role->name === 'TU' ? 'disabled' : '' }}
                                    class="rounded border-gray-300 text-teal-primary focus:ring-teal-primary/20">
                                <span class="text-gray-700">
                                    @php
                                        $action = explode('.', $perm->name)[1] ?? $perm->name;
                                    @endphp
                                    @switch($action)
                                        @case('view') Lihat @break
                                        @case('create') Tambah @break
                                        @case('edit') Edit @break
                                        @case('delete') Hapus @break
                                        @case('input') Input @break
                                        @case('generate') Generate @break
                                        @case('print') Cetak @break
                                        @case('sync') Sinkronisasi @break
                                        @default {{ ucfirst($action) }}
                                    @endswitch
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($role->name !== 'TU')
            <div class="p-5 border-t border-gray-100 flex justify-end gap-2">
                <a href="{{ route('tu.roles.index') }}" class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <x-heroicon-o-check class="w-4 h-4" />
                    Simpan Perubahan
                </button>
            </div>
            @endif
        </div>
    </form>
</div>

<script>
function toggleAll(checked) {
    document.querySelectorAll('input[name="permissions[]"]:not(:disabled)').forEach(cb => cb.checked = checked);
    document.querySelectorAll('input[onclick^="toggleGroup"]').forEach(cb => cb.checked = checked);
}

function toggleGroup(group, checked) {
    document.querySelectorAll(`input[name="permissions[]"][data-group="${group}"]:not(:disabled)`).forEach(cb => cb.checked = checked);
}
</script>
@endsection
