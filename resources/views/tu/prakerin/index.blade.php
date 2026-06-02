@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-building-office class="w-7 h-7"/> Prakerin</h1><p class="mt-1 text-sm text-gray-500">Kelola data praktik kerja industri.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Prakerin</h2>
    <form method="POST" action="{{route('tu.prakerin.store')}}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">@csrf
    <div><x-text-input name="nama_perusahaan" placeholder="Nama Perusahaan" class="block w-full" required/></div>
    <div><x-text-input name="PIC" placeholder="PIC" class="block w-full"/></div>
    <div><x-text-input name="kontak" placeholder="Kontak" class="block w-full"/></div>
    <div><x-text-input name="alamat" placeholder="Alamat" class="block w-full"/></div>
    <div><x-text-input name="tanggal_mulai" type="date" class="block w-full"/></div>
    <div><x-text-input name="tanggal_selesai" type="date" class="block w-full"/></div>
    <div class="md:col-span-2"><x-text-input name="keterangan" placeholder="Keterangan" class="block w-full"/></div>
    <button class="btn-primary">Tambah</button></form>
</div>
<div class="bg-white rounded-card shadow-card overflow-hidden">
    <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Perusahaan</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">PIC</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden lg:table-cell">Periode</th><th class="px-4 py-3 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
    <tbody class="divide-y">@forelse($prakerins as $p)<tr class="hover:bg-teal-bg/30"><td class="px-4 py-3 font-bold">{{$p->nama_perusahaan}}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{$p->PIC??'-'}}</td><td class="px-4 py-3 hidden lg:table-cell text-gray-500">{{$p->tanggal_mulai?->format('d/m/Y')}} - {{$p->tanggal_selesai?->format('d/m/Y')}}</td><td class="px-4 py-3 text-right"><a href="{{route('tu.prakerin.peserta',['prakerin_id'=>$p->id])}}" class="p-1 text-teal-primary hover:bg-teal-bg rounded" title="Peserta"><x-heroicon-o-user-group class="w-4 h-4"/></a><button onclick="document.getElementById('pk-{{$p->id}}').classList.toggle('hidden')" class="p-1 text-sky hover:bg-sky/5 rounded"><x-heroicon-o-pencil-square class="w-4 h-4"/></button><form method="POST" action="{{route('tu.prakerin.destroy',$p)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>
    <tr id="pk-{{$p->id}}" class="hidden bg-surface-base/50"><td colspan="4" class="px-4 py-2"><form method="POST" action="{{route('tu.prakerin.update',$p)}}" class="grid grid-cols-1 md:grid-cols-4 gap-3">@csrf @method('PUT')<x-text-input name="nama_perusahaan" :value="$p->nama_perusahaan" class="block w-full" required/><x-text-input name="PIC" :value="$p->PIC" class="block w-full"/><x-text-input name="kontak" :value="$p->kontak" class="block w-full"/><x-text-input name="alamat" :value="$p->alamat" class="block w-full"/><x-text-input name="tanggal_mulai" type="date" :value="$p->tanggal_mulai?->format('Y-m-d')" class="block w-full"/><x-text-input name="tanggal_selesai" type="date" :value="$p->tanggal_selesai?->format('Y-m-d')" class="block w-full"/><x-text-input name="keterangan" :value="$p->keterangan" class="block w-full"/><button class="btn-primary">Simpan</button></form></td></tr>@empty<tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada data.</td></tr>@endforelse</tbody></table>
    <div class="px-4 py-3 border-t">{{$prakerins->links()}}</div>
</div>
</div>
</div>
@endsection