@extends('layouts.guru')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-building-office class="w-7 h-7" /> Prakerin</h1><p class="mt-1 text-sm text-gray-500">Data praktik kerja industri.</p></div>
<div class="bg-white rounded-card shadow-card overflow-hidden">
<table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Perusahaan</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">PIC</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden lg:table-cell">Periode</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden xl:table-cell">Keterangan</th></tr></thead>
<tbody class="divide-y">@forelse($prakerins as $p)<tr class="hover:bg-coral-bg/30"><td class="px-4 py-3 font-bold">{{ $p->nama_perusahaan }}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{ $p->PIC ?? '-' }}</td><td class="px-4 py-3 hidden lg:table-cell text-gray-500">{{ $p->tanggal_mulai?->format('d/m/Y') }} - {{ $p->tanggal_selesai?->format('d/m/Y') }}</td><td class="px-4 py-3 hidden xl:table-cell text-gray-500">{{ $p->keterangan ?? '-' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada data prakerin.</td></tr>@endforelse</tbody></table></div>
<div class="px-4 py-3">{{ $prakerins->links() }}</div>
</div>
@endsection
