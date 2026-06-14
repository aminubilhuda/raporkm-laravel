@extends('layouts.guru')

@section('content')
<div class="space-y-6" x-data="{
    selectedTP: null,
    bulkTpId: null,
    bulkKode: '',
    bulkSection: 'formatif',
    bulkForm: null,
    bulkValues: '',
    bulkModal: false,
    clearTpId: null,
    clearKode: '',
    clearSection: 'formatif',
    clearForm: null,
    clearModal: false,
    fillColumn(tpId, kode, section, formEl) {
        this.bulkTpId = tpId;
        this.bulkKode = kode;
        this.bulkSection = section;
        this.bulkForm = formEl;
        this.bulkValues = '';
        this.bulkModal = true;
    },
    applyBulkFill() {
        const lines = this.bulkValues.trim().split(/\r?\n/).filter(v => v.trim() !== '');
        let inputs;
        if (this.bulkSection === 'formatif' || this.bulkSection === 'ph') {
            inputs = this.bulkForm.querySelectorAll(`input[name^='nilai[${this.bulkTpId}][']`);
        } else {
            inputs = this.bulkForm.querySelectorAll(`input[name^='nilai[']`);
        }
        lines.forEach((val, i) => {
            if (inputs[i]) {
                const num = parseInt(val.trim());
                if (!isNaN(num) && num >= 0 && num <= 100) {
                    inputs[i].value = num;
                    inputs[i].classList.add('ring-2', 'ring-coral/40');
                    setTimeout(() => inputs[i].classList.remove('ring-2', 'ring-coral/40'), 1500);
                }
            }
        });
        this.bulkModal = false;
    },
    clearColumn(tpId, kode, section, formEl) {
        this.clearTpId = tpId;
        this.clearKode = kode;
        this.clearSection = section;
        this.clearForm = formEl;
        this.clearModal = true;
    },
    applyClear() {
        let inputs;
        if (this.clearSection === 'formatif' || this.clearSection === 'ph') {
            inputs = this.clearForm.querySelectorAll(`input[name^='nilai[${this.clearTpId}][']`);
        } else {
            inputs = this.clearForm.querySelectorAll(`input[name^='nilai[']`);
        }
        inputs.forEach(input => {
            input.value = '';
            input.classList.add('ring-2', 'ring-red-400/40');
            setTimeout(() => input.classList.remove('ring-2', 'ring-red-400/40'), 1000);
        });
        this.clearModal = false;
    }
}">
    @if(session('status'))
        <div class="bg-success/10 border border-success/30 text-success px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
            <x-heroicon-o-check-circle class="w-5 h-5" />
            {{ session('status') }}
        </div>
    @endif

    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-pencil-square class="w-7 h-7" />
            Penilaian
        </h1>
        <p class="mt-1 text-sm text-gray-500">Input nilai formatif, sumatif PH, sumatif TS, dan sumatif AS.</p>
    </div>

    {{-- Picker --}}
    @if($mapelKelasList->isNotEmpty())
    <div class="bg-white rounded-card shadow-card p-4">
        <label class="block text-sm font-bold text-gray-500 mb-2">Pilih Kelas & Mapel</label>
        <div class="flex flex-wrap gap-2">
            @foreach($mapelKelasList as $mk)
                <a href="{{ route('guru.penilaian.index', ['kelas' => $mk->kelas_id, 'mapel' => $mk->mapel_id]) }}"
                    class="px-4 py-2 text-sm font-bold rounded-pill transition-all {{ $authorized && $kelas->id === $mk->kelas_id && $mapel->id === $mk->mapel_id ? 'bg-coral text-white shadow-coral-glow' : 'bg-surface-base text-gray-600 hover:bg-coral/10 hover:text-coral' }}">
                    {{ $mk->kelas->nama_kelas }} · {{ $mk->mapel->nama_mapel }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($authorized && $tujuanPembelajaran->isNotEmpty() && $siswa->isNotEmpty())
        {{-- Formatif --}}
        <div id="formatif" class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 text-coral" />
                    Nilai Formatif
                </h3>
                <span class="text-xs text-gray-400">Bobot 40%</span>
            </div>
            <div class="overflow-x-auto">
                <form method="POST" action="{{ route('guru.penilaian.formatif') }}">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                    <input type="hidden" name="_section" value="formatif">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-base text-left">
                            <tr>
                                <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider sticky left-0 bg-surface-base min-w-[160px]">Siswa</th>
                                @foreach($tujuanPembelajaran as $tp)
                                <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[100px]">
                                    <div class="flex flex-col items-center gap-1">
                                        <span title="{{ $tp->kode_tp }} — {{ $tp->nama_tp }}">F.{{ $loop->iteration }}</span>
                                        <div class="flex items-center rounded overflow-hidden">
                                            {{-- Detail Icon --}}
                                            <button type="button"
                                                data-kode="{{ $tp->kode_tp }}"
                                                data-nama="{{ $tp->nama_tp }}"
                                                @click="selectedTP = { kode: $el.dataset.kode, nama: $el.dataset.nama }; $dispatch('open-modal', 'detail-tp')"
                                                class="p-1 bg-gray-500 hover:bg-gray-600 text-white transition-colors" title="Detail TP">
                                                <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                            </button>
                                            {{-- Bulk Fill Icon --}}
                                            <button type="button" @click="fillColumn({{ $tp->id }}, '{{ $loop->iteration }}', 'formatif', $el.closest('form'))"
                                                class="p-1 bg-green-600 hover:bg-green-700 text-white transition-colors" title="Isi cepat kolom ini">
                                                <x-heroicon-o-table-cells class="w-3.5 h-3.5" />
                                            </button>
                                            {{-- Clear Column Icon --}}
                                            <button type="button" @click="clearColumn({{ $tp->id }}, '{{ $loop->iteration }}', 'formatif', $el.closest('form'))"
                                                class="p-1 bg-red-500 hover:bg-red-600 text-white transition-colors" title="Kosongkan kolom ini">
                                                <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                        <div class="text-[10px] font-normal text-gray-400">Nilai</div>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswa as $sk)
                            <tr class="hover:bg-coral/5 transition-colors">
                                <td class="px-3 py-2 sticky left-0 bg-white font-bold text-gray-700 text-xs">
                                    <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                    {{ $sk->siswa->nama_siswa ?? '-' }}
                                </td>
                                @foreach($tujuanPembelajaran as $tp)
                                @php $key = "{$sk->siswa_id}_{$tp->id}"; $nf = $nilaiFormatif->get($key); @endphp
                                <td class="px-1 py-2">
                                    <input type="number" name="nilai[{{ $tp->id }}][{{ $sk->siswa_id }}]" value="{{ $nf->nilai ?? '' }}" min="0" max="100"
                                        class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-coral/30 focus:border-coral outline-none mx-auto block">
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-4 py-3 border-t border-gray-100 text-right">
                        <button type="submit" class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                            Simpan Formatif
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sumatif PH --}}
        <div id="sumatif-ph" class="bg-white rounded-card shadow-card overflow-hidden">
            <div class="px-4 py-3 bg-surface-base border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5 text-sky" />
                    Nilai Sumatif PH
                </h3>
                <span class="text-xs text-gray-400">Bobot 30%</span>
            </div>
            <div class="overflow-x-auto">
                <form method="POST" action="{{ route('guru.penilaian.sumatif-ph') }}">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                    <input type="hidden" name="_section" value="sumatif-ph">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-base text-left">
                            <tr>
                                <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider sticky left-0 bg-surface-base min-w-[160px]">Siswa</th>
                                @foreach($tujuanPembelajaran as $tp)
                                <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider min-w-[120px]">
                                    <div class="flex flex-col items-center gap-1">
                                        <span title="{{ $tp->kode_tp }} — {{ $tp->nama_tp }}">S.{{ $loop->iteration }}</span>
                                        <div class="flex items-center rounded overflow-hidden">
                                            <button type="button"
                                                data-kode="{{ $tp->kode_tp }}"
                                                data-nama="{{ $tp->nama_tp }}"
                                                @click="selectedTP = { kode: $el.dataset.kode, nama: $el.dataset.nama }; $dispatch('open-modal', 'detail-tp')"
                                                class="p-1 bg-gray-500 hover:bg-gray-600 text-white transition-colors" title="Detail TP">
                                                <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                            </button>
                                            <button type="button" @click="fillColumn({{ $tp->id }}, '{{ $loop->iteration }}', 'ph', $el.closest('form'))"
                                                class="p-1 bg-green-600 hover:bg-green-700 text-white transition-colors" title="Isi cepat kolom ini">
                                                <x-heroicon-o-table-cells class="w-3.5 h-3.5" />
                                            </button>
                                            <button type="button" @click="clearColumn({{ $tp->id }}, '{{ $loop->iteration }}', 'ph', $el.closest('form'))"
                                                class="p-1 bg-red-500 hover:bg-red-600 text-white transition-colors" title="Kosongkan kolom ini">
                                                <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                        <div class="text-[10px] font-normal text-gray-400">Nilai</div>
                                    </div>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswa as $sk)
                            <tr class="hover:bg-coral/5 transition-colors">
                                <td class="px-3 py-2 sticky left-0 bg-white font-bold text-gray-700 text-xs">
                                    <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                    {{ $sk->siswa->nama_siswa ?? '-' }}
                                </td>
                                @foreach($tujuanPembelajaran as $tp)
                                @php $key = "{$sk->siswa_id}_{$tp->id}"; $nph = $nilaiSumatifPh->get($key); @endphp
                                <td class="px-1 py-2">
                                    <input type="number" name="nilai[{{ $tp->id }}][{{ $sk->siswa_id }}]" value="{{ $nph->nilai ?? '' }}" min="0" max="100"
                                        class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-sky/30 focus:border-sky outline-none mx-auto block">
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-4 py-3 border-t border-gray-100 text-right">
                        <button type="submit" class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                            Simpan Sumatif PH
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sumatif TS & AS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div id="sumatif-ts" class="bg-white rounded-card shadow-card overflow-hidden">
                <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                        <x-heroicon-o-document-arrow-up class="w-5 h-5 text-gold-dark" />
                        Nilai Sumatif TS
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <form method="POST" action="{{ route('guru.penilaian.sumatif-ts') }}">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                        <input type="hidden" name="_section" value="sumatif-ts">
                        <table class="w-full text-sm">
                            <thead class="bg-surface-base text-left">
                                <tr>
                                    <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th>
                                    <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-24">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>Nilai</span>
                                            <div class="flex items-center rounded overflow-hidden">
                                                <button type="button" @click="fillColumn(null, 'TS', 'ts', $el.closest('form'))"
                                                    class="p-1 bg-green-600 hover:bg-green-700 text-white transition-colors" title="Isi cepat kolom ini">
                                                    <x-heroicon-o-table-cells class="w-3.5 h-3.5" />
                                                </button>
                                                <button type="button" @click="clearColumn(null, 'TS', 'ts', $el.closest('form'))"
                                                    class="p-1 bg-red-500 hover:bg-red-600 text-white transition-colors" title="Kosongkan kolom ini">
                                                    <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($siswa as $sk)
                                @php $nts = $nilaiSumatifTs->get("{$sk->siswa_id}"); @endphp
                                <tr class="hover:bg-coral/5 transition-colors">
                                    <td class="px-3 py-2 font-bold text-gray-700 text-xs">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                                    <td class="px-2 py-2">
                                        <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                        <input type="number" name="nilai[{{ $sk->siswa_id }}]" value="{{ $nts->nilai ?? '' }}" min="0" max="100"
                                            class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-gold/30 focus:border-gold outline-none">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 py-3 border-t border-gray-100 text-right">
                            <button type="submit" class="bg-coral text-white px-4 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                                Simpan TS
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="sumatif-as" class="bg-white rounded-card shadow-card overflow-hidden">
                <div class="px-4 py-3 bg-surface-base border-b border-gray-100">
                    <h3 class="font-extrabold text-gray-700 flex items-center gap-2">
                        <x-heroicon-o-document-arrow-down class="w-5 h-5 text-teal-primary" />
                        Nilai Sumatif AS
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <form method="POST" action="{{ route('guru.penilaian.sumatif-as') }}">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">
                        <input type="hidden" name="_section" value="sumatif-as">
                        <table class="w-full text-sm">
                            <thead class="bg-surface-base text-left">
                                <tr>
                                    <th class="px-3 py-2 font-extrabold text-gray-500 text-xs uppercase tracking-wider">Siswa</th>
                                    <th class="px-2 py-2 text-center font-extrabold text-gray-500 text-xs uppercase tracking-wider w-24">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>Nilai</span>
                                            <div class="flex items-center rounded overflow-hidden">
                                                <button type="button" @click="fillColumn(null, 'AS', 'as', $el.closest('form'))"
                                                    class="p-1 bg-green-600 hover:bg-green-700 text-white transition-colors" title="Isi cepat kolom ini">
                                                    <x-heroicon-o-table-cells class="w-3.5 h-3.5" />
                                                </button>
                                                <button type="button" @click="clearColumn(null, 'AS', 'as', $el.closest('form'))"
                                                    class="p-1 bg-red-500 hover:bg-red-600 text-white transition-colors" title="Kosongkan kolom ini">
                                                    <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($siswa as $sk)
                                @php $nas = $nilaiSumatifAs->get("{$sk->siswa_id}"); @endphp
                                <tr class="hover:bg-coral/5 transition-colors">
                                    <td class="px-3 py-2 font-bold text-gray-700 text-xs">{{ $sk->siswa->nama_siswa ?? '-' }}</td>
                                    <td class="px-2 py-2">
                                        <input type="hidden" name="siswa_id[]" value="{{ $sk->siswa_id }}">
                                        <input type="number" name="nilai[{{ $sk->siswa_id }}]" value="{{ $nas->nilai ?? '' }}" min="0" max="100"
                                            class="w-16 px-1 py-1 text-center border border-gray-200 rounded text-xs focus:ring-1 focus:ring-teal/30 focus:border-teal outline-none">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-4 py-3 border-t border-gray-100 text-right">
                            <button type="submit" class="bg-coral text-white px-4 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                                Simpan AS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($authorized && $tujuanPembelajaran->isEmpty())
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <x-heroicon-o-check-badge class="w-12 h-12 text-gray-300 mb-3" />
            <p class="text-gray-400">Belum ada Tujuan Pembelajaran untuk kelas & mapel ini.</p>
            <a href="{{ route('guru.tujuan-pembelajaran.index', ['kelas' => $kelas?->id, 'mapel' => $mapel?->id]) }}" class="mt-3 text-coral font-bold text-sm hover:underline">
                Buat TP dulu →
            </a>
        </div>
    @elseif($authorized && $siswa->isEmpty())
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <x-heroicon-o-users class="w-12 h-12 text-gray-300 mb-3" />
            <p class="text-gray-400">Belum ada siswa di kelas ini.</p>
        </div>
    @elseif($mapelKelasList->isNotEmpty())
        <div class="flex flex-col items-center justify-center min-h-[30vh] text-center">
            <x-heroicon-o-cursor-arrow-rays class="w-12 h-12 text-gray-300 mb-3" />
            <p class="text-gray-400">Pilih kelas & mapel terlebih dahulu.</p>
        </div>
    @else
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <x-heroicon-o-pencil-square class="w-16 h-16 text-gray-300 mb-4" />
            <h2 class="text-xl font-extrabold text-gray-400 mb-2">Belum Ada Mapel</h2>
            <p class="text-gray-400">Anda belum terdaftar sebagai pengajar mapel apapun.</p>
        </div>
    @endif

    {{-- Modal Detail TP --}}
    <x-modal name="detail-tp" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-document-text class="w-5 h-5 text-coral" />
                <h3 class="text-lg font-bold text-gray-700">Tujuan Pembelajaran</h3>
            </div>
            <div class="space-y-3 mb-6">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase">Kode</span>
                    <p class="text-sm text-gray-700 font-semibold" x-text="selectedTP?.kode"></p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase">Deskripsi</span>
                    <p class="text-sm text-gray-700" x-text="selectedTP?.nama"></p>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" @click="$dispatch('close-modal', 'detail-tp')"
                    class="bg-coral text-white px-5 py-2 rounded-card font-bold text-sm hover:bg-coral-dark transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </x-modal>

    {{-- Modal Bulk Fill --}}
    <div x-show="bulkModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="fixed inset-0 bg-black/50" @click="bulkModal = false" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6 z-10" x-show="bulkModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-start gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-bg flex items-center justify-center">
                    <x-heroicon-o-table-cells class="w-5 h-5 text-teal-primary" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-extrabold text-gray-800">Isi Cepat Kolom <span x-text="bulkSection === 'formatif' ? 'F.' + bulkKode : bulkSection === 'ph' ? 'S.' + bulkKode : bulkKode"></span></h3>
                    <p class="mt-1 text-xs text-gray-500">Masukkan satu nilai per baris. Baris pertama = siswa teratas.</p>
                </div>
            </div>
            <textarea x-model="bulkValues" rows="8" placeholder="80&#10;89&#10;88&#10;..."
                class="w-full px-3 py-2 border border-gray-200 rounded-card text-sm font-mono focus:ring-2 focus:ring-teal-primary/30 focus:border-teal-primary outline-none resize-none"></textarea>
            <div class="mt-4 flex justify-end gap-3">
                <button type="button" @click="bulkModal = false" class="px-4 py-2 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="button" @click="applyBulkFill()" class="px-4 py-2 text-sm font-bold text-white bg-teal-primary rounded-lg hover:bg-teal-dark transition-colors">
                    Isi Nilai
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Clear Column --}}
    <div x-show="clearModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="fixed inset-0 bg-black/50" @click="clearModal = false" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 z-10" x-show="clearModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-start gap-3 mb-4">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-extrabold text-gray-800">Kosongkan Kolom <span x-text="clearSection === 'formatif' ? 'F.' + clearKode : clearSection === 'ph' ? 'S.' + clearKode : clearKode"></span>?</h3>
                    <p class="mt-1 text-sm text-gray-600">Semua nilai di kolom ini akan dihapus. Perubahan akan disimpan saat Anda klik tombol <strong>Simpan</strong>.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="clearModal = false" class="px-4 py-2 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="button" @click="applyClear()" class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Ya, Kosongkan
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('scroll_to'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const target = document.getElementById('{{ session("scroll_to") }}');
        if (target) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        }
    });
</script>
@endif
@endsection
