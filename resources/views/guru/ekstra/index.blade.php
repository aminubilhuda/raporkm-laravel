@extends('layouts.guru')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-trophy class="w-7 h-7" /> Ekstrakurikuler</h1><p class="mt-1 text-sm text-gray-500">Daftar eskul yang Anda bina.</p></div>
<div class="bg-white rounded-card shadow-card overflow-hidden">
<table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Ekstrakurikuler</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Keterangan</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden lg:table-cell">Pembina</th></tr></thead>
<tbody class="divide-y">@forelse($eskuls as $e)<tr class="hover:bg-coral-bg/30"><td class="px-4 py-3 font-bold">{{ $e->nama_eskul }}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{ $e->keterangan ?? '-' }}</td><td class="px-4 py-3 hidden lg:table-cell text-gray-500">@foreach($e->pembinaEskul as $i => $p){{ $i ? ', ' : '' }}{{ $p->user->nama ?? '-' }}@endforeach</td></tr>@empty<tr><td colspan="3" class="px-4 py-12 text-center text-gray-400">Belum ada eskul yang Anda bina.</td></tr>@endforelse</tbody></table></div></div>
@endsection
