@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-trophy class="w-7 h-7"/> Ekstrakurikuler</h1><p class="mt-1 text-sm text-gray-500">Kelola eskul dan pembina.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Eskul</h2>
    <form method="POST" action="{{route('tu.ekstra.store')}}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end mb-4">@csrf
        <div><x-text-input name="nama_eskul" placeholder="Nama Eskul" class="block w-full" required/></div>
        <div><x-text-input name="keterangan" placeholder="Keterangan" class="block w-full"/></div>
        <button class="btn-primary">Tambah</button>
    </form>
    @foreach($eskuls as $e)
    <div class="border-t border-gray-100 pt-4 mt-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="font-extrabold text-teal-primary-dark">{{$e->nama_eskul}} <span class="text-xs text-gray-400 font-normal">{{$e->keterangan}}</span></h3>
            <div class="flex gap-1">
                <button onclick="document.getElementById('esk-{{$e->id}}').classList.toggle('hidden')" class="btn-ghost-teal text-xs py-1 px-3">Edit</button>
                <form method="POST" action="{{route('tu.ekstra.destroy',$e)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn-ghost-coral text-xs py-1 px-3">Hapus</button></form>
            </div>
        </div>
        <form id="esk-{{$e->id}}" class="hidden grid grid-cols-1 md:grid-cols-3 gap-3 mb-3" method="POST" action="{{route('tu.ekstra.update',$e)}}">@csrf @method('PUT')<x-text-input name="nama_eskul" :value="$e->nama_eskul" class="block w-full" required/><x-text-input name="keterangan" :value="$e->keterangan" class="block w-full"/><button class="btn-primary">Simpan</button></form>
        {{-- Pembina --}}
        <form method="POST" action="{{route('tu.ekstra.pembina.store')}}" class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-end ml-4">@csrf
            <input type="hidden" name="eskul_id" value="{{$e->id}}">
            <select name="user_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Pilih Pembina</option>@foreach($gurus as $g)<option value="{{$g->id}}">{{$g->nama}}</option>@endforeach</select>
            <button class="btn-primary text-xs">Tambah Pembina</button>
        </form>
        @if($e->pembinaEskul->count())
        <p class="text-xs text-gray-500 ml-4 mt-1">Pembina: @foreach($e->pembinaEskul as $p){{$p->user->nama}} <form method="POST" action="{{route('tu.ekstra.pembina.destroy',$p)}}" class="inline">@csrf @method('DELETE')<button class="text-coral hover:underline ml-1">✕</button></form>@endforeach</p>
        @endif
    </div>
    @endforeach
</div>
</div>
@endsection