@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-academic-cap class="w-7 h-7" />
                Data Siswa
            </h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data siswa aktif.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('tu.kesiswaan.import') }}" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap bg-sky hover:bg-sky/90">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5" /> Import
            </a>
            <a href="{{ route('tu.kesiswaan.create') }}" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
                <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambah Siswa
            </a>
        </div>
    </div>

    <div class="bg-white rounded-card shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">NISN</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">NIS</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden sm:table-cell">JK</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden md:table-cell">Kelas</th>
                        <th class="px-4 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider hidden lg:table-cell">Jurusan</th>
                        <th class="px-4 py-3 text-right font-extrabold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($siswa as $s)
                    <tr class="hover:bg-teal-bg/30">
                        <td class="px-4 py-3 font-bold text-gray-800">{{ $s->nama_siswa }}</td>
                        <td class="px-4 py-3 text-teal-primary font-bold">{{ $s->nisn }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $s->nis }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $s->kelamin == 1 ? 'L' : ($s->kelamin == 2 ? 'P' : '-') }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $s->siswaKelas->last()?->kelas?->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500 hidden lg:table-cell">{{ $s->siswaKelas->last()?->kelas?->kompetensiKeahlian?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" onclick="showDetail({{ $s->id }})" class="p-1.5 text-teal-primary hover:bg-teal-bg rounded-lg" title="Detail">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>
                                <button type="button" onclick="editSiswa({{ $s->id }})" class="p-1.5 text-sky hover:bg-sky/5 rounded-lg" title="Edit"><x-heroicon-o-pencil-square class="w-4 h-4" /></button>
                                <form method="POST" action="{{ route('tu.kesiswaan.destroy', $s) }}" class="inline" onsubmit="return confirm('Nonaktifkan siswa ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-coral hover:bg-coral/5 rounded-lg"><x-heroicon-o-x-circle class="w-4 h-4" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">Belum ada data siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $siswa->links() }}</div>
    </div>
</div>

{{-- Modal Detail Siswa --}}
<div id="modal-detail" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeDetail()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="relative bg-white rounded-card shadow-card w-full max-w-4xl max-h-[90vh] overflow-y-auto">

            {{-- Header --}}
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                <h3 class="text-lg font-extrabold text-teal-primary-dark">Detail Siswa</h3>
                <button onclick="closeDetail()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{-- Loading --}}
            <div id="detail-loading" class="px-6 py-16 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-teal-primary border-r-transparent"></div>
                <p class="mt-3 text-sm text-gray-500">Memuat data...</p>
            </div>

            {{-- Content --}}
            <div id="detail-content" class="px-6 py-5 space-y-5 hidden">

                {{-- Info Utama --}}
                <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                    <div class="w-16 h-16 rounded-full bg-teal-primary/10 flex items-center justify-center flex-shrink-0">
                        <span id="detail-avatar" class="text-2xl font-extrabold text-teal-primary"></span>
                    </div>
                    <div>
                        <h4 id="detail-nama" class="text-xl font-extrabold text-gray-800"></h4>
                        <p class="text-sm text-gray-500">NISN: <span id="detail-nisn" class="font-bold text-teal-primary"></span> &middot; NIS: <span id="detail-nis" class="font-bold"></span></p>
                    </div>
                    <div class="ml-auto">
                        <span id="detail-status" class="px-3 py-1 rounded-pill text-xs font-bold"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Data Pribadi --}}
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Pribadi</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">JK</span><span id="detail-jk" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Tempat Lahir</span><span id="detail-tplahir" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Tgl Lahir</span><span id="detail-tglahir" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Agama</span><span id="detail-agama" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Jurusan</span><span id="detail-jurusan" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Kontak</span><span id="detail-kontak" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">NIK PD</span><span id="detail-nikpd" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">NKK</span><span id="detail-nkk" class="font-medium"></span></div>
                        </div>
                    </div>

                    {{-- Alamat & Status --}}
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Alamat & Status</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Alamat</span><span id="detail-alamat" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Sekolah Asal</span><span id="detail-sekolah" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Terima Tgl</span><span id="detail-terima" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Terima Kelas</span><span id="detail-terimakelas" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Jenis</span><span id="detail-jenis" class="font-medium"></span></div>
                        </div>
                    </div>

                    {{-- Ayah --}}
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Ayah</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Nama</span><span id="detail-ayah" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">NIK</span><span id="detail-nikayah" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Pekerjaan</span><span id="detail-pkayah" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Kontak</span><span id="detail-ktayah" class="font-medium"></span></div>
                        </div>
                    </div>

                    {{-- Ibu --}}
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Ibu</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Nama</span><span id="detail-ibu" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">NIK</span><span id="detail-nikibu" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Pekerjaan</span><span id="detail-pkibu" class="font-medium"></span></div>
                            <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Kontak</span><span id="detail-ktibu" class="font-medium"></span></div>
                        </div>
                    </div>
                </div>

                {{-- Data Wali --}}
                <div id="detail-wali-section" class="hidden space-y-3">
                    <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Wali</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Nama</span><span id="detail-wali" class="font-medium"></span></div>
                        <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Pekerjaan</span><span id="detail-pkwali" class="font-medium"></span></div>
                        <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Kontak</span><span id="detail-ktwali" class="font-medium"></span></div>
                        <div class="flex"><span class="w-28 text-gray-500 flex-shrink-0">Alamat</span><span id="detail-alwali" class="font-medium"></span></div>
                    </div>
                </div>

                {{-- Riwayat Kelas --}}
                <div id="detail-riwayat-section" class="hidden space-y-3">
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
                            <tbody id="detail-riwayat-body" class="divide-y divide-gray-100">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Siswa --}}
<div id="modal-edit" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeEdit()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="relative bg-white rounded-card shadow-card w-full max-w-4xl max-h-[90vh] overflow-y-auto">

            {{-- Header --}}
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                <h3 class="text-lg font-extrabold text-teal-primary-dark">Edit Siswa</h3>
                <button onclick="closeEdit()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            {{-- Loading --}}
            <div id="edit-loading" class="px-6 py-16 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-teal-primary border-r-transparent"></div>
                <p class="mt-3 text-sm text-gray-500">Memuat data...</p>
            </div>

            {{-- Form --}}
            <form id="edit-form" method="POST" class="hidden" onsubmit="submitEdit(event)">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id" name="id">

                <div class="px-6 py-5 space-y-5">

                    {{-- Data Pribadi --}}
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Pribadi</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Lengkap *</label>
                                <input type="text" id="edit-nama_siswa" name="nama_siswa" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">NISN *</label>
                                <input type="text" id="edit-nisn" name="nisn" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">NIS *</label>
                                <input type="text" id="edit-nis" name="nis" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">NIK PD</label>
                                <input type="text" id="edit-nik_pd" name="nik_pd" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Tempat Lahir</label>
                                <input type="text" id="edit-tempat_lahir" name="tempat_lahir" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Lahir</label>
                                <input type="date" id="edit-tanggal_lahir" name="tanggal_lahir" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Jenis Kelamin</label>
                                <select id="edit-kelamin" name="kelamin" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                                    <option value="">-- Pilih --</option>
                                    <option value="1">Laki-laki</option>
                                    <option value="2">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Agama</label>
                                <select id="edit-agama" name="agama" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                                    <option value="">-- Pilih --</option>
                                    <option value="1">Islam</option>
                                    <option value="2">Kristen</option>
                                    <option value="3">Katolik</option>
                                    <option value="4">Hindu</option>
                                    <option value="5">Buddha</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Jurusan</label>
                                <select id="edit-jurusan" name="jurusan" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                                    <option value="">-- Pilih Jurusan --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Kontak</label>
                                <input type="text" id="edit-kontak_siswa" name="kontak_siswa" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 mb-1">Alamat</label>
                                <textarea id="edit-alamat" name="alamat" rows="2" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Data Ayah --}}
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Ayah</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Ayah</label>
                                <input type="text" id="edit-nama_ayah" name="nama_ayah" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">NIK Ayah</label>
                                <input type="text" id="edit-nik_ayah" name="nik_ayah" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Pekerjaan</label>
                                <input type="text" id="edit-pekerjaan_ayah" name="pekerjaan_ayah" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Kontak</label>
                                <input type="text" id="edit-kontak_ayah" name="kontak_ayah" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                        </div>
                    </div>

                    {{-- Data Ibu --}}
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Ibu</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Ibu</label>
                                <input type="text" id="edit-nama_ibu" name="nama_ibu" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">NIK Ibu</label>
                                <input type="text" id="edit-nik_ibu" name="nik_ibu" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Pekerjaan</label>
                                <input type="text" id="edit-pekerjaan_ibu" name="pekerjaan_ibu" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Kontak</label>
                                <input type="text" id="edit-kontak_ibu" name="kontak_ibu" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                        </div>
                    </div>

                    {{-- Data Wali --}}
                    <div class="space-y-3">
                        <h5 class="font-extrabold text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100 pb-1">Data Wali (Opsional)</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Wali</label>
                                <input type="text" id="edit-nama_wali" name="nama_wali" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Pekerjaan</label>
                                <input type="text" id="edit-pekerjaan_wali" name="pekerjaan_wali" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Kontak</label>
                                <input type="text" id="edit-kontak_wali" name="kontak_wali" class="w-full border-teal-primary/20 rounded-card text-sm py-1.5 px-3 focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeEdit()" class="px-4 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-card transition-colors">Batal</button>
                    <button type="submit" id="edit-submit-btn" class="px-4 py-2 text-sm font-bold text-white bg-teal-primary hover:bg-teal-primary-dark rounded-card transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal-detail');
    const loading = document.getElementById('detail-loading');
    const content = document.getElementById('detail-content');

    const agamaMap = {1:'Islam',2:'Kristen',3:'Katolik',4:'Hindu',5:'Buddha',6:'Konghucu'};

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
            const res = await fetch('{{ url("tu/kesiswaan") }}/' + id);
            if (!res.ok) throw new Error('Gagal memuat data');
            const d = await res.json();

            document.getElementById('detail-avatar').textContent = d.nama_siswa?.charAt(0) ?? '?';
            document.getElementById('detail-nama').textContent = d.nama_siswa ?? '-';
            document.getElementById('detail-nisn').textContent = d.nisn ?? '-';
            document.getElementById('detail-nis').textContent = d.nis ?? '-';

            const statusEl = document.getElementById('detail-status');
            if (d.aktif == 1) {
                statusEl.textContent = 'Aktif';
                statusEl.className = 'px-3 py-1 rounded-pill text-xs font-bold bg-teal-primary/10 text-teal-primary';
            } else {
                statusEl.textContent = 'Non-aktif';
                statusEl.className = 'px-3 py-1 rounded-pill text-xs font-bold bg-coral/10 text-coral';
            }

            setText('detail-jk', d.kelamin == 1 ? 'Laki-laki' : (d.kelamin == 2 ? 'Perempuan' : '-'));
            setText('detail-tplahir', d.tempat_lahir || '-');
            setText('detail-tglahir', d.tanggal_lahir ? new Date(d.tanggal_lahir).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : '-');
            setText('detail-agama', agamaMap[d.agama] || '-');

            const kelasAktif = d.siswa_kelas?.find(sk => sk.status === 'aktif') || d.siswa_kelas?.[0];
            setText('detail-jurusan', kelasAktif?.kelas?.kompetensi_keahlian?.nama || '-');

            setText('detail-kontak', d.kontak_siswa || '-');
            setText('detail-nikpd', d.nik_pd || '-');
            setText('detail-nkk', d.nkk || '-');

            setText('detail-alamat', d.alamat || '-');
            setText('detail-sekolah', d.sekolah_asal || '-');
            setText('detail-terima', d.terima_tanggal ? new Date(d.terima_tanggal).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : '-');
            setText('detail-terimakelas', d.terima_kelas || '-');
            setText('detail-jenis', d.jenis_siswa == 1 ? 'Reguler' : (d.jenis_siswa == 2 ? 'Pindahan' : '-'));

            setText('detail-ayah', d.nama_ayah || '-');
            setText('detail-nikayah', d.nik_ayah || '-');
            setText('detail-pkayah', d.pekerjaan_ayah || '-');
            setText('detail-ktayah', d.kontak_ayah || '-');

            setText('detail-ibu', d.nama_ibu || '-');
            setText('detail-nikibu', d.nik_ibu || '-');
            setText('detail-pkibu', d.pekerjaan_ibu || '-');
            setText('detail-ktibu', d.kontak_ibu || '-');

            const waliSection = document.getElementById('detail-wali-section');
            if (d.nama_wali) {
                waliSection.classList.remove('hidden');
                setText('detail-wali', d.nama_wali);
                setText('detail-pkwali', d.pekerjaan_wali || '-');
                setText('detail-ktwali', d.kontak_wali || '-');
                setText('detail-alwali', d.alamat_wali || '-');
            } else {
                waliSection.classList.add('hidden');
            }

            const riwayatSection = document.getElementById('detail-riwayat-section');
            const riwayatBody = document.getElementById('detail-riwayat-body');
            riwayatBody.innerHTML = '';

            if (d.siswa_kelas && d.siswa_kelas.length > 0) {
                riwayatSection.classList.remove('hidden');
                d.siswa_kelas.forEach(sk => {
                    riwayatBody.innerHTML += `<tr>
                        <td class="px-3 py-2 font-bold">${sk.kelas?.nama_kelas || '-'}</td>
                        <td class="px-3 py-2">${sk.kelas?.tingkat?.nama || '-'}</td>
                        <td class="px-3 py-2">${sk.kelas?.tahun_pelajaran?.tahun || '-'}</td>
                        <td class="px-3 py-2">${sk.semester?.nama || '-'}</td>
                        <td class="px-3 py-2"><span class="px-2 py-0.5 rounded-pill text-xs font-bold ${sk.status == 'aktif' ? 'bg-teal-primary/10 text-teal-primary' : 'bg-gray-100 text-gray-500'}">${sk.status}</span></td>
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

    function setText(id, value) {
        document.getElementById(id).textContent = value || '-';
    }

    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('bg-black/50')) {
            closeDetail();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeDetail();
        }
    });

    // ── Modal Edit ──
    const modalEdit = document.getElementById('modal-edit');
    const editLoading = document.getElementById('edit-loading');
    const editForm = document.getElementById('edit-form');
    let kompetensiList = [];

    function openEdit() {
        modalEdit.classList.remove('hidden');
        editLoading.classList.remove('hidden');
        editForm.classList.add('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEdit() {
        modalEdit.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        editForm.reset();
    }

    async function editSiswa(id) {
        openEdit();

        try {
            const res = await fetch('{{ url("tu/kesiswaan") }}/' + id + '/edit');
            if (!res.ok) throw new Error('Gagal memuat data');
            const data = await res.json();

            const s = data.siswa;
            kompetensiList = data.kompetensi || [];

            // Populate jurusan dropdown
            const jurusanSelect = document.getElementById('edit-jurusan');
            jurusanSelect.innerHTML = '<option value="">-- Pilih Jurusan --</option>';
            kompetensiList.forEach(k => {
                const opt = document.createElement('option');
                opt.value = k.id;
                opt.textContent = k.nama;
                if (s.jurusan == k.id) opt.selected = true;
                jurusanSelect.appendChild(opt);
            });

            // Populate form fields
            document.getElementById('edit-id').value = s.id;
            document.getElementById('edit-nama_siswa').value = s.nama_siswa || '';
            document.getElementById('edit-nisn').value = s.nisn || '';
            document.getElementById('edit-nis').value = s.nis || '';
            document.getElementById('edit-nik_pd').value = s.nik_pd || '';
            document.getElementById('edit-tempat_lahir').value = s.tempat_lahir || '';
            document.getElementById('edit-tanggal_lahir').value = s.tanggal_lahir ? s.tanggal_lahir.substring(0, 10) : '';
            document.getElementById('edit-kelamin').value = s.kelamin || '';
            document.getElementById('edit-agama').value = s.agama || '';
            document.getElementById('edit-kontak_siswa').value = s.kontak_siswa || '';
            document.getElementById('edit-alamat').value = s.alamat || '';

            document.getElementById('edit-nama_ayah').value = s.nama_ayah || '';
            document.getElementById('edit-nik_ayah').value = s.nik_ayah || '';
            document.getElementById('edit-pekerjaan_ayah').value = s.pekerjaan_ayah || '';
            document.getElementById('edit-kontak_ayah').value = s.kontak_ayah || '';

            document.getElementById('edit-nama_ibu').value = s.nama_ibu || '';
            document.getElementById('edit-nik_ibu').value = s.nik_ibu || '';
            document.getElementById('edit-pekerjaan_ibu').value = s.pekerjaan_ibu || '';
            document.getElementById('edit-kontak_ibu').value = s.kontak_ibu || '';

            document.getElementById('edit-nama_wali').value = s.nama_wali || '';
            document.getElementById('edit-pekerjaan_wali').value = s.pekerjaan_wali || '';
            document.getElementById('edit-kontak_wali').value = s.kontak_wali || '';

            // Set form action
            editForm.action = '{{ url("tu/kesiswaan") }}/' + s.id;

            editLoading.classList.add('hidden');
            editForm.classList.remove('hidden');

        } catch (err) {
            editLoading.innerHTML = '<p class="text-coral text-sm">Gagal memuat data siswa.</p>';
        }
    }

    async function submitEdit(e) {
        e.preventDefault();

        const btn = document.getElementById('edit-submit-btn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        try {
            const formData = new FormData(editForm);
            formData.append('_method', 'PUT');

            const res = await fetch(editForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (res.redirected) {
                window.location.href = res.url;
                return;
            }

            const data = await res.json();

            if (res.ok) {
                window.location.reload();
            } else {
                // Tampilkan error validasi
                let errors = '';
                if (data.errors) {
                    Object.values(data.errors).forEach(arr => {
                        errors += arr.join('\n') + '\n';
                    });
                }
                alert(errors || data.message || 'Gagal menyimpan data.');
            }
        } catch (err) {
            window.location.reload();
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }

    modalEdit.addEventListener('click', function(e) {
        if (e.target === modalEdit || e.target.classList.contains('bg-black/50')) {
            closeEdit();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modalEdit.classList.contains('hidden')) {
            closeEdit();
        }
    });
</script>
@endsection
