@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-user-group class="w-7 h-7"/> Peserta Prakerin</h1><a href="{{route('tu.prakerin.index')}}" class="btn-ghost-teal text-sm">← Kembali</a></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <form method="POST" action="{{route('tu.prakerin.peserta.store')}}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">@csrf
    <input type="hidden" name="prakerin_id" value="{{$prakerinId}}">
    <select name="kelas_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Kelas</option>@foreach(\App\Models\Kelas::orderBy('nama_kelas')->get() as $k)<option value="{{$k->id}}">{{$k->nama_kelas}}</option>@endforeach</select>
    <select name="siswa_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Siswa</option>@foreach($siswas as $s)<option value="{{$s->id}}">{{$s->nama_siswa}}</option>@endforeach</select>
    <select name="user_id" class="mt-1 block w-full border-teal-primary/20 rounded-card"><option value="">Pembimbing</option>@foreach($gurus as $g)<option value="{{$g->id}}">{{$g->nama}}</option>@endforeach</select>
    <button class="btn-primary">Tambah Peserta</button></form>
</div>
<div class="bg-white rounded-card shadow-card overflow-hidden">
    <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Siswa</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden sm:table-cell">Kelas</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Pembimbing</th><th class="px-4 py-3 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
    <tbody>@forelse($peserta as $p)<tr><td class="px-4 py-3 font-bold">{{$p->siswa->nama_siswa??'-'}}</td><td class="px-4 py-3 hidden sm:table-cell">{{$p->kelas->nama_kelas??'-'}}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{$p->user->nama??'-'}}</td><td class="px-4 py-3 text-right"><form method="POST" action="{{route('tu.prakerin.peserta.destroy',$p)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>@empty<tr><td colspan="4" class="px-4 py-12 text-center text-gray-400">Belum ada peserta.</td></tr>@endforelse</tbody></table>
</div>
</div>
</div>
@endsection