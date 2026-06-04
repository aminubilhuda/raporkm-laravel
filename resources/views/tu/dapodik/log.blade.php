@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-clock class="w-7 h-7" /> Log Sinkronisasi</h1><p class="mt-1 text-sm text-gray-500">Riwayat sinkronisasi data Dapodik.</p></div>
    <a href="{{ route('tu.dapodik.index') }}" class="btn-primary inline-flex items-center gap-2"><x-heroicon-o-arrow-left class="w-5 h-5" /> Kembali</a>
</div>

<div class="bg-white rounded-card shadow-card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">#</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Endpoint</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Status</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Records</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden lg:table-cell">Pesan</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Waktu</th></tr></thead>
    <tbody class="divide-y">@forelse($logs as $l)<tr class="hover:bg-teal-bg/30"><td class="px-4 py-3 text-gray-400">{{ $logs->firstItem() + $loop->index }}</td>
    <td class="px-4 py-3 font-bold">{{ $l->endpoint }}</td>
    <td class="px-4 py-3">@if($l->status === 'success')<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-success/10 text-success font-bold text-xs rounded-pill">✓ Sukses</span>@else<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-coral/10 text-coral font-bold text-xs rounded-pill">✗ Gagal</span>@endif</td>
    <td class="px-4 py-3 font-bold">{{ $l->records_count }}</td>
    <td class="px-4 py-3 hidden lg:table-cell text-gray-500 max-w-xs truncate">{{ $l->message }}</td>
    <td class="px-4 py-3 text-gray-500 text-xs">{{ $l->created_at->format('d/m/Y H:i') }}</td></tr>@empty<tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">Belum ada riwayat sinkronisasi.</td></tr>@endforelse</tbody></table>
    </div>
    <div class="px-4 py-3 border-t">{{ $logs->links() }}</div>
</div>
</div>
@endsection
