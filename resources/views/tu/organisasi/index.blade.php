@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-flag class="w-7 h-7"/> Organisasi</h1><p class="mt-1 text-sm text-gray-500">Kelola data organisasi siswa.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <form method="POST" action="{{route('tu.organisasi.store')}}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">@csrf
    <div><x-text-input name="nama_organisasi" placeholder="Nama Organisasi" class="block w-full" required/></div>
    <div><x-text-input name="keterangan" placeholder="Keterangan" class="block w-full"/></div>
    <button class="btn-primary">Tambah</button></form>
</div>
<div class="bg-white rounded-card shadow-card overflow-hidden"><table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Nama</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Keterangan</th><th class="px-4 py-3 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
<tbody>@forelse($organisasi as $o)<tr><td class="px-4 py-3 font-bold">{{$o->nama_organisasi}}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{$o->keterangan??'-'}}</td><td class="px-4 py-3 text-right"><button onclick="document.getElementById('org-{{$o->id}}').classList.toggle('hidden')" class="p-1 text-sky hover:bg-sky/5 rounded"><x-heroicon-o-pencil-square class="w-4 h-4"/></button><form method="POST" action="{{route('tu.organisasi.destroy',$o)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>
<tr id="org-{{$o->id}}" class="hidden bg-surface-base/50"><td colspan="3" class="px-4 py-2"><form method="POST" action="{{route('tu.organisasi.update',$o)}}" class="grid grid-cols-1 md:grid-cols-3 gap-3">@csrf @method('PUT')<x-text-input name="nama_organisasi" :value="$o->nama_organisasi" class="block w-full" required/><x-text-input name="keterangan" :value="$o->keterangan" class="block w-full"/><button class="btn-primary">Simpan</button></form></td></tr>@empty<tr><td colspan="3" class="px-4 py-12 text-center text-gray-400">Belum ada data.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection