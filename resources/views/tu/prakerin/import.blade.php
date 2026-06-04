@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-arrow-up-tray class="w-7 h-7" /> Import Prakerin</h1>
        <p class="mt-1 text-sm text-gray-500">Import data prakerin dari file CSV atau XLSX.</p>
    </div>
    <a href="{{ route('tu.prakerin.index') }}" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap"><x-heroicon-o-arrow-left class="w-5 h-5" /> Kembali</a>
</div>

@if(session('import_result'))
    @php $res = session('import_result'); @endphp
    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-3">Hasil Import</h2>
        <div class="flex gap-4 mb-3">
            <span class="inline-flex items-center gap-1 px-3 py-1 bg-success/10 text-success font-bold text-sm rounded-pill">✓ {{ $res['success'] }} berhasil</span>
            @if($res['failed'] > 0)<span class="inline-flex items-center gap-1 px-3 py-1 bg-coral/10 text-coral font-bold text-sm rounded-pill">✗ {{ $res['failed'] }} gagal</span>@endif
        </div>
        @if(!empty($res['errors']))
        <div class="bg-coral/5 rounded-card p-3 max-h-48 overflow-y-auto text-sm">
            @foreach($res['errors'] as $e)<p class="text-coral-dark mb-1">{{ $e }}</p>@endforeach
        </div>
        @endif
    </div>
@endif

<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Upload File</h2>
    <form method="POST" action="{{ route('tu.prakerin.do-import') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Pilih File (CSV / XLSX)</label>
            <input type="file" name="file" accept=".csv,.xlsx" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-pill file:border-0 file:text-sm file:font-bold file:bg-teal-primary file:text-white hover:file:bg-teal-primary-dark" required>
            @error('file')<p class="text-coral text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <button class="btn-primary">Import Data</button>
    </form>
</div>

<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-sky">
    <h2 class="text-lg font-extrabold text-teal-primary-dark mb-3">Panduan Format File</h2>
    <p class="text-sm text-gray-500 mb-2">Baris pertama harus berisi header. Kolom wajib: <strong>nama_perusahaan</strong>.</p>
    <p class="text-sm text-gray-500 mb-2">Kolom opsional: PIC, kontak, alamat, tanggal_mulai, tanggal_selesai, keterangan.</p>
    <div class="bg-surface-base rounded-card p-3 text-sm font-mono text-gray-600 mt-2">
nama_perusahaan,PIC,kontak,alamat,tanggal_mulai,tanggal_selesai,keterangan<br>
PT Teknologi Maju,Budi,08123456789,Jl. Raya No.10,2026-01-10,2026-03-10,<br>
CV Karya Mandiri,Siti,08123456788,Jl. Mawar No.5,2026-02-01,2026-04-30,
    </div>
</div>
</div>
@endsection
