@extends('layouts.guru')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-calendar-days class="w-7 h-7" /> Piket Harian</h1><p class="mt-1 text-sm text-gray-500">Jadwal piket guru.</p></div>
<div class="bg-white rounded-card shadow-card overflow-hidden">
<table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Guru</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Hari</th></tr></thead>
<tbody class="divide-y">@forelse($pikets as $p)<tr class="hover:bg-coral-bg/30"><td class="px-4 py-3 font-bold">{{ $p->user->nama ?? '-' }}</td><td class="px-4 py-3 text-gray-500">{{ $p->hari?->nama ?? '-' }}</td></tr>@empty<tr><td colspan="2" class="px-4 py-12 text-center text-gray-400">Belum ada jadwal piket.</td></tr>@endforelse</tbody></table></div></div>
@endsection
