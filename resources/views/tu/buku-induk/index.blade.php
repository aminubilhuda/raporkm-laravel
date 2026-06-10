@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-book-open class="w-7 h-7" />
                Buku Induk Siswa
            </h1>
            <p class="mt-1 text-sm text-gray-500">Data induk seluruh siswa secara komprehensif.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('tu.buku-induk.pdf', request()->query()) }}" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap bg-coral hover:bg-coral/90" target="_blank">
                <x-heroicon-o-document-arrow-down class="w-5 h-5" /> PDF
            </a>
            <button type="button" onclick="window.print()" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap bg-sky hover:bg-sky/90">
                <x-heroicon-o-printer class="w-5 h-5" /> Cetak
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="bg-cream rounded-field shadow-card p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Pencarian</label>
                <input class="w-full border-teal-primary/20 rounded-field text-sm py-2 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 bg-cream">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Kelas</label>
                <select class="w-full border-teal-primary/20 rounded-field text-sm py-2 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 bg-cream">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelass as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }} ({{ $k->tingkat->nama ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary px-4 py-2 text-sm">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 inline-block mr-1" /> Filter
                </button>
                <a href="{{ route('tu.buku-induk.index') }}" class="px-4 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-field transition-colors">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-cream rounded-field shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-10">No</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">NISN</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden lg:table-cell">NIS</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">JK</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden lg:table-cell">Tgl Lahir</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden xl:table-cell">Agama</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden xl:table-cell">Alamat</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden 2xl:table-cell">Nama Ayah</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden 2xl:table-cell">Nama Ibu</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">Kelas</th>
                        <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($siswa as $i => $s)
                    <tr class="hover:bg-teal-bg/30">
                        <td class="px-3 py-2.5 text-gray-400 text-xs">{{ $siswa->firstItem() + $i }}</td>
                        <td class="px-3 py-2.5 text-teal-primary font-bold text-xs">{{ $s->nisn }}</td>
                        <td class="px-3 py-2.5 text-gray-500 text-xs hidden lg:table-cell">{{ $s->nis }}</td>
                        <td class="px-3 py-2.5">
                            <button type="button" onclick="showDetail({{ $s->id }})" class="font-bold text-gray-800 hover:text-teal-primary text-left">
                                {{ $s->nama_siswa }}
                            </button>
                        </td>
                        <td class="px-3 py-2.5 text-gray-500 text-xs hidden md:table-cell">
                            @if($s->kelamin == 1)
                                <span class="text-sky font-bold">L</span>
                            @elseif($s->kelamin == 2)
                                <span class="text-coral font-bold">P</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-gray-500 text-xs hidden lg:table-cell">
                            {{ $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-3 py-2.5 text-gray-500 text-xs hidden xl:table-cell">
                            {{ [1=>'Islam',2=>'Kristen',3=>'Katolik',4=>'Hindu',5=>'Buddha',6=>'Konghucu'][$s->agama] ?? '-' }}
                        </td>
                        <td class="px-3 py-2.5 text-gray-500 text-xs max-w-[150px] truncate hidden xl:table-cell" title="{{ $s->alamat }}">
                            {{ $s->alamat ?: '-' }}
                        </td>
                        <td class="px-3 py-2.5 text-gray-500 text-xs hidden 2xl:table-cell">{{ $s->nama_ayah ?: '-' }}</td>
                        <td class="px-3 py-2.5 text-gray-500 text-xs hidden 2xl:table-cell">{{ $s->nama_ibu ?: '-' }}</td>
                        <td class="px-3 py-2.5 text-xs hidden md:table-cell">
                            @php
                                $kelasAktif = $s->siswaKelas->firstWhere('status', 'aktif');
                            @endphp
                            @if($kelasAktif)
                                <span class="px-2 py-0.5 rounded-field bg-teal-primary/10 text-teal-primary font-bold text-xs">
                                    {{ $kelasAktif->kelas->nama_kelas ?? '-' }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5">
                            @if($s->aktif)
                                <span class="px-2 py-0.5 rounded-field bg-teal-primary/10 text-teal-primary font-bold text-xs">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded-field bg-coral/10 text-coral font-bold text-xs">Non-aktif</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-4 py-12 text-center text-gray-400">
                            <x-heroicon-o-academic-cap class="w-12 h-12 mx-auto mb-2 text-gray-300" />
                            Tidak ada data siswa ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Menampilkan {{ $siswa->firstItem() ?? 0 }}–{{ $siswa->lastItem() ?? 0 }} dari {{ $siswa->total() }} siswa
            </p>
            {{ $siswa->links() }}
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div id="modal-detail" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeDetail()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="relative bg-cream rounded-field shadow-card w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                <h3 class="text-lg font-extrabold text-teal-primary-dark">Detail Buku Induk</h3>
                <button onclick="closeDetail()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
            <div id="detail-loading" class="px-6 py-16 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-teal-primary border-r-transparent"></div>
                <p class="mt-3 text-sm text-gray-500">Memuat data...</p>
            </div>
            <div id="detail-content" class="px-6 py-5 space-y-5 hidden">
                <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                    <div class="w-16 h-16 rounded-full bg-teal-primary/10 flex items-center justify-center flex-shrink-0">
                        <span id="d-avatar" class="text-2xl font-extrabold text-teal-primary"></span>
                    </div>
                    <div>
                        <h4 id="d-nama" class="text-xl font-extrabold text-gray-800"></h4>
                        <p class="text-sm text-gray-500">NISN: <span id="d-nisn" class="font-bold text-teal-primary"></span> &middot; NIS: <span id="d-nis" class="font-bold"></span></p>
                    </div>
                    <div class="ml-auto">
                        <span id="d-status" class="px-3 py-1 rounded-field text-xs font-bold"></span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Pribadi</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">JK</span><span id="d-jk" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Tempat Lahir</span><span id="d-tplahir" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Tgl Lahir</span><span id="d-tglahir" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Agama</span><span id="d-agama" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Jurusan</span><span id="d-jurusan" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Kontak</span><span id="d-kontak" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">NIK PD</span><span id="d-nikpd" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">NKK</span><span id="d-nkk" class="font-medium"></span></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Alamat & Status</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Alamat</span><span id="d-alamat" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Sekolah Asal</span><span id="d-sekolah" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Terima Tgl</span><span id="d-terima" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Terima Kelas</span><span id="d-terimakelas" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Jenis</span><span id="d-jenis" class="font-medium"></span></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Ayah</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Nama</span><span id="d-ayah" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">NIK</span><span id="d-nikayah" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Pekerjaan</span><span id="d-pkayah" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Kontak</span><span id="d-ktayah" class="font-medium"></span></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Ibu</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Nama</span><span id="d-ibu" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">NIK</span><span id="d-nikibu" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Pekerjaan</span><span id="d-pkibu" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Kontak</span><span id="d-ktibu" class="font-medium"></span></div>
                        </div>
                    </div>
                </div>
                <div id="d-wali-section" class="hidden space-y-3">
                    <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Wali</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Nama</span><span id="d-wali" class="font-medium"></span></div>
                        <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Pekerjaan</span><span id="d-pkwali" class="font-medium"></span></div>
                        <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Kontak</span><span id="d-ktwali" class="font-medium"></span></div>
                        <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Alamat</span><span id="d-alwali" class="font-medium"></span></div>
                    </div>
                </div>
                <div id="d-riwayat-section" class="hidden space-y-3">
                    <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Riwayat Kelas</h5>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-surface-base">
                                <tr>
                                    <th class="px-3 py-2 text-left font-bold text-gray-500">Kelas</th>
                                    <th class="px-3 py-2 text-left font-bold text-gray-500">Tingkat</th>
                                    <th class="px-3 py-2 text-left font-bold text-gray-500">Tahun Pelajaran</th>
                                    <th class="px-3 py-2 text-left font-bold text-gray-500">Semester</th>
                                    <th class="px-3 py-2 text-left font-bold text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody id="d-riwayat-body" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>

<script>
    const agamaMap = {1:'Islam',2:'Kristen',3:'Katolik',4:'Hindu',5:'Buddha',6:'Konghucu'};
    const modal = document.getElementById('modal-detail');
    const loading = document.getElementById('detail-loading');
    const content = document.getElementById('detail-content');

    function openDetail() {
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDetail() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    async function showDetail(id) {
        openDetail();
        try {
            const res = await fetch('{{ url("tu/buku-induk") }}/' + id);
            if (!res.ok) throw new Error('Gagal');
            const d = await res.json();

            document.getElementById('d-avatar').textContent = d.nama_siswa?.charAt(0) ?? '?';
            document.getElementById('d-nama').textContent = d.nama_siswa ?? '-';
            document.getElementById('d-nisn').textContent = d.nisn ?? '-';
            document.getElementById('d-nis').textContent = d.nis ?? '-';

            const st = document.getElementById('d-status');
            if (d.aktif == 1) {
                st.textContent = 'Aktif';
                st.className = 'px-3 py-1 rounded-pill text-xs font-bold bg-teal-primary/10 text-teal-primary';
            } else {
                st.textContent = 'Non-aktif';
                st.className = 'px-3 py-1 rounded-pill text-xs font-bold bg-coral/10 text-coral';
            }

            const s = (id, v) => document.getElementById(id).textContent = v || '-';
            s('d-jk', d.kelamin == 1 ? 'Laki-laki' : d.kelamin == 2 ? 'Perempuan' : '-');
            s('d-tplahir', d.tempat_lahir);
            s('d-tglahir', d.tanggal_lahir ? new Date(d.tanggal_lahir).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : null);
            s('d-agama', agamaMap[d.agama]);
            s('d-jurusan', d.siswa_kelas?.find(sk => sk.status === 'aktif')?.kelas?.kompetensi_keahlian?.nama);
            s('d-kontak', d.kontak_siswa);
            s('d-nikpd', d.nik_pd);
            s('d-nkk', d.nkk);
            s('d-alamat', d.alamat);
            s('d-sekolah', d.sekolah_asal);
            s('d-terima', d.terima_tanggal ? new Date(d.terima_tanggal).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : null);
            s('d-terimakelas', d.terima_kelas);
            s('d-jenis', d.jenis_siswa == 1 ? 'Reguler' : d.jenis_siswa == 2 ? 'Pindahan' : null);
            s('d-ayah', d.nama_ayah);
            s('d-nikayah', d.nik_ayah);
            s('d-pkayah', d.pekerjaan_ayah);
            s('d-ktayah', d.kontak_ayah);
            s('d-ibu', d.nama_ibu);
            s('d-nikibu', d.nik_ibu);
            s('d-pkibu', d.pekerjaan_ibu);
            s('d-ktibu', d.kontak_ibu);

            const waliSection = document.getElementById('d-wali-section');
            if (d.nama_wali) {
                waliSection.classList.remove('hidden');
                s('d-wali', d.nama_wali);
                s('d-pkwali', d.pekerjaan_wali);
                s('d-ktwali', d.kontak_wali);
                s('d-alwali', d.alamat_wali);
            } else {
                waliSection.classList.add('hidden');
            }

            const riwayatSection = document.getElementById('d-riwayat-section');
            const riwayatBody = document.getElementById('d-riwayat-body');
            riwayatBody.innerHTML = '';

            if (d.siswa_kelas && d.siswa_kelas.length > 0) {
                riwayatSection.classList.remove('hidden');
                d.siswa_kelas.forEach(sk => {
                    riwayatBody.innerHTML += `<tr>
                        <td class="px-3 py-2 font-bold">${sk.kelas?.nama_kelas || '-'}</td>
                        <td class="px-3 py-2">${sk.kelas?.tingkat?.nama || '-'}</td>
                        <td class="px-3 py-2">${sk.tahun_pelajaran?.tahun || '-'}</td>
                        <td class="px-3 py-2">${sk.semester?.nama || '-'}</td>
                        <td class="px-3 py-2"><span class="px-2 py-0.5 rounded-field text-xs font-bold ${sk.status == "aktif' ? 'bg-teal-primary/10 text-teal-primary' : 'bg-gray-100 text-gray-500'}">${sk.status}</span></td>
                    </tr>`;
                });
            } else {
                riwayatSection.classList.add('hidden');
            }

            loading.classList.add('hidden');
            content.classList.remove('hidden');
        } catch (err) {
            loading.innerHTML = '<p class="text-coral text-sm">Gagal memuat data siswa.</p>';
        }
    }

    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('bg-black/50')) closeDetail();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeDetail();
    });
</script>
@endsection
