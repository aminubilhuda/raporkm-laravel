@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-star class="w-7 h-7"/> P5BK</h1><p class="mt-1 text-sm text-gray-500">Kelola dimensi, elemen, sub-elemen, tema, dan proyek P5.</p></div>

    {{-- Dimensi --}}
    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Dimensi Profil Pancasila</h2>
        <form method="POST" action="{{ route('tu.p5bk.dimensi.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end mb-4">
            @csrf
            <div><x-text-input name="nama" placeholder="Nama Dimensi" class="block w-full" required /></div>
            <div><x-text-input name="keterangan" placeholder="Keterangan" class="block w-full" /></div>
            <div><x-text-input name="urutan" type="number" placeholder="Urutan" class="block w-full" /></div>
            <button class="btn-primary">Tambah</button>
        </form>
        <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-2 font-extrabold text-xs uppercase">Nama</th><th class="px-4 py-2 font-extrabold text-xs uppercase hidden md:table-cell">Keterangan</th><th class="px-4 py-2 font-extrabold text-xs uppercase">Urutan</th><th class="px-4 py-2 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
        <tbody class="divide-y">@foreach($dimensi as $d)<tr class="hover:bg-teal-bg/30"><td class="px-4 py-2 font-bold">{{$d->nama}}</td><td class="px-4 py-2 hidden md:table-cell text-gray-500">{{$d->keterangan??'-'}}</td><td class="px-4 py-2">{{$d->urutan}}</td><td class="px-4 py-2 text-right"><button onclick="document.getElementById('de-{{$d->id}}').classList.toggle('hidden')" class="p-1 text-sky hover:bg-sky/5 rounded"><x-heroicon-o-pencil-square class="w-4 h-4"/></button><form method="POST" action="{{route('tu.p5bk.dimensi.destroy',$d)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>
        <tr id="de-{{$d->id}}" class="hidden bg-surface-base/50"><td colspan="4" class="px-4 py-2"><form method="POST" action="{{route('tu.p5bk.dimensi.update',$d)}}" class="grid grid-cols-1 md:grid-cols-4 gap-3">@csrf @method('PUT')<x-text-input name="nama" :value="$d->nama" class="block w-full" required/><x-text-input name="keterangan" :value="$d->keterangan" class="block w-full"/><x-text-input name="urutan" type="number" :value="$d->urutan" class="block w-full"/><button class="btn-primary">Simpan</button></form></td></tr>@endforeach</tbody></table>
    </div>

    {{-- Elemen --}}
    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Elemen</h2>
        <form method="POST" action="{{route('tu.p5bk.elemen.store')}}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end mb-4">
            @csrf
            <div>
                <select name="dimensi_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required>
                    <option value="">Dimensi</option>@foreach($dimensi as $d)<option value="{{$d->id}}">{{$d->nama}}</option>@endforeach</select>
            </div>
            <div><x-text-input name="nama" placeholder="Nama Elemen" class="block w-full" required/></div>
            <div><x-text-input name="keterangan" placeholder="Keterangan" class="block w-full"/></div>
            <div><x-text-input name="urutan" type="number" placeholder="Urutan" class="block w-full"/></div>
            <button class="btn-primary">Tambah</button>
        </form>
        <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-2 font-extrabold text-xs uppercase">Dimensi</th><th class="px-4 py-2 font-extrabold text-xs uppercase">Nama</th><th class="px-4 py-2 font-extrabold text-xs uppercase">Urutan</th><th class="px-4 py-2 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
        <tbody class="divide-y">@foreach($elemens as $e)<tr class="hover:bg-teal-bg/30"><td class="px-4 py-2 text-gray-500">{{$e->dimensi->nama??'-'}}</td><td class="px-4 py-2 font-bold">{{$e->nama}}</td><td class="px-4 py-2">{{$e->urutan}}</td><td class="px-4 py-2 text-right"><form method="POST" action="{{route('tu.p5bk.elemen.destroy',$e)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>@endforeach</tbody></table>
    </div>

    {{-- Sub-Elemen --}}
    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-sky">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Sub-Elemen</h2>
        <form method="POST" action="{{route('tu.p5bk.sub.store')}}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end mb-4">@csrf
            <div><select name="elemen_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Elemen</option>@foreach($elemens as $e)<option value="{{$e->id}}">{{$e->dimensi->nama??''}} > {{$e->nama}}</option>@endforeach</select></div>
            <div><x-text-input name="nama" placeholder="Nama" class="block w-full" required/></div>
            <div><x-text-input name="capaian" placeholder="Capaian" class="block w-full"/></div>
            <div><x-text-input name="urutan" type="number" placeholder="Urutan" class="block w-full"/></div>
            <button class="btn-primary">Tambah</button>
        </form>
        <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-2 font-extrabold text-xs uppercase">Elemen</th><th class="px-4 py-2 font-extrabold text-xs uppercase">Nama</th><th class="px-4 py-2 font-extrabold text-xs uppercase">Urutan</th><th class="px-4 py-2 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
        <tbody class="divide-y">@foreach($subs as $s)<tr class="hover:bg-teal-bg/30"><td class="px-4 py-2 text-gray-500">{{$s->elemen->nama??'-'}}</td><td class="px-4 py-2 font-bold">{{$s->nama}}</td><td class="px-4 py-2">{{$s->urutan}}</td><td class="px-4 py-2 text-right"><form method="POST" action="{{route('tu.p5bk.sub.destroy',$s)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>@endforeach</tbody></table>
    </div>
</div>
@endsection