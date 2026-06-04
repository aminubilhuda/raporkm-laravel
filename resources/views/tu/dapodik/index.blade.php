@extends('layouts.tu')
@section('content')
<style>
    .loader {
        width: 120px;
        height: 24px;
        -webkit-mask:
            radial-gradient(circle closest-side,#000 94%,#0000) 0 0/25% 100%,
            linear-gradient(#000 0 0) center/calc(100% - 12px) calc(100% - 12px) no-repeat;
        background:
            linear-gradient(#25b09b 0 0) 0/0% no-repeat #ddd;
        animation: l7 2s infinite linear;
    }
    @keyframes l7 {
        100% { background-size: 100% }
    }
</style>

<div id="sync-loading" class="fixed inset-0 z-50 hidden bg-black/60 flex flex-col items-center justify-center">
    <div class="loader"></div>
    <p class="mt-4 text-white font-bold text-lg">Sedang sinkronisasi...</p>
    <p class="mt-1 text-white/70 text-sm">Mohon tunggu, jangan tutup halaman ini.</p>
</div>

<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-cloud-arrow-down class="w-7 h-7" /> Dapodik</h1><p class="mt-1 text-sm text-gray-500">Sinkronisasi data dari API Dapodik.</p></div>

<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Konfigurasi API Dapodik</h2>
    <form method="POST" action="{{ route('tu.dapodik.config') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @csrf
        <div>
            <label class="block text-xs font-extrabold uppercase text-gray-500 mb-1">URL API</label>
            <x-text-input name="url" :value="$config['url']" placeholder="http://192.168.11.207:5774/WebService" class="block w-full" required />
            @error('url')<p class="text-coral text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-extrabold uppercase text-gray-500 mb-1">NPSN</label>
            <x-text-input name="npsn" :value="$config['npsn']" placeholder="20505005" class="block w-full" required />
            @error('npsn')<p class="text-coral text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-extrabold uppercase text-gray-500 mb-1">Bearer Token</label>
            <x-text-input name="token" :value="$config['token']" placeholder="Bearer token" class="block w-full" required />
            @error('token')<p class="text-coral text-sm mt-1">{{ $message }}</p>@enderror
            <button class="btn-primary mt-2 w-full">Simpan</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
    <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Sinkronisasi Data</h2>
    <form method="POST" action="{{ route('tu.dapodik.sync', 'all') }}" class="mb-4">
        @csrf
        <button class="w-full flex items-center gap-3 p-4 bg-gold/10 rounded-card hover:bg-gold/20 transition-colors text-left border-2 border-dashed border-gold/40">
            <span class="flex-shrink-0 w-12 h-12 rounded-full bg-gold shadow-accent-glow flex items-center justify-center"><x-heroicon-o-rocket-launch class="w-6 h-6 text-white" /></span>
            <span class="flex-1"><strong class="block text-lg text-teal-primary-dark">Sync Semua Data</strong><small class="text-gray-500">/getSekolah + /getPesertaDidik + /getRombonganBelajar + /getGtk + /getPengguna + mapel_kelas</small></span>
            <x-heroicon-o-arrow-path class="w-6 h-6 text-gold-dark" />
        </button>
    </form>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <form method="POST" action="{{ route('tu.dapodik.sync', 'sekolah') }}">
            @csrf
            <button class="w-full flex items-center gap-3 p-4 bg-surface-base rounded-card hover:bg-teal-bg/50 transition-colors text-left">
                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-primary/10 flex items-center justify-center"><x-heroicon-o-building-library class="w-5 h-5 text-teal-primary" /></span>
                <span class="flex-1"><strong class="block text-teal-primary-dark">Sekolah</strong><small class="text-gray-500">/getSekolah</small></span>
                <x-heroicon-o-arrow-path class="w-5 h-5 text-teal-primary" />
            </button>
        </form>
        <form method="POST" action="{{ route('tu.dapodik.sync', 'peserta-didik') }}">
            @csrf
            <button class="w-full flex items-center gap-3 p-4 bg-surface-base rounded-card hover:bg-teal-bg/50 transition-colors text-left">
                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-primary/10 flex items-center justify-center"><x-heroicon-o-academic-cap class="w-5 h-5 text-teal-primary" /></span>
                <span class="flex-1"><strong class="block text-teal-primary-dark">Peserta Didik</strong><small class="text-gray-500">/getPesertaDidik</small></span>
                <x-heroicon-o-arrow-path class="w-5 h-5 text-teal-primary" />
            </button>
        </form>
        <form method="POST" action="{{ route('tu.dapodik.sync', 'rombongan-belajar') }}">
            @csrf
            <button class="w-full flex items-center gap-3 p-4 bg-surface-base rounded-card hover:bg-teal-bg/50 transition-colors text-left">
                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-primary/10 flex items-center justify-center"><x-heroicon-o-building-office-2 class="w-5 h-5 text-teal-primary" /></span>
                <span class="flex-1"><strong class="block text-teal-primary-dark">Rombongan Belajar</strong><small class="text-gray-500">/getRombonganBelajar</small></span>
                <x-heroicon-o-arrow-path class="w-5 h-5 text-teal-primary" />
            </button>
        </form>
        <form method="POST" action="{{ route('tu.dapodik.sync', 'gtk') }}">
            @csrf
            <button class="w-full flex items-center gap-3 p-4 bg-surface-base rounded-card hover:bg-teal-bg/50 transition-colors text-left">
                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-primary/10 flex items-center justify-center"><x-heroicon-o-identification class="w-5 h-5 text-teal-primary" /></span>
                <span class="flex-1"><strong class="block text-teal-primary-dark">GTK</strong><small class="text-gray-500">/getGtk</small></span>
                <x-heroicon-o-arrow-path class="w-5 h-5 text-teal-primary" />
            </button>
        </form>
        <form method="POST" action="{{ route('tu.dapodik.sync', 'pembelajaran') }}">
            @csrf
            <button class="w-full flex items-center gap-3 p-4 bg-surface-base rounded-card hover:bg-teal-bg/50 transition-colors text-left">
                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-primary/10 flex items-center justify-center"><x-heroicon-o-book-open class="w-5 h-5 text-teal-primary" /></span>
                <span class="flex-1"><strong class="block text-teal-primary-dark">Pembelajaran</strong><small class="text-gray-500">/getRombonganBelajar → mapel_kelas</small></span>
                <x-heroicon-o-arrow-path class="w-5 h-5 text-teal-primary" />
            </button>
        </form>
        <form method="POST" action="{{ route('tu.dapodik.sync', 'pengguna') }}">
            @csrf
            <button class="w-full flex items-center gap-3 p-4 bg-surface-base rounded-card hover:bg-teal-bg/50 transition-colors text-left">
                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-primary/10 flex items-center justify-center"><x-heroicon-o-users class="w-5 h-5 text-teal-primary" /></span>
                <span class="flex-1"><strong class="block text-teal-primary-dark">Pengguna</strong><small class="text-gray-500">/getPengguna</small></span>
                <x-heroicon-o-arrow-path class="w-5 h-5 text-teal-primary" />
            </button>
        </form>
    </div>
</div>

@if($logs->isNotEmpty())
<div class="bg-white rounded-card shadow-card overflow-hidden">
    <div class="px-4 py-3 border-b flex items-center justify-between">
        <h2 class="font-extrabold text-teal-primary-dark">Riwayat Sinkronisasi</h2>
        <a href="{{ route('tu.dapodik.log') }}" class="text-sm text-teal-primary font-bold hover:underline">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Endpoint</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Status</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Records</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden lg:table-cell">Waktu</th></tr></thead>
    <tbody class="divide-y">@foreach($logs as $l)<tr class="hover:bg-teal-bg/30"><td class="px-4 py-3 font-bold">{{ $l->endpoint }}</td>
    <td class="px-4 py-3">@if($l->status === 'success')<span class="text-success font-bold">✓ Sukses</span>@else<span class="text-coral font-bold">✗ Gagal</span>@endif</td>
    <td class="px-4 py-3">{{ $l->records_count }}</td>
    <td class="px-4 py-3 hidden lg:table-cell text-gray-500">{{ $l->created_at->format('d/m/Y H:i') }}</td></tr>@endforeach</tbody></table>
    </div>
</div>
@endif
</div>

<script>
    document.querySelectorAll('form[action*="dapodik/sync"]').forEach(function(form) {
        form.addEventListener('submit', function() {
            document.getElementById('sync-loading').classList.remove('hidden');
            document.querySelectorAll('form[action*="dapodik/sync"] button').forEach(function(btn) {
                btn.disabled = true;
            });
        });
    });
</script>
@endsection
