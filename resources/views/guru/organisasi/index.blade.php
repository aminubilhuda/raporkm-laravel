@extends('layouts.guru')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-user-group class="w-7 h-7" /> Organisasi</h1><p class="mt-1 text-sm text-gray-500">Data organisasi siswa.</p></div>
<div class="bg-white rounded-card shadow-card overflow-hidden">
<table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Nama Organisasi</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Keterangan</th></tr></thead>
<tbody class="divide-y">@forelse($organisasi as $o)<tr class="hover:bg-coral-bg/30"><td class="px-4 py-3 font-bold">{{ $o->nama_organisasi }}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{ $o->keterangan ?? '-' }}</td></tr>@empty<tr><td colspan="2" class="px-4 py-12 text-center text-gray-400">Belum ada data organisasi.</td></tr>@endforelse</tbody></table></div></div>
@endsection
