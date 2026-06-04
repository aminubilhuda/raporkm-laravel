@extends('layouts.tu')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
                <x-heroicon-o-book-open class="w-7 h-7" /> Mata Pelajaran
            </h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data mata pelajaran. Edit langsung di tabel.</p>
        </div>
        <button type="button" onclick="openModal()" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
            <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambah Mapel
        </button>
    </div>

    @if($mapel->isNotEmpty())
    <div class="bg-white rounded-card shadow-card overflow-hidden border-l-[6px] border-l-teal-primary">
        <form method="POST" action="{{ route('tu.mapel.batch-update') }}">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-surface-base text-left">
                        <tr>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-10">No</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Kode</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Nama Mapel</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Kelompok</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">KKM</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-20">Urutan</th>
                            <th class="px-3 py-3 font-extrabold text-gray-500 text-xs uppercase tracking-wider w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($mapel as $i => $m)
                        <tr class="hover:bg-teal-bg/30">
                            <td class="px-3 py-2 text-gray-500 text-center">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2"><span class="px-2 py-0.5 text-xs font-bold rounded-pill bg-teal-primary/10 text-teal-primary">{{ $m->kode ?? '-' }}</span></td>
                            <td class="px-3 py-2 font-bold text-gray-800">{{ $m->nama_mapel }}</td>
                            <td class="px-3 py-2">
                                <input type="hidden" name="items[{{ $i }}][id]" value="{{ $m->id }}">
                                <select name="items[{{ $i }}][kelompok_mapel_id]" class="block w-full border-teal-primary/20 bg-white rounded-card shadow-sm focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 transition-colors text-sm py-1.5">
                                    @foreach($kelompok as $k)
                                        <option value="{{ $k->id }}" {{ $m->kelompok_mapel_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" name="items[{{ $i }}][kkm]" value="{{ $m->kkm }}" min="0" max="100" class="block w-20 border-teal-primary/20 bg-white rounded-card shadow-sm focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 transition-colors text-sm py-1.5 text-center">
                            </td>
                            <td class="px-3 py-2">
                                <input type="number" name="items[{{ $i }}][urutan]" value="{{ $m->urutan }}" min="0" class="block w-20 border-teal-primary/20 bg-white rounded-card shadow-sm focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20 transition-colors text-sm py-1.5 text-center">
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" onclick="deleteMapel({{ $m->id }}, '{{ addslashes($m->nama_mapel) }}')" class="p-1.5 text-coral hover:bg-coral/5 rounded-lg" title="Hapus">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-end gap-3">
                <span class="text-sm text-gray-500">{{ $mapel->total() }} mapel</span>
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5" /> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <div class="px-4 py-3">{{ $mapel->links() }}</div>

    <script>
        async function deleteMapel(id, name) {
            if (!confirm('Hapus ' + name + '?')) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("tu/mapel") }}/' + id;

            const token = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = '<input type="hidden" name="_token" value="' + token + '"><input type="hidden" name="_method" value="DELETE">';

            document.body.appendChild(form);
            form.submit();
        }
    </script>
    @else
    <div class="bg-white rounded-card shadow-card p-12 text-center border-l-[6px] border-l-teal-primary">
        <x-heroicon-o-book-open class="w-16 h-16 text-gray-300 mx-auto mb-4" />
        <p class="text-gray-400 mb-4">Belum ada data mata pelajaran.</p>
        <button type="button" onclick="openModal()" class="btn-primary inline-flex items-center gap-2">
            <x-heroicon-o-plus-circle class="w-5 h-5" /> Tambah Mapel Pertama
        </button>
    </div>
    @endif

    {{-- Modal Tambah Mapel --}}
    <div id="modal-tambah" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="relative bg-white rounded-card shadow-card w-full sm:max-w-2xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-extrabold text-teal-primary-dark">Tambah Mata Pelajaran</h3>
                    <button onclick="closeModal()" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <form method="POST" action="{{ route('tu.mapel.store') }}">
                    @csrf
                    <div class="px-6 py-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="modal_nama_mapel" value="Nama Mata Pelajaran" />
                                <x-text-input id="modal_nama_mapel" name="nama_mapel" :value="old('nama_mapel')" class="block w-full mt-1" required />
                                <x-input-error :messages="$errors->get('nama_mapel')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="modal_kode" value="Kode" />
                                <x-text-input id="modal_kode" name="kode" :value="old('kode')" placeholder="MTK" class="block w-full mt-1" />
                                <x-input-error :messages="$errors->get('kode')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="modal_kelompok" value="Kelompok Mapel" />
                                <select id="modal_kelompok" name="kelompok_mapel_id" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach($kelompok as $k)
                                        <option value="{{ $k->id }}" {{ old('kelompok_mapel_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('kelompok_mapel_id')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="modal_kkm" value="KKM" />
                                <x-text-input id="modal_kkm" name="kkm" type="number" :value="old('kkm', 75)" min="0" max="100" class="block w-full mt-1" required />
                                <x-input-error :messages="$errors->get('kkm')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="modal_urutan" value="Urutan" />
                                <x-text-input id="modal_urutan" name="urutan" type="number" :value="old('urutan')" min="0" placeholder="1" class="block w-full mt-1" />
                                <x-input-error :messages="$errors->get('urutan')" class="mt-1" />
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal()" class="btn-secondary inline-flex items-center gap-2">Batal</button>
                        <x-primary-button>Tambah Mapel</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('modal-tambah');

    function openModal() {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('bg-black/50')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    @if($errors->any())
        openModal();
    @endif
</script>
@endsection
