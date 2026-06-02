@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-bell-alert class="w-7 h-7"/> Pengingat</h1><p class="mt-1 text-sm text-gray-500">Kelola pengingat otomatis.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <form method="POST" action="{{route('tu.pengingat.store')}}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">@csrf
    <div><x-text-input name="judul" placeholder="Judul" class="block w-full" required/></div>
    <div><x-text-input name="pesan" placeholder="Pesan" class="block w-full" required/></div>
    <select name="untuk_role" class="block w-full border-teal-primary/20 rounded-card" required><option value="">Untuk</option><option value="2">TU</option><option value="3">Guru</option><option value="4">Kepsek</option></select>
    <div><x-text-input name="tanggal" type="date" class="block w-full" required/></div>
    <button class="btn-primary">Tambah</button></form>
</div>
<div class="bg-white rounded-card shadow-card overflow-hidden"><table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Judul</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Untuk</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Tanggal</th><th class="px-4 py-3 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
<tbody>@forelse($pengingat as $p)<tr><td class="px-4 py-3 font-bold">{{$p->judul}}</td><td class="px-4 py-3 hidden md:table-cell"><span class="px-2 py-0.5 rounded-pill text-xs font-bold bg-teal-primary/10 text-teal-primary">{{$p->untuk_role==2?'TU':($p->untuk_role==4?'Kepsek':'Guru')}}</span></td><td class="px-4 py-3">{{$p->tanggal?->format('d/m/Y')}}</td><td class="px-4 py-3 text-right"><form method="POST" action="{{route('tu.pengingat.destroy',$p)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>@empty<tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada pengingat.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection