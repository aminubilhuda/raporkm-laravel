@extends('layouts.guru')
@section('content')
@php
    $dimensiList = \App\Models\DimensiKokurikuler::orderBy('nama')->get();
@endphp
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-sparkles class="w-7 h-7" /> Kokurikuler</h1><p class="mt-1 text-sm text-gray-500">Dimensi kegiatan kokurikuler.</p></div>
@if($dimensiList->isEmpty())
<div class="flex flex-col items-center justify-center min-h-[40vh] text-center"><x-heroicon-o-sparkles class="w-16 h-16 text-gray-300 mb-4" /><p class="text-gray-400 max-w-md">Data kokurikuler dikelola oleh Tata Usaha. Guru dapat melakukan penilaian melalui menu <a href="{{ route('guru.penilaian-kokurikuler.index') }}" class="text-coral font-bold hover:underline">Penilaian Kokurikuler</a>.</p></div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($dimensiList as $d)
    <div class="bg-white rounded-card shadow-card p-5 border-l-[6px] border-l-coral">
        <h3 class="font-extrabold text-coral-dark">{{ $d->nama }}</h3>
        @if($d->deskripsi)<p class="text-sm text-gray-500 mt-1">{{ $d->deskripsi }}</p>@endif
        <a href="{{ route('guru.penilaian-kokurikuler.index') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-coral hover:underline">Nilai <x-heroicon-o-arrow-right class="w-4 h-4" /></a>
    </div>
    @endforeach
</div>
@endif
</div>
@endsection
