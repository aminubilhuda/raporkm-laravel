@extends('layouts.tu')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-arrow-right-on-rectangle class="w-7 h-7"/> Mutasi Masuk</h1><p class="mt-1 text-sm text-gray-500">Catat siswa pindahan masuk.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
    <form method="POST" action="{{route('tu.mutasi-masuk.store')}}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">@csrf
    <select name="siswa_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Pilih Siswa</option>@foreach($siswas as $s)<option value="{{$s->id}}">{{$s->nama_siswa}}</option>@endforeach</select>
    <select name="kelas_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Kelas Tujuan</option>@foreach($kelass as $k)<option value="{{$k->id}}">{{$k->nama_kelas}}</option>@endforeach</select>
    <div><x-text-input name="asal_sekolah" placeholder="Asal Sekolah" class="block w-full" required/></div>
    <div><x-text-input name="tanggal_masuk" type="date" class="block w-full" required/></div>
    <div><x-text-input name="alasan" placeholder="Alasan" class="block w-full"/></div>
    <button class="btn-primary md:col-span-2">Catat Mutasi Masuk</button></form>
</div>
<div class="bg-white rounded-card shadow-card overflow-hidden">
    <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Siswa</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden sm:table-cell">Kelas</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Asal Sekolah</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Tanggal</th><th class="px-4 py-3 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
    <tbody>@forelse($mutasi as $m)<tr><td class="px-4 py-3 font-bold">{{$m->siswa->nama_siswa??'-'}}</td><td class="px-4 py-3 hidden sm:table-cell">{{$m->kelas->nama_kelas??'-'}}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{$m->asal_sekolah}}</td><td class="px-4 py-3">{{$m->tanggal_masuk?->format('d/m/Y')}}</td><td class="px-4 py-3 text-right"><form method="POST" action="{{route('tu.mutasi-masuk.destroy',$m)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>@empty<tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada data.</td></tr>@endforelse</tbody></table>
    <div class="px-4 py-3 border-t">{{$mutasi->links()}}</div>
</div>
</div>
</div>
@endsection