@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-cog-6-tooth class="w-7 h-7" />
            Pengaturan
        </h1>
        <p class="mt-1 text-sm text-gray-500">Atur tahun pelajaran, semester aktif, tanggal pembagian rapor, dan push notification.</p>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="flex gap-6" role="tablist">
            <button id="tab-general" onclick="switchTab('general')"
                    class="pb-3 text-sm font-semibold border-b-2 border-teal-primary text-teal-primary-dark"
                    role="tab" aria-selected="true">
                Umum
            </button>
            <button id="tab-push" onclick="switchTab('push')"
                    class="pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                    role="tab" aria-selected="false">
                Push Notification
            </button>
        </nav>
    </div>

    {{-- Tab: General --}}
    <div id="panel-general" role="tabpanel">
        <form method="POST" action="{{ route('tu.pengaturan.update') }}" class="space-y-6">
            @csrf @method('PUT')

            <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
                <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Tahun Pelajaran & Semester Aktif</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="tahun_pelajaran_id" value="Tahun Pelajaran Aktif" />
                        <select id="tahun_pelajaran_id" name="tahun_pelajaran_id" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            @foreach($tahunPelajarans as $tp)
                                <option value="{{ $tp->id }}" {{ old('tahun_pelajaran_id', $sekolah->tahun_aktif) == $tp->id ? 'selected' : '' }}>
                                    {{ $tp->tahun }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tahun_pelajaran_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="semester_id" value="Semester Aktif" />
                        <select id="semester_id" name="semester_id" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            @foreach($semesters as $s)
                                <option value="{{ $s->id }}" {{ old('semester_id', $sekolah->semester_aktif) == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('semester_id')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
                <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Pembagian Rapor</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="tanggal_mid" value="Tanggal Rapor Mid Semester" />
                        <x-text-input id="tanggal_mid" name="tanggal_mid" type="date" :value="old('tanggal_mid', optional($pembagian)->tanggal_mid?->format('Y-m-d'))" class="block w-full mt-1" />
                        <x-input-error :messages="$errors->get('tanggal_mid')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="tanggal_semester" value="Tanggal Rapor Semester" />
                        <x-text-input id="tanggal_semester" name="tanggal_semester" type="date" :value="old('tanggal_semester', optional($pembagian)->tanggal_semester?->format('Y-m-d'))" class="block w-full mt-1" />
                        <x-input-error :messages="$errors->get('tanggal_semester')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <x-primary-button>Simpan Pengaturan</x-primary-button>
            </div>
        </form>
    </div>

    {{-- Tab: Push Notification --}}
    <div id="panel-push" role="tabpanel" class="hidden">
        <form method="POST" action="{{ route('tu.pengaturan.push') }}" class="space-y-6">
            @csrf

            <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
                <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Kirim Push Notification</h2>

                @if(!config('push.vapid.public_key') || !config('push.vapid.private_key'))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                        VAPID keys belum dikonfigurasi. Tambahkan <code>VAPID_PUBLIC_KEY</code> dan <code>VAPID_PRIVATE_KEY</code> di file <code>.env</code>.
                    </div>
                @else
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="push_target" value="Target Penerima" />
                            <select id="push_target" name="push_target" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20"
                                    onchange="togglePushTarget(this.value)">
                                <option value="all">Semua Guru & Kepsek</option>
                                <option value="role">Berdasarkan Role</option>
                                <option value="user">Pilih User Tertentu</option>
                            </select>
                        </div>

                        <div id="push-role-section" class="hidden">
                            <x-input-label for="push_role" value="Role" />
                            <select id="push_role" name="push_role" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                                <option value="3">Guru</option>
                                <option value="4">Kepala Sekolah</option>
                            </select>
                        </div>

                        <div id="push-user-section" class="hidden">
                            <x-input-label for="push_user_ids" value="Pilih User" />
                            <select id="push_user_ids" name="push_user_ids[]" multiple
                                    class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20"
                                    size="5">
                                @foreach($guruUsers as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }} ({{ $guru->jabatan == 4 ? 'Kepsek' : 'Guru' }})</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Tahan Ctrl/Cmd untuk memilih banyak</p>
                        </div>

                        <div>
                            <x-input-label for="push_title" value="Judul Notifikasi" />
                            <x-text-input id="push_title" name="push_title" type="text" class="block w-full mt-1" placeholder="Contoh: Pengingat Input Nilai" />
                            <x-input-error :messages="$errors->get('push_title')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="push_body" value="Isi Pesan" />
                            <textarea id="push_body" name="push_body" rows="3"
                                      class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20"
                                      placeholder="Contoh: Segera input nilai untuk semester ini sebelum deadline."></textarea>
                            <x-input-error :messages="$errors->get('push_body')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="push_url" value="URL Tujuan (opsional)" />
                            <x-text-input id="push_url" name="push_url" type="text" class="block w-full mt-1" placeholder="/guru/penilaian" />
                            <p class="mt-1 text-xs text-gray-400">Klik notifikasi akan membuka halaman ini</p>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>Kirim Push Notification</x-primary-button>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    const generalTab = document.getElementById('tab-general');
    const pushTab = document.getElementById('tab-push');
    const generalPanel = document.getElementById('panel-general');
    const pushPanel = document.getElementById('panel-push');

    if (tab === 'general') {
        generalTab.classList.add('border-teal-primary', 'text-teal-primary-dark');
        generalTab.classList.remove('border-transparent', 'text-gray-500');
        pushTab.classList.remove('border-teal-primary', 'text-teal-primary-dark');
        pushTab.classList.add('border-transparent', 'text-gray-500');
        generalPanel.classList.remove('hidden');
        pushPanel.classList.add('hidden');
    } else {
        pushTab.classList.add('border-teal-primary', 'text-teal-primary-dark');
        pushTab.classList.remove('border-transparent', 'text-gray-500');
        generalTab.classList.remove('border-teal-primary', 'text-teal-primary-dark');
        generalTab.classList.add('border-transparent', 'text-gray-500');
        pushPanel.classList.remove('hidden');
        generalPanel.classList.add('hidden');
    }
}

function togglePushTarget(value) {
    const roleSection = document.getElementById('push-role-section');
    const userSection = document.getElementById('push-user-section');

    roleSection.classList.toggle('hidden', value !== 'role');
    userSection.classList.toggle('hidden', value !== 'user');
}
</script>
@endsection
