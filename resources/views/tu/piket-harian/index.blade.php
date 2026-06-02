@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-calendar-days class="w-7 h-7"/> Piket Harian</h1><p class="mt-1 text-sm text-gray-500">Kelola jadwal piket guru.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <form method="POST" action="{{route('tu.piket-harian.store')}}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">@csrf
    <select name="user_id" class="block w-full border-teal-primary/20 rounded-card" required><option value="">Pilih Guru</option>@foreach($gurus as $g)<option value="{{$g->id}}">{{$g->nama}}</option>@endforeach</select>
    <select name="hari_id" class="block w-full border-teal-primary/20 rounded-card" required><option value="">Hari</option>@foreach([1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu'] as $i=>$h)<option value="{{$i}}">{{$h}}</option>@endforeach</select>
    <button class="btn-primary">Tambah</button></form>
</div>
<div class="bg-white rounded-card shadow-card overflow-hidden"><table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Guru</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Hari</th><th class="px-4 py-3 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
<tbody>@forelse($pikets as $p)<tr><td class="px-4 py-3 font-bold">{{$p->user->nama??'-'}}</td><td class="px-4 py-3">{{['','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][$p->hari_id]??'-'}}</td><td class="px-4 py-3 text-right"><form method="POST" action="{{route('tu.piket-harian.destroy',$p)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>@empty<tr><td colspan="3" class="px-4 py-12 text-center text-gray-400">Belum ada data.</td></tr>@endforelse</tbody></table></div></div></div>
@endsection