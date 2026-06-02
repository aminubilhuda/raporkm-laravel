@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-academic-cap class="w-7 h-7" />
                Data Siswa
            </h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data siswa aktif.</p>
        </div>
        <a href="{{ route('tu.kesiswaan.create') }}" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
            <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambah Siswa
        </a>
    </div>

    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">NISN</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">NIS</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">JK</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden lg:table-cell">Tgl Lahir</th>
                        <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($siswa as $s)
                    <tr class="hover:bg-teal-bg/30">
                        <td class="px-4 py-3 font-bold text-gray-800">{{ $s->nama_siswa }}</td>
                        <td class="px-4 py-3 text-teal-primary font-bold">{{ $s->nisn }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $s->nis }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $s->kelamin == 1 ? 'L' : ($s->kelamin == 2 ? 'P' : '-') }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden lg:table-cell">{{ $s->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('tu.kesiswaan.show', $s) }}" class="p-1.5 text-teal-primary hover:bg-teal-bg rounded-lg" title="Detail"><x-heroicon-o-eye class="w-4 h-4" /></a>
                                <a href="{{ route('tu.kesiswaan.edit', $s) }}" class="p-1.5 text-sky hover:bg-sky/5 rounded-lg" title="Edit"><x-heroicon-o-pencil-square class="w-4 h-4" /></a>
                                <form method="POST" action="{{ route('tu.kesiswaan.destroy', $s) }}" class="inline" onsubmit="return confirm('Nonaktifkan siswa ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-x-circle class="w-4 h-4" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada data siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $siswa->links() }}</div>
    </div>
</div>
@endsection