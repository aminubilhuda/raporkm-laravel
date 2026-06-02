@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div><h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2"><x-heroicon-o-folder-open class="w-7 h-7"/> Proyek Kelas P5</h1><p class="mt-1 text-sm text-gray-500">Kelola proyek P5 per kelas.</p></div>
    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
        <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tambah Proyek</h2>
        <form method="POST" action="{{route('tu.p5bk.proyek.store')}}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">@csrf
            <div><select name="kelas_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Kelas</option>@foreach($kelass as $k)<option value="{{$k->id}}">{{$k->nama_kelas}}</option>@endforeach</select></div>
            <div><select name="proyek_tema_id" class="mt-1 block w-full border-teal-primary/20 rounded-card" required><option value="">Tema</option>@foreach($temas as $t)<option value="{{$t->id}}">{{$t->nama_tema}}</option>@endforeach</select></div>
            <div><x-text-input name="judul" placeholder="Judul Proyek" class="block w-full"/></div>
            <div><select name="user_id" class="mt-1 block w-full border-teal-primary/20 rounded-card"><option value="">Guru</option>@foreach($gurus as $g)<option value="{{$g->id}}">{{$g->nama}}</option>@endforeach</select></div>
            <button class="btn-primary">Tambah</button>
        </form>
    </div>
    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm"><thead class="bg-surface-base text-left"><tr><th class="px-4 py-3 font-extrabold text-xs uppercase">Kelas</th><th class="px-4 py-3 font-extrabold text-xs uppercase">Tema</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden md:table-cell">Judul</th><th class="px-4 py-3 font-extrabold text-xs uppercase hidden sm:table-cell">Guru</th><th class="px-4 py-3 text-right font-extrabold text-xs uppercase">Aksi</th></tr></thead>
        <tbody class="divide-y">@forelse($proyeks as $p)<tr class="hover:bg-teal-bg/30"><td class="px-4 py-3 font-bold">{{$p->kelas->nama_kelas??'-'}}</td><td class="px-4 py-3">{{$p->proyekTema->nama_tema??'-'}}</td><td class="px-4 py-3 hidden md:table-cell text-gray-500">{{$p->judul??'-'}}</td><td class="px-4 py-3 hidden sm:table-cell text-gray-500">{{$p->user->nama??'-'}}</td><td class="px-4 py-3 text-right"><form method="POST" action="{{route('tu.p5bk.proyek.destroy',$p)}}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-coral hover:bg-coral/5 rounded"><x-heroicon-o-trash class="w-4 h-4"/></button></form></td></tr>@empty<tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Belum ada proyek.</td></tr>@endforelse</tbody></table>
        <div class="px-4 py-3 border-t">{{$proyeks->links()}}</div>
    </div>
</div>
@endsection