@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-printer class="w-7 h-7" />
            Cetak Rapor
        </h1>
        <p class="mt-1 text-sm text-gray-500">Cetak rapor siswa untuk kelas yang Anda wali.</p>
    </div>

    @if($kelasWali->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas Wali</label>
        <div class="flex flex-wrap gap-2">
            @foreach($kelasWali as $k)
                <a href="{{ route('guru.cetak-rapor.index', $k) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $authorized && $kelas->id === $k->id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $k->nama_kelas }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($authorized && $siswa->isNotEmpty())
    <form method="POST" action="{{ route('guru.cetak-rapor.cetak', $kelas->id) }}" target="_blank" class="bg-white rounded-card shadow-card overflow-hidden">
        @csrf
        <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-extrabold text-gray-700">Daftar Siswa {{ $kelas->nama_kelas }}</h3>
            <div class="flex items-center gap-2">
                <select name="jenis" class="text-sm border-gray-300 rounded px-2 py-1 text-gray-700 font-bold outline-none focus:border-coral focus:ring-coral/30">
                    <option value="semester">Rapor Semester</option>
                    <option value="mid">Rapor Mid Semester</option>
                </select>
                <button type="submit" class="bg-coral text-white px-4 py-1.5 rounded-pill font-bold text-sm hover:bg-coral-dark transition-colors inline-flex items-center gap-1">
                    <x-heroicon-s-printer class="w-4 h-4" /> Cetak
                </button>
            </div>
        </div>
        
        <div class="p-4 bg-yellow-50 text-yellow-800 text-sm flex gap-2 items-start border-b border-yellow-100">
            <x-heroicon-o-information-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <div>Pilih satu siswa untuk mencetak file PDF langsung. Pilih banyak siswa untuk mengunduh arsip ZIP berisi semua PDF rapor.</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" id="checkAll" class="rounded text-coral focus:ring-coral">
                        </th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">NISN/NIS</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($siswa as $sk)
                    <tr class="hover:bg-coral/5 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="siswa_id[]" value="{{ $sk->siswa_id }}" class="siswa-checkbox rounded text-coral focus:ring-coral">
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-700 text-sm">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $sk->siswa->nisn ?? '-' }} / {{ $sk->siswa->nis ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <button type="submit" name="single_siswa" value="{{ $sk->siswa_id }}" class="text-coral hover:text-coral-dark font-bold text-xs inline-flex items-center gap-1" onclick="selectSingle(this, '{{ $sk->siswa_id }}')">
                                <x-heroicon-o-document-arrow-down class="w-4 h-4" /> Cetak PDF
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
    
    <script>
        document.getElementById('checkAll').addEventListener('change', function() {
            document.querySelectorAll('.siswa-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
        });
        
        function selectSingle(btn, siswaId) {
            // Uncheck all first
            document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = false);
            // Check only this one
            document.querySelector(`.siswa-checkbox[value="${siswaId}"]`).checked = true;
        }
    </script>
    
    @elseif($authorized && $siswa->isEmpty())
        <div class="text-center py-12 text-gray-400">Belum ada siswa di kelas ini.</div>
    @elseif($kelasWali->isNotEmpty())
        <div class="text-center py-12 text-gray-400">Pilih kelas terlebih dahulu.</div>
    @else
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <x-heroicon-o-printer class="w-16 h-16 text-gray-300 mb-4" />
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Bukan Wali Kelas</h2>
            <p class="text-gray-400">Hanya wali kelas yang dapat mencetak rapor serentak.</p>
        </div>
    @endif
</div>
@endsection