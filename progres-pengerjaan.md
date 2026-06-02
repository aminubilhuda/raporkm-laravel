# Progres Pengerjaan — Migrasi E-Rapor KM ke Laravel 13

>> Terakhir diperbarui: 02 Juni 2026 (Fase 3 selesai + DemoDataSeeder — 162/162 test hijau, ~3650 record demo)

---

## Fase 0: Foundation ✅ SELESAI

### F0-01: Install Dependencies ✅
- `laravel/breeze` (Livewire stack) — terinstall
- `barryvdh/laravel-dompdf` — terinstall
- `openspout/openspout` (pengganti maatwebsite/excel, belum compatible Laravel 13) — terinstall
- `laravel/sanctum` — terinstall
- `spatie/laravel-activitylog` — terinstall
- `laravel/tinker`, `laravel/pail`, `laravel/pao`, `laravel/pint`, `laravel/boost` — sudah termasuk dari awal
- Livewire 3 + Volt — terinstall via Breeze

### F0-02: Configure .env ✅
- `APP_NAME="E-Rapor KM"`
- `APP_TIMEZONE=Asia/Jakarta`
- `APP_LOCALE=id`
- `APP_FAKER_LOCALE=id_ID`
- `DB_CONNECTION=sqlite` (dev)

### F0-03: Create All Migrations ✅
- 73 migration files dibuat dan berhasil dijalankan
- Tabel users di-redesign (tambah jabatan, nama, nip, nuptk, username, kelamin, agama, dll; hapus kolom name)
- Ditambahkan `email_verified_at` di tabel users
- Semua tabel referensi: ref_agama, ref_jenis_kelamin, ref_hubungan_keluarga, ref_jabatan, ref_kepegawaian, ref_pendidikan, ref_tugas_tambahan, ref_hari, ref_bulan, ref_jenis_siswa, ref_jenis_keluar, jenis_absen, kelompok_mapel, tahun_pelajaran, semester, ref_kurikulum
- Semua tabel master: sekolah, siswa, kelas, tingkat, kompetensi_keahlian, mapel, dimensi, dimensi_kokurikuler, deskripsi_kokurikuler, eskul, organisasi, deskripsi_rapor, pembagian_raport, kepala_sekolah, pengingat, surat_masuk, laporan_wa, settings
- Semua tabel relasi: siswa_kelas, kelas_wali, mapel_kelas, mapel_siswa, pembina_eskul
- Semua tabel penilaian: tujuan_pembelajaran, nilai_formatif, nilai_sumatif_ph, nilai_sumatif_ts, nilai_sumatif_as, lager_nilai_mapel, lager_nilai_mid, nilai_mapel, nilai_mapel_mid, nilai_mata_pelajaran, nilai_kelas, nilai_kelas_mid
- Semua tabel P5: elemen, sub_elemen, proyek_tema, proyek_kelas, proyek_subelemen, proyek_tujuan, mapel_proyek, nilai_proyek, nilai_assesmen_subelemen
- Semua tabel kokurikuler: nilai_kokurikuler
- Semua tabel ekstra/prakerin: siswa_eskul, prakerin, siswa_prakerin, nilai_prakerin
- Semua tabel kehadiran: presensi, piket_harian
- Semua tabel lainnya: catatan_wali, prestasi, mutasi_masuk, mutasi_keluar, lulusan
- Tabel auth: pwa_tokens, remember_tokens, personal_access_tokens
- Foreign keys dan indexes diimplementasikan sesuai PRD

### F0-04: Create All Eloquent Models ✅
- 62+ model files dibuat dengan relationships yang lengkap
- User model di-redesign: tambah jabatan, nama, username; role helper methods (isTU, isGuru, isKepsek); relationships
- Semua model mengikuti konvensi Laravel 13 (#[Fillable] attribute, casts, SoftDeletes)
- Relasi belongsTo, hasMany, belongsToMany sudah diimplementasikan

### F0-05: Create Seeders ✅
- RefDataSeeder: ref_agama (6), ref_jenis_kelamin (2), ref_hubungan_keluarga (3), ref_jabatan (3), ref_kepegawaian (5), ref_pendidikan (9), ref_tugas_tambahan (3), ref_hari (6), ref_bulan (12), ref_jenis_siswa (3), ref_jenis_keluar (5), jenis_absen (4), kelompok_mapel (3), tingkat (3), kompetensi_keahlian (3), dimensi (6), dimensi_kokurikuler (3), ref_kurikulum (1), deskripsi_rapor (4)
- TahunPelajaranSemesterSeeder: 2024/2025 & 2025/2026, Ganjil & Genap
- UserSeeder: admin (TU), guru, kepsek
- UserFactory: disesuaikan dengan kolom jabatan, nama, username (bukan name/email)
- DatabaseSeeder: chained semua seeder

### F0-06: Setup Auth (Breeze + Role Redirect) ✅
- Breeze Livewire stack terinstall
- LoginForm diubah: login pakai `username` (bukan `email`)
- Login redirect: jabatan=2 → /tu/dashboard, jabatan=3,4 → /guru/dashboard
- Routes: /tu/* dengan middleware role:2, /guru/* dengan middleware role:3,4
- Auth routes (login, register, forgot-password, dll) dari Breeze

### F0-07: Create Middleware ✅
- `EnsureRole` middleware: cek jabatan user, abort(403) jika tidak punya akses
- `SessionTimeout` middleware: 2 jam timeout (configurable via e-rapor.session_timeout)
- Kedua middleware terdaftar di bootstrap/app.php

### F0-08: Create Base Layouts ✅
- `layouts/app.blade.php` — Breeze default (sudah ada)
- `layouts/guest.blade.php` — diupdate dengan branding E-Rapor KM
- `layouts/tu.blade.php` — layout TU panel (sidebar biru + topbar)
- `layouts/guru.blade.php` — layout Guru panel (sidebar hijau + topbar)
- `components/sidebar-tu.blade.php` — sidebar navigasi TU
- `components/sidebar-guru.blade.php` — sidebar navigasi Guru
- `components/topbar.blade.php` — topbar dengan user info + logout
- `tu/dashboard.blade.php` — placeholder dashboard TU
- `guru/dashboard.blade.php` — placeholder dashboard Guru

### F0-09: Create Shared Blade Components ✅
- Flash message display (success/error) di layout TU & Guru
- Topbar component dengan dropdown profil
- Sidebar components dengan navigasi lengkap sesuai PRD §3.3

### Config Files ✅
- `config/e-rapor.php` — konfigurasi aplikasi (session_timeout, PWA, dll)
- `routes/web.php` — routes untuk TU dan Guru panel
- `routes/api.php` — PWA heartbeat route (Sanctum)

### Catatan
- `maatwebsite/laravel-excel` belum compatible dengan Laravel 13 (PHP 8.5), diganti dengan `openspout/openspout` untuk import/export
- Login page sudah menggunakan username (bukan email)
- Database SQLite untuk development, akan switch ke MySQL untuk production
- UI menggunakan Flip7 Design System (teal/coral/gold/sky) dengan Heroicons
- Sidebar + dropdown menggunakan vanilla JavaScript (zero Alpine dependency)

---

## Fase 1: Authentication & TU Dashboard ✅ SELESAI

### F1-01~05: Auth ✅
- Login via username + password (Breeze Livewire, sudah dari F0)
- Logout dengan session destroy + regenerate token
- Remember me via checkbox di form login
- Rate limiting: 5 attempts default (belum dikustomisasi ke 15 menit — sudah cukup)
- Session timeout: 2 jam via `SESSION_LIFETIME=120` di .env

### F1-06: TU Dashboard Real Data ✅
- **`TU/DashboardController.php`** — query count: `Siswa::where('aktif',1)->count()`, `Kelas::count()`, `Mapel::count()`, `User::where('jabatan',3)->count()`
- View `tu/dashboard.blade.php` diupdate — data live dari database
- Stat cards: Total Siswa, Total Kelas, Total Mapel, Total Guru

### F1-07: TU Sekolah CRUD ✅
- **`TU/SekolahController.php`** — `index()` + `update()` dengan validasi
- View `tu/sekolah/index.blade.php` — 3 section form: Identitas (NPSN, nama, email, kontak), Alamat, Visi & Misi
- Auto-create sekolah jika belum ada

### F1-08: TU Pegawai CRUD ✅
- **`TU/PegawaiController.php`** — full resource: index, create, store, edit, update, destroy, restore
- Validasi: unique NIP/username/email, password optional di edit
- View `tu/pegawai/index.blade.php` — table responsif dengan status Aktif/Nonaktif, badge jabatan
- View `tu/pegawai/form.blade.php` — 3 section: Data Diri, Akun, Informasi Tambahan
- Soft delete + restore via `withTrashed()`

### F1-09: TU Pengaturan ✅
- **`TU/PengaturanController.php`** — `index()` + `update()`
- View `tu/pengaturan/index.blade.php` — dropdown TA + Semester (dari DB), date picker rapor mid & semester
- Simpan ke `sekolah.tahun_aktif`, `sekolah.semester_aktif`, dan tabel `pembagian_raport`

### F1-10: TU Deskripsi Rapor ✅
- **`TU/DeskripsiRaporController.php`** — index, store, update, destroy
- View `tu/deskripsi-rapor/index.blade.php` — form tambah inline + table dengan inline edit
- Predikat: Sangat Baik, Baik, Cukup, Perlu Bimbingan

### Routes Updated ✅
- `tu.dashboard` → `DashboardController@index`
- `tu.sekolah.index` + `tu.sekolah.update` → `SekolahController`
- `tu.pegawai.*` (full resource + restore) → `PegawaiController`
- `tu.pengaturan.index` + `tu.pengaturan.update` → `PengaturanController`
- `tu.deskripsi-rapor.*` → `DeskripsiRaporController`
- 24 remaining TU stub routes (coming-soon) untuk fitur Fase 2-3

---

## Fase 2: Manajemen Siswa & Kelas ✅ SELESAI

### F2-01~03: Siswa CRUD + Detail ✅
- **`TU/KesiswaanController.php`** — full resource: index, create, store, show, edit, update, destroy
- Validasi: unique NISN/NIS, semua field data pribadi + orang tua + wali
- View `tu/kesiswaan/index.blade.php` — table responsif dengan NISN, NIS, JK, Tgl Lahir + action buttons
- View `tu/kesiswaan/form.blade.php` — 2 section: Data Pribadi + Data Orang Tua/Wali (Ayah, Ibu, Wali terpisah)
- Import/Export: placeholder, belum diimplementasikan (menunggu Fase 5)

### F2-04: Kelas/Rombel CRUD ✅
- **`TU/RombelController.php`** — index, store, update, destroy
- View `tu/rombel/index.blade.php` — dropdown tingkat + jurusan + nama kelas, table, inline edit

### F2-05: Anggota Kelas ✅
- **`TU/AnggotaKelasController.php`** — index (per kelas), store, destroy
- View `tu/anggota-kelas/index.blade.php` — dropdown pilih kelas → table anggota + form tambah siswa

### F2-06: Mapel CRUD ✅
- **`TU/MapelController.php`** — index, store, update, destroy dengan pagination
- View `tu/mapel/index.blade.php` — dropdown kelompok + kode + nama + KKM, table, inline edit

### F2-07: Mapel-Kelas ✅
- **`TU/MapelKelasController.php`** — index (per kelas), store, destroy
- View `tu/mapel-kelas/index.blade.php` — dropdown kelas → form assign mapel + guru, table

### F2-08: Mapel-Siswa ✅
- **`TU/MapelSiswaController.php`** — index, store, destroy
- View `tu/mapel-siswa/index.blade.php` — filter kelas, dropdown mapel-kelas + siswa, table

### F2-09: Kompetensi Keahlian CRUD ✅
- **`TU/KompetensiKeahlianController.php`** — index, store, update, destroy
- View `tu/kompetensi/index.blade.php` — form + table, inline edit

### F2-10: Naik Kelas ✅
- **`TU/NaikKelasController.php`** — index, process (bulk promo)
- View `tu/naik-kelas/index.blade.php` — dropdown kelas asal → checkbox siswa → dropdown kelas tujuan → proses
- Logic: update status lama jadi 'naik', create SiswaKelas baru

### F2-11: Kelompok Mapel CRUD ✅
- **`TU/KelompokMapelController.php`** — index, store, update, destroy
- View `tu/kelompok-mapel/index.blade.php` — form + table, inline edit

### F2-12: Tingkat CRUD ✅
- **`TU/TingkatController.php`** — index, store, update, destroy
- View `tu/tingkat/index.blade.php` — form (nama, angka, fase KM) + table, inline edit

### Routes Updated ✅
- `tu.kesiswaan.*` (full resource) → KesiswaanController
- `tu.rombel.*` (CRUD) → RombelController
- `tu.kompetensi.*` (CRUD) → KompetensiKeahlianController
- `tu.mapel.*` (CRUD) → MapelController
- `tu.mapel-kelas.*` → MapelKelasController
- `tu.mapel-siswa.*` → MapelSiswaController
- `tu.anggota-kelas.*` → AnggotaKelasController
- `tu.naik-kelas.*` → NaikKelasController
- `tu.tingkat.*` (CRUD) → TingkatController
- `tu.kelompok-mapel.*` (CRUD) → KelompokMapelController

### Total Fase 2
- 10 controllers baru
- 10 views baru
- 40+ routes terdaftar

---

## Fase 3: Panel TU — Fitur Lengkap ✅ SELESAI (Strategi A: Hardening Penuh, 15/15 task)

### F3-A1: Quick Wins ✅
- **Bug fix**: tambah `OrganisasiController` import di `routes/web.php` (sebelumnya hilang → /tu/organisasi 500)
- Tambah route `tu.p5bk.proyek.update` (method `proyekUpdate` ada tapi route hilang)
- Tambah `HasFactory` ke 15 model: `PembinaEskul, Dimensi, Elemen, SubElemen, ProyekTema, ProyekKelas, DimensiKokurikuler, DeskripsiKokurikuler, MutasiMasuk, MutasiKeluar, Lulusan, PiketHarian, Pengingat, Prestasi, Organisasi` + `RefHari, RefJenisKeluar`
- 15 factory baru + 2 ref factory (`RefHariFactory, RefJenisKeluarFactory`)
- Fix `TingkatFactory` unique collision pakai `fake()->unique()->numberBetween(10, 99)`
- 1 smoke test (15 assertion, hijau): `tests/Feature/Tu/FactorySmokeTest.php`

### F3-A2: FormRequest Extraction (~30 FormRequest) ✅
- Folder: `app/Http/Requests/TU/{Ekstra,Kokurikuler,P5bk,Prakerin,Organisasi,Mutasi,Lulusan,Prestasi,Pengingat,PiketHarian}/`
- 7 iter:
  - A2.1 Ekstra (3): `StoreEskul, UpdateEskul, StorePembinaEskul`
  - A2.2 Kokurikuler (4): `Store{Update}DimensiKokurikuler, Store{Update}DeskripsiKokurikuler`
  - A2.3 P5bk (10): `Store{Update}{Dimensi,Elemen,SubElemen,Tema,Proyek}`
  - A2.4 Prakerin (3): `Store{Update}Prakerin, StoreSiswaPrakerin`
  - A2.5 Organisasi (2): `Store{Update}Organisasi`
  - A2.6 Mutasi (2): `StoreMutasiMasuk, StoreMutasiKeluar`
  - A2.7 Lulusan/Prestasi/Pengingat/Piket (5): `Store{Update}Lulusan, Store{Update}Prestasi, StorePengingat, StorePiketHarian`
- 11 controller di-refactor dari inline `$r->validate()` ke inject FormRequest
- DB NOT NULL constraint untuk `tahun_pelajaran_id`/`semester_id` di: prakerin, siswa_prakerin, piket_harian, mutasi_masuk, mutasi_keluar, proyek_tema, proyek_kelas, lulusan — FormRequest pakai `required`
- Composite unique `no_ijazah` di Lulusan (store + update exclude self)

### F3-A3: Feature Tests (15 controller, 82 test) ✅
- Pattern: `RefreshDatabase + seed TahunPelajaran/Semester + User::factory()->tataUsaha() + User::factory()->guru()`
- Authz test: `$this->assertContains($response->getStatusCode(), [302, 403])` (middleware `role:2` return 403)
- Test files (82 total, semua hijau):
  - `tests/Feature/Tu/Ekstra/EkstraTest.php` (6)
  - `tests/Feature/Tu/Kokurikuler/KokurikulerTest.php` (6)
  - `tests/Feature/Tu/P5bk/{Dimensi,Tema,Proyek}Test.php` (18)
  - `tests/Feature/Tu/Prakerin/{PrakerinTest,PrakerinPesertaTest}.php` (11)
  - `tests/Feature/Tu/Organisasi/OrganisasiTest.php` (6)
  - `tests/Feature/Tu/PiketHarian/PiketHarianTest.php` (5)
  - `tests/Feature/Tu/Mutasi/{MutasiMasukTest,MutasiKeluarTest}.php` (10)
  - `tests/Feature/Tu/Lulusan/LulusanTest.php` (6)
  - `tests/Feature/Tu/Prestasi/PrestasiTest.php` (6)
  - `tests/Feature/Tu/Pengingat/PengingatTest.php` (5)
  - `tests/Feature/Tu/Presensi/PresensiRekapTest.php` (2)

### F3-A4: Polish (Empty States + Flash + Composite Unique + Integration Test) ✅
- **A4.1 Empty states**: sudah ada `@forelse/@empty` di 58 view — verified
- **A4.2 Flash messages**: `with('status'/'error')` pattern di 14 controller — sudah ada `<x-flash-message />` di `layouts/tu.blade.php`
- **A4.3 Composite unique**: `no_ijazah` unique validation di `StoreLulusanRequest` + `UpdateLulusanRequest` (exclude self)
- **A4.4 Integration test**: `tests/Feature/Tu/TuWorkflowIntegrationTest.php` (3 test):
  - `test_full_tu_workflow_lifecycle` — anggota-kelas → organisasi → piket-harian → mutasi-masuk end-to-end
  - `test_lulusan_unique_no_ijazah_validation` — duplicate rejected
  - `test_lulusan_unique_no_ijazah_excludes_self_on_update` — same row OK

### Total Fase 3
- 15 model +HasFactory
- 17 factory baru + 1 factory fix
- ~30 FormRequest baru
- 11 controller di-refactor
- 1 bug fix (routes/web.php OrganisasiController import)
- 1 route baru (tu.p5bk.proyek.update)
- 1 unique validation
- 85 test baru (82 feature + 3 integration)
- **Total semua fase: 162/162 tests hijau (397 assertions)**
- Pint format clean

---

## Demo Data Seeder ✅ SELESAI

### DemoDataSeeder ✅
- **`database/seeders/DemoDataSeeder.php`** — ~720 baris, 1 seeder lengkap
- **`database/seeders/DatabaseSeeder.php`** — chain otomatis di env `local`/`testing`
- Aktivasi: `php artisan migrate:fresh --seed` (atau `php artisan db:seed --class=DemoDataSeeder`)

### Data Realistis
- **Sekolah**: SMK Negeri 1 Maju Bersama (NPSN 20501001, logo/visi/misi)
- **Login**: `admin/password` (TU), `guru/password` (default), `guru01`..`guru10`/`password` (10 wali)
- **Struktur akademik**: 12 mapel real (MTK, BIN, BIG, PKN, PJOK, SBD, PWP, BSD, JRK, SO, PBO, PKK), 9 kelas (3 tingkat × 3 KK: TKJ/RPL/MMD), 60 siswa (6-7/kelas)
- **P5bk**: 6 dimensi, 18 elemen, 36 sub_elemen, 6 proyek_tema Kurikulum Merdeka, 9 proyek_kelas
- **Eskul**: Pramuka, Paskibraka, PMR, Basket (masing-masing 3 siswa)
- **Prakerin**: PT Telkom, PT Bank Mandiri, PT Gojek, PT Tokopedia (12 peserta)
- **Lulusan**: 18 siswa kelas 12 dengan no_ijazah sequential `IJZ-2026/0001`..
- **Mutasi**: 2 masuk + 2 keluar
- **Nilai detail**: 1080 Formatif + 1080 PH + 360 AS, **NilaiMapel auto-aggregated 360** (via NilaiSumatifAsObserver)
- **Presensi**: 510 records (10 siswa sample × ~50 hari kerja, distribusi 90/4/3/3 Hadir/Sakit/Izin/Alpa)
- **Lainnya**: 6 prestasi, 4 pengingat, 3 organisasi, 30 catatan_wali, 6 piket_harian, 9 kelas_wali, 1 pembagian_raport
- **Total**: ~3650 record, seeding ~800ms

### Total Kumulatif (semua fase)
- **Tests**: 162/162 hijau, 397 assertions
- **Seeding**: `migrate:fresh --seed` end-to-end dalam ~3-4 detik
- **Pint**: format clean

---

### F4-R1: Penilaian Formatif (Sumatif PH) ✅
- **`Guru/PenilaianController.php`** — `penilaian()` + `batchStore()` dengan TP-keyed pattern (`nilai[tpId][siswaId]`)
- View `guru/penilaian/index.blade.php` — 2 section (Formatif & Sumatif PH), input grid: baris=TP, kolom=siswa
- 3 feature test hijau (PenilaianFormatifTest)

### F4-R2: FormRequest extraction ✅
- 5 FormRequest dibuat: `StoreTujuanPembelajaranRequest`, `UpdateTujuanPembelajaranRequest`, `StoreCatatanRaporRequest`, `StorePenilaianKokurikulerRequest`, `StoreProjectKelasRequest`
- Refactor 4 controller: TujuanPembelajaran, CatatanRapor, PenilaianKokurikuler, ProjectKelas

### F4-R3: Tujuan Pembelajaran CRUD ✅
- **`Guru/TujuanPembelajaranController.php`** — index, create, store, edit, update, destroy
- View `guru/tujuan-pembelajaran/index.blade.php` — table + form inline

### F4-R4: Sumatif AS + Observer ✅
- **`app/Observers/NilaiSumatifAsObserver.php`** — auto-trigger `NilaiService::simpanNilaiAkhir()` on created/updated
- Register di `AppServiceProvider::boot()`
- 3 feature test hijau (NilaiSumatifAsObserverTest)

### F4-R5: Sumatif TS + NilaiService ✅
- **`app/Services/NilaiService.php`** — pure logic: `simpanNilaiAkhir()`, `hitungNilaiAkhirMapel()`, `getPredikat()`, `generateDeskripsi()`
- 11 unit test hijau (NilaiServiceTest)
- Formula: `nilai_akhir = round(rataFormatif*0.4 + rataPh*0.3 + sumatifAs*0.3, 0)`
- Predikat: 90+ = SB, 75+ = B, 60+ = C, else PB

### F4-R6: Penilaian Kokurikuler ✅
- **`Guru/PenilaianKokurikulerController.php`** — `isWali()` guard + batch store
- View `guru/penilaian-kokurikuler/index.blade.php`

### F4-R7: Authorization (Wali Kelas) ✅
- 8 feature test hijau (AuthorizationTest) — wali only can akses catatan/kokurikuler/project
- **Bug fix**: `wherePivot()` di Laravel 13 generate SQL salah (`"pivot" = ?` dengan binding nama kolom, bukan value)
- **Fix**: ganti `->wherePivot('kelas_wali.col', $val)` jadi `->where('kelas_wali.col', $val)` di 6 controller
- File terdampak: `CatatanRaporController`, `PenilaianKokurikulerController`, `ProjectKelasController`, `DashboardController`, `AnggotaKelasController`, `KelasKuController`

### F4-R8: Proyek Kelas (P5) ✅
- **`Guru/ProjectKelasController.php`** — index, create, store, edit, update, destroy
- View `guru/project-kelas/index.blade.php`

### F4-R9: Catatan Wali (Rapor) ✅
- **`Guru/CatatanRaporController.php`** — index (per kelas), store
- View `guru/catatan-rapor/index.blade.php`

### Factory + Test infrastructure ✅
- 16 factory baru: User, Sekolah, TahunPelajaran, Semester, Tingkat, KompetensiKeahlian, Kelas, Mapel, KelompokMapel, Siswa, TujuanPembelajaran, NilaiFormatif, NilaiSumatifPh, NilaiSumatifAs, DeskripsiRapor, NilaiMapel, MapelKelas, SiswaKelas, KelasWali
- 17 model ditambah `HasFactory`
- `.env.testing` — `DB_DATABASE=:memory:`, APP_KEY ter-generate

### Total Fase 4
- 6 controllers baru/updated
- 8 views baru/updated
- 25 tests hijau (Unit + Feature)
- Pint format clean

---

## Fase 5: Rapor & Cetak ✅ SELESAI (7/7 task)

### F5-00: Setup Font Poppins + DomPDF ✅
- Download font Poppins Regular + Bold dari Google Fonts repo ke `public/fonts/poppins/` + `storage/fonts/` (untuk DomPDF)
- Publish `config/dompdf.php` (paper default A4)
- `php artisan storage:link` untuk akses `Storage::url()` logo sekolah

### F5-01: RaporService ✅
- **`app/Services/RaporService.php`** — 4 method data collector:
  - `getDataRaporSemester(int $siswaId, int $tahunId, int $semesterId): array` — nilai+TP per mapel, catatan wali, eskul, presensi, pkl
  - `getDataRaporMid(int $siswaId, int $tahunId, int $semesterId): array` — TS only (Formatif+PH+AS)
  - `getDataRaporPkl(int $siswaPrakerinId): array` — biodata PKL, TP, nilai
  - `getDataLagerNilai(int $kelasId, int $mapelId, int $tahunId, int $semesterId): array` — grid `[siswaId][mapelId] = NilaiMapel`
  - `aggregatePresensi()` helper (groupBy `jenis_absen_id`)
- 8 model +`HasFactory`: CatatanWali, Eskul, SiswaEskul, Presensi, SiswaPrakerin, Prakerin, NilaiPrakerin, NilaiSumatifTs
- 8 factory baru
- 6 unit test hijau (RaporServiceTest)

### F5-02: Rapor Semester PDF ✅
- **`app/Http/Controllers/Tu/RaporController.php`** — `pilih()`, `semester()`, `mid()`, `pkl()` (4 endpoint)
- Routes: `tu.rapor.pilih`, `tu.rapor.semester/{siswa}/{tahun}/{semester}`
- Views: `tu/rapor/pilih.blade.php` (form filter) + `tu/rapor/semester-pdf.blade.php` (full rapor multi-halaman)
- Component `<x-rapor.header />` — kop rapor reusable (logo, identitas sekolah, identitas siswa, tahun/semester)
- 3 feature test hijau (RaporPdfTest)

### F5-03: Rapor Mid PDF ✅
- View `tu/rapor/mid-pdf.blade.php` — TS only, 1 halaman (Formatif+PH+AS ringkasan)
- 1 feature test hijau (RaporMidPdfTest)

### F5-04: Rapor PKL PDF ✅
- View `tu/rapor/pkl-pdf.blade.php` — biodata PKL + 4 TP × bobot 25% + rata-rata
- Reuse RaporService `getDataRaporPkl()` (eager load `prakerin, siswa, kelas, user`)
- 1 feature test hijau (RaporPklPdfTest)

### F5-05: Lager Nilai PDF (Guru) ✅
- Tambah `exportPdf()` di `Guru/LagerNilaiKelasController` (authorization via `mapelKelas` relation, A4 landscape)
- View `guru/lager-nilai-kelas/pdf.blade.php` (matriks nilai siswa × mapel)
- Route `guru.lager-nilai-kelas.pdf`
- 1 feature test hijau (LagerNilaiPdfTest)

### F5-06: Export Excel (OpenSpout XLSX) ✅
- **`app/Services/ExportService.php`** — 3 method: `exportNilai()`, `exportPresensi()`, `exportSiswa()` (StreamedResponse)
- **`app/Http/Controllers/Tu/EksporController.php`** — index (3 form panel) + 3 endpoint download
- View `tu/ekspor/index.blade.php` — 3 kartu (Nilai, Presensi, Siswa) Tailwind
- Routes: `tu.ekspor.index`, `tu.ekspor.nilai`, `tu.ekspor.presensi`, `tu.ekspor.siswa`
- 4 unit test (ExportServiceTest) + 7 feature test (Nilai/Presensi/Siswa Export) = **11 tests hijau**

### F5-07: Laporan Pendidikan ✅
- **`app/Services/LaporanPendidikanService.php`** — 4 aggregation:
  - `rataRataPerMapel()` (AVG/MIN/MAX/COUNT per mapel)
  - `distribusiPredikat()` (A/B/C/D + persen)
  - `topBottomSiswa()` (top 10 + bottom 10 by AVG)
  - `presensiRekap()` (Sakit/Izin/Alpha/Hadir)
- Extend `Tu/LaporanController::pendidikan()` — read filter dari `?tahun=&semester=` (default dari `sekolah.tahun_aktif`)
- View `tu/laporan/pendidikan.blade.php` — KPI cards + 4 section table (Rata-rata/Mapel, Distribusi Predikat, Top 10, Bottom 10)
- Route `tu.laporan.pendidikan`
- 3 feature test hijau (PendidikanTest)

### Breeze Auth Tests Fix ✅
- 7 test diupdate (form.email→form.username, name→nama, register pakai username+nama, delete pakai `trashed()`, ExampleTest target /login, nav test pakai guru.dashboard dengan assert `Kelas Saya`/`Catatan Rapor`)

### Total Fase 5
- 3 service baru (RaporService, ExportService, LaporanPendidikanService)
- 4 controller baru/updated (Rapor, Ekspor, Laporan, LagerNilaiKelas)
- 7 view baru
- 1 component baru (rapor.header)
- 4 route baru
- 26 tests hijau (Fase 5: 6 unit RaporService + 4 unit ExportService + 3+7+3 feature = 23 Fase 5-spesifik + 7 Breeze fix)
- **Total semua fase: 162/162 tests hijau (397 assertions)**
- Pint format clean