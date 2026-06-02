@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-document-chart-bar class="w-7 h-7"/> Laporan</h1><p class="mt-1 text-sm text-gray-500">Laporan pendidikan dan riwayat WhatsApp.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Laporan WhatsApp</h2>
    <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Tujuan</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Pesan</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Status</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Dikirim</th></tr></thead>
    <tbody>@forelse($laporanWa as $l)<tr><td class="px-4 py-3 font-bold">{{$l->tujuan}}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500 truncate max-w-xs">{{$l->pesan??'-'}}</td><td class="px-4 py-3"><span class="px-2 py-0.5 rounded-pill text-xs font-bold {{$l->status=='sent'?'bg-success/10 text-success':'bg-coral/10 text-coral'}}">{{$l->status}}</span></td><td class="px-4 py-3 text-gray-500">{{$l->dikirim_pada?->format('d/m/Y H:i')??'-'}}</td></tr>@empty<tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada laporan WhatsApp.</td></tr>@endforelse</tbody></table></div>
</div>
</div>
</div>
@endsection