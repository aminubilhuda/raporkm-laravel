@extends('layouts.guru')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-clipboard-document-list class="w-7 h-7" /> Rekap Presensi</h1><p class="mt-1 text-sm text-gray-500">Ringkasan kehadiran siswa per kelas.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div><select name="kelas_id" onchange="this.form.submit()" class="block w-full border-coral/20 rounded-card"><option value="">Pilih Kelas</option>@foreach($kelass as $k)<option value="{{$k->id}}" {{$kelasId==$k->id?'selected':''}}>{{$k->nama_kelas}}</option>@endforeach</select></div></form>
</div>
@if($kelasId)
<div class="bg-white rounded-card shadow-card overflow-hidden"><div class="overflow-x-auto">
<table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Siswa</th><th class="px-4 py-3 font-extrabold text-xs uppercase text-center text-success">Hadir</th><th class="px-4 py-3 font-extrabold text-xs uppercase text-center text-gold-dark">Sakit</th><th class="px-4 py-3 font-extrabold text-xs uppercase text-center text-sky">Izin</th><th class="px-4 py-3 font-extrabold text-xs uppercase text-center text-coral">Alpa</th></tr></thead>
<tbody>@forelse($rekap as $siswaId => $items)<tr><td class="px-4 py-3 font-bold">{{$items->first()->siswa->nama_siswa??$siswaId}}</td>
<td class="px-4 py-3 text-center text-success font-bold">{{$items->where('jenis_absen_id',1)->sum('total')??0}}</td>
<td class="px-4 py-3 text-center text-gold-dark font-bold">{{$items->where('jenis_absen_id',2)->sum('total')??0}}</td>
<td class="px-4 py-3 text-center text-sky font-bold">{{$items->where('jenis_absen_id',3)->sum('total')??0}}</td>
<td class="px-4 py-3 text-center text-coral font-bold">{{$items->where('jenis_absen_id',4)->sum('total')??0}}</td></tr>@empty<tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada data presensi.</td></tr>@endforelse</tbody></table></div></div>
@endif
</div>
@endsection
