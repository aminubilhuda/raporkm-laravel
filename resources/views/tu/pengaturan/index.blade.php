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
            <button id="tab-database" onclick="switchTab('database')"
                    class="pb-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                    role="tab" aria-selected="false">
                Database
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

            <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
                <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Pengaturan Cetak Rapor</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="format_rapor" value="Format Kertas Default" />
                        <select id="format_rapor" name="format_rapor" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">
                            <option value="a4" {{ old('format_rapor', $sekolah->format_rapor ?? 'a4') == 'a4' ? 'selected' : '' }}>A4 (210 × 297 mm)</option>
                            <option value="f4" {{ old('format_rapor', $sekolah->format_rapor ?? 'a4') == 'f4' ? 'selected' : '' }}>F4 / Folio (210 × 330 mm)</option>
                        </select>
                        <x-input-error :messages="$errors->get('format_rapor')" class="mt-1" />
                        <p class="mt-1 text-xs text-gray-400">Ukuran kertas default untuk cetak identitas siswa & rapor.</p>
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

    {{-- Tab: Database --}}
    <div id="panel-database" role="tabpanel" class="hidden">
        <div class="space-y-6">
            {{-- Backup --}}
            <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
                <h2 class="text-lg font-extrabold text-teal-primary-dark mb-1">Backup Database</h2>
                <p class="text-xs text-gray-400 mb-4">Download seluruh database sebagai file SQL.</p>
                <form method="POST" action="{{ route('tu.pengaturan.backup') }}">
                    @csrf
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-primary text-white text-sm font-bold rounded-pill shadow-teal-glow hover:bg-teal-primary-dark hover:shadow-teal-glow-strong transition-all duration-200">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            Download Backup
                        </button>
                        <p class="text-xs text-gray-400">File .sql akan langsung diunduh ke komputer Anda.</p>
                    </div>
                </form>
            </div>

            {{-- Restore --}}
            <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
                <h2 class="text-lg font-extrabold text-coral mb-1">Restore Database</h2>
                <p class="text-xs text-gray-400 mb-4">Restore database dari file backup SQL. <strong class="text-coral">Tindakan ini akan mengganti seluruh data database.</strong></p>

                <div class="bg-coral/5 border border-coral/20 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-coral flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-coral">
                            <p class="font-bold">Peringatan!</p>
                            <ul class="list-disc ml-4 mt-1 text-xs">
                                <li>Semua data saat ini akan diganti oleh data dari file backup.</li>
                                <li>Proses ini tidak dapat dibatalkan.</li>
                                <li>Disarankan backup database terlebih dahulu sebelum restore.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('tu.pengaturan.restore') }}" enctype="multipart/form-data"
                      onsubmit="return confirm('Apakah Anda yakin ingin merestore database? Semua data akan diganti.')">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="backup_file" value="File Backup (.sql)" />
                            <input type="file" id="backup_file" name="backup_file" accept=".sql,.txt"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-coral/10 file:text-coral hover:file:bg-coral/20">
                            <x-input-error :messages="$errors->get('backup_file')" class="mt-1" />
                            <p class="text-xs text-gray-400 mt-1">Maks 50MB. Hanya menerima file .sql atau .txt.</p>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-coral cursor-pointer">
                            <input type="checkbox" name="confirm_restore" value="1" required
                                   class="rounded border-coral text-coral focus:ring-coral">
                            Saya mengerti bahwa semua data akan diganti dan tidak dapat dibatalkan
                        </label>
                        <x-input-error :messages="$errors->get('confirm_restore')" class="mt-1" />

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-coral text-white text-sm font-bold rounded-pill shadow-coral-glow hover:bg-coral/90 hover:shadow-coral-glow-strong transition-all duration-200">
                                <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                                Restore Database
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    const tabs = ['general', 'push', 'database'];
    tabs.forEach(function(t) {
        const tabBtn = document.getElementById('tab-' + t);
        const panel = document.getElementById('panel-' + t);
        if (t === tab) {
            tabBtn.classList.add('border-teal-primary', 'text-teal-primary-dark');
            tabBtn.classList.remove('border-transparent', 'text-gray-500');
            panel.classList.remove('hidden');
        } else {
            tabBtn.classList.remove('border-teal-primary', 'text-teal-primary-dark');
            tabBtn.classList.add('border-transparent', 'text-gray-500');
            panel.classList.add('hidden');
        }
    });
}

function togglePushTarget(value) {
    const roleSection = document.getElementById('push-role-section');
    const userSection = document.getElementById('push-user-section');

    roleSection.classList.toggle('hidden', value !== 'role');
    userSection.classList.toggle('hidden', value !== 'user');
}
</script>
@endsection
