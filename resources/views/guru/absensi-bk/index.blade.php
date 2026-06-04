@extends('layouts.guru')
@section('content')
<div class="space-y-6">
<div><h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2"><x-heroicon-o-clipboard-document class="w-7 h-7" /> Absensi BK</h1><p class="mt-1 text-sm text-gray-500">Catatan bimbingan konseling.</p></div>
<div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
        <div><label class="block text-xs font-extrabold uppercase text-coral-dark mb-1">Kelas</label><select name="kelas_id" onchange="this.form.submit()" class="block w-full border-coral/20 rounded-card"><option value="">Pilih Kelas</option>@foreach($kelass as $k)<option value="{{$k->id}}" {{$kelasId==$k->id?'selected':''}}>{{$k->nama_kelas}}</option>@endforeach</select></div>
        <div><label class="block text-xs font-extrabold uppercase text-coral-dark mb-1">Tanggal</label><input type="date" name="tanggal" value="{{$tanggal}}" onchange="this.form.submit()" class="block w-full border-coral/20 rounded-card"></div>
    </form>
</div>
@if($kelasId && $siswa->isNotEmpty())
<div class="bg-white rounded-card shadow-card overflow-hidden">
    <form method="POST" action="{{route('guru.absensi-bk.store')}}">@csrf
    <input type="hidden" name="kelas_id" value="{{$kelasId}}">
    <input type="hidden" name="tanggal" value="{{$tanggal}}">
    <div class="overflow-x-auto">
    <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Siswa</th><th class="px-4 py-3 font-extrabold text-xs uppercase text-center">NISN</th>@foreach($jenisAbsens as $ja)<th class="px-4 py-3 font-extrabold text-xs uppercase text-center">{{$ja->nama}}</th>@endforeach</tr></thead>
    <tbody class="divide-y">@foreach($siswa as $sk)<tr class="hover:bg-coral-bg/30"><td class="px-4 py-3 font-bold text-sm">{{$sk->siswa->nama_siswa}}</td><td class="px-4 py-3 text-center text-gray-500">{{$sk->siswa->nisn}}</td>
    @foreach($jenisAbsens as $ja)
    @php $checked = ($presensiHariIni->get($sk->siswa_id)?->jenis_absen_id ?? 1) == $ja->id; @endphp
    <td class="px-4 py-3 text-center"><input type="radio" name="jenis_absen_id[{{$sk->siswa_id}}]" value="{{$ja->id}}" {{$checked?'checked':''}} class="w-4 h-4 text-coral border-coral/30 focus:ring-coral"></td>
    @endforeach
    <input type="hidden" name="siswa_id[]" value="{{$sk->siswa_id}}">
    </tr>@endforeach</tbody></table></div>
    <div class="px-4 py-3 border-t flex justify-end"><button class="btn-primary-coral">Simpan Absensi BK</button></div>
    </form>
</div>
@endif
</div>
@endsection
