@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-users class="w-7 h-7" />
                Pegawai / Guru
            </h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data pegawai dan guru.</p>
        </div>
        <a href="{{ route('tu.pegawai.create') }}" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
            <x-heroicon-o-plus-circle class="w-5 h-5" />
            Tambah Pegawai
        </a>
    </div>

    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">Username</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">Jabatan</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden lg:table-cell">Kontak</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pegawai as $p)
                    <tr class="hover:bg-teal-bg/30 transition-colors {{ $p->trashed() ? 'opacity-60' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $p->jabatan == 2 ? 'bg-teal-primary/20 text-teal-primary' : ($p->jabatan == 4 ? 'bg-gold/20 text-gold-dark' : 'bg-coral/20 text-coral') }} flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-o-user class="w-4 h-4" />
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $p->nama }}</p>
                                    <p class="text-xs text-gray-400 md:hidden">@ {{ $p->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $p->username }}</td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span class="px-2 py-0.5 text-xs font-bold rounded-pill {{ $p->jabatan == 2 ? 'bg-teal-primary/10 text-teal-primary' : ($p->jabatan == 4 ? 'bg-gold/10 text-gold-dark' : 'bg-coral/10 text-coral') }}">
                                {{ $p->jabatan == 2 ? 'TU' : ($p->jabatan == 4 ? 'Kepsek' : 'Guru') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 hidden lg:table-cell">{{ $p->kontak ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($p->trashed())
                                <span class="text-xs font-bold text-coral">Nonaktif</span>
                            @else
                                <span class="text-xs font-bold text-success">Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                @if($p->trashed())
                                    <form method="POST" action="{{ route('tu.pegawai.restore', $p->id) }}" class="inline">
                                        @csrf
                                        <button class="p-1.5 text-teal-primary hover:bg-teal-bg rounded-lg transition-colors" title="Aktifkan kembali">
                                            <x-heroicon-o-arrow-path class="w-4 h-4" />
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('tu.pegawai.edit', $p) }}" class="p-1.5 text-sky hover:bg-sky/5 rounded-lg transition-colors" title="Edit">
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('tu.pegawai.destroy', $p) }}" class="inline" onsubmit="return confirm('Nonaktifkan pegawai ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg transition-colors" title="Nonaktifkan">
                                            <x-heroicon-o-x-circle class="w-4 h-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada data pegawai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $pegawai->links() }}
        </div>
    </div>
</div>
@endsection