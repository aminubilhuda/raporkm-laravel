@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-sparkles class="w-7 h-7"/> Kokurikuler</h1><p class="mt-1 text-sm text-gray-500">Kelola dimensi kokurikuler dan deskripsi penilaian.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Dimensi</h2>
    <form method="POST" action="{{route('tu.kokurikuler.dimensi.store')}}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end mb-4">@csrf
        <div><x-text-input name="nama" placeholder="Nama Dimensi" class="block w-full" required/></div>
        <div><x-text-input name="keterangan" placeholder="Keterangan" class="block w-full"/></div>
        <button class="btn-primary">Tambah</button>
    </form>
    @foreach($dimensi as $d)
    <div class="border-t border-gray-100 pt-4 mt-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-extrabold text-teal-primary-dark">{{$d->nama}} <span class="text-xs text-gray-400 font-normal">{{$d->keterangan}}</span></h3>
            <div class="flex gap-1">
                <button onclick="document.getElementById('dk-{{$d->id}}').classList.toggle('hidden')" class="p-1 text-sky hover:bg-sky/5 rounded text-xs">Edit</button>
                <form method="POST" action="{{route('tu.kokurikuler.dimensi.destroy',$d)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded text-xs">Hapus</button></form>
            </div>
        </div>
        <form id="dk-{{$d->id}}" class="hidden grid grid-cols-1 md:grid-cols-3 gap-3 mb-3" method="POST" action="{{route('tu.kokurikuler.dimensi.update',$d)}}">@csrf @method('PUT')<x-text-input name="nama" :value="$d->nama" class="block w-full" required/><x-text-input name="keterangan" :value="$d->keterangan" class="block w-full"/><button class="btn-primary">Simpan</button></form>
        <form method="POST" action="{{route('tu.kokurikuler.deskripsi.store')}}" class="grid grid-cols-1 md:grid-cols-4 gap-2 items-end">@csrf
            <input type="hidden" name="dimensi_kokurikuler_id" value="{{$d->id}}">
            <select name="predikat" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Predikat</option><option>Sangat Baik</option><option>Baik</option><option>Cukup</option><option>Perlu Bimbingan</option></select>
            <div class="md:col-span-2"><x-text-input name="deskripsi" placeholder="Deskripsi" class="block w-full" required/></div>
            <button class="btn-primary text-xs">Tambah Deskripsi</button>
        </form>
        @if($d->deskripsiKokurikuler->count())
        <div class="mt-2 space-y-1">
            @foreach($d->deskripsiKokurikuler as $dk)
            <div class="flex items-center justify-between text-xs bg-surface-base px-3 py-1.5 rounded-card">
                <span class="font-bold text-teal-primary">{{$dk->predikat}}:</span>
                <span class="text-gray-600 mx-2 flex-1 truncate">{{$dk->deskripsi}}</span>
                <div class="flex gap-1 flex-shrink-0">
                    <button onclick="document.getElementById('ddk-{{$dk->id}}').classList.toggle('hidden')" class="text-sky hover:underline">Edit</button>
                    <form method="POST" action="{{route('tu.kokurikuler.deskripsi.destroy',$dk)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-coral hover:underline">Hapus</button></form>
                </div>
            </div>
            <form id="ddk-{{$dk->id}}" class="hidden grid grid-cols-1 md:grid-cols-4 gap-2 items-end mt-1" method="POST" action="{{route('tu.kokurikuler.deskripsi.update',$dk)}}">@csrf @method('PUT')
                <input type="hidden" name="dimensi_kokurikuler_id" value="{{$d->id}}">
                <select name="predikat" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>@foreach(['Sangat Baik','Baik','Cukup','Perlu Bimbingan'] as $p)<option value="{{$p}}" {{$dk->predikat==$p?'selected':''}}>{{$p}}</option>@endforeach</select>
                <div class="md:col-span-2"><x-text-input name="deskripsi" :value="$dk->deskripsi" class="block w-full" required/></div>
                <button class="btn-primary text-xs">Simpan</button>
            </form>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach
</div>
</div>
@endsection