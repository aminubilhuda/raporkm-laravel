# Product Requirements Document (PRD)
# E-Rapor SMK Abdi Negara Tuban — Laravel 13 Migration

> **Versi**: 1.1  
> **Tanggal**: 01 Juni 2026  
> **Status**: Draft  
> **Penulis**: Aminu Bil Huda, S.Kom  
> **Project**: E-Rapor Kurikulum Merdeka  
> **Domain Production**: km.smkan.sch.id  
> **Server**: aaPanel (VPS)

---

## 1. Executive Summary

### 1.1 Latar Belakang

Aplikasi **E-Rapor SMK Abdi Negara Tuban** saat ini dibangun menggunakan PHP native (tanpa framework) dengan arsitektur page-based monolitik. Aplikasi ini memiliki ~62 halaman, 55+ tabel database, dan melayani 2 role utama: Tata Usaha (admin) dan Guru (wali kelas/pengajar). Meskipun fungsional, arsitektur saat ini menghadapi tantangan:

- **Tidak ada framework** — routing manual via `?pages=key`, tidak ada middleware, tidak ada dependency injection
- **Tidak ada ORM** — SQL raw dengan mysqli prepared statements
- **Tidak ada testing** — zero automated tests
- **Tidak ada namespacing** — semua class di global namespace
- **Security credentials hardcode** — token bot dan kredensial DB di source code
- **Mixed PHP/HTML** — logic dan presentation bercampur di setiap file
- **Sulit di-maintain** — perubahan satu fitur bisa menyentuh banyak file

### 1.2 Tujuan Migrasi

Membangun ulang aplikasi E-Rapor menggunakan **Laravel 13** dengan **Livewire** sebagai stack frontend, mengikuti best practices modern PHP development, sambil mempertahankan **100% fitur** yang ada di sistem lama dan menambahkan perbaikan arsitektur.

### 1.3 Pendekatan

**Migrasi bertahap (incremental)** — membangun fitur per fitur di environment development, lalu melakukan **switchover penuh** setelah fitur kritis siap. Database akan **di-redesign dari awal** menggunakan Laravel migrations, mengikuti konvensi penamaan dan relasi Eloquent.

### 1.4 Target Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13 (PHP 8.4+) |
| Frontend | Livewire 3 + Blade + Tailwind CSS |
| Database | MySQL 8.x / MariaDB 10.6+ |
| PDF Generation | barryvdh/laravel-dompdf |
| Excel Import/Export | maatwebsite/laravel-excel |
| Auth | Laravel Breeze (Livewire stack) + Custom Role |
| API Token | Laravel Sanctum (PWA auto-login) |
| PWA | Custom Service Worker + manifest.json |
| Notifications | Laravel Notification + Custom WA/Telegram Channel |
| Testing | PHPUnit + Pest |

---

## 2. Sistem Saat Ini (As-Is)

### 2.1 Arsitektur Saat Ini

```
raporkm/                          # Document root
├── index.php                     # Redirect → login.php
├── login.php                     # Login form + auth logic (inline)
├── logout.php                    # Session destroy
├── config/
│   ├── Database.php              # Singleton DB class (mysqli prepared stmts)
│   ├── Session.php               # Custom session management + CSRF + rate limit
│   ├── Request.php               # Simple request helper
│   ├── helpers.php               # Global db_query/db_fetch helpers
│   ├── koneksi.php               # DB connection config + global includes
│   ├── function_antiinjection.php # SQL injection sanitization
│   ├── fungsi_validasi.php       # Input validation class
│   └── function_date.php         # Indonesian date formatting
├── tu/                            # Panel Tata Usaha (Admin)
│   ├── index.php                 # Dashboard shell (sidebar + topbar + content)
│   ├── content.php               # Page router (?pages=key → whitelist include)
│   ├── dashboard.php             # TU dashboard
│   ├── sekolah.php               # School profile management
│   ├── pegawai.php               # Employee/teacher CRUD
│   ├── kesiswaan.php             # Student data management
│   ├── kesiswaan-upload.php      # CSV student import
│   ├── kesiswaan_export.php      # Student data export
│   ├── rombel.php                # Class groups (Rombel)
│   ├── mapel.php                 # Subjects CRUD
│   ├── mapel-kelas.php           # Subject-to-class assignment
│   ├── mapel-siswa.php           # Student subject selection
│   ├── anggota-kelas.php         # Class members management
│   ├── naik-kelas.php            # Grade promotion
│   ├── ekstra.php                # Extracurricular activities
│   ├── organisasi.php            # Student organizations
│   ├── kompetensi-keahlian.php   # Competency programs (Jurusan)
│   ├── prakerin.php              # Internship management
│   ├── prakerin-upload.php       # Internship data upload
│   ├── prakerin-update-guru.php  # Internship advisor assignment
│   ├── piket-harian.php          # Daily picket schedule
│   ├── pengingat.php             # Reminders
│   ├── laporan_wa.php            # WhatsApp sending reports
│   ├── laporan-pendidikan.php    # Education reports
│   ├── pengaturan.php            # Settings (academic year, report dates)
│   ├── deskripsi-rapor.php       # Report card description templates
│   ├── p5bk.php                  # P5 (Projek Penguatan Profil Pancasila) management
│   ├── kokurikuler.php           # Co-curricular activities
│   ├── detail-kokurikuler.php    # Co-curricular detail
│   ├── penilaian-kokurikuler.php # Co-curricular assessment
│   ├── penilaian-profil-pancasila.php # Pancasila profile assessment
│   ├── managemen-tema.php        # Theme management
│   ├── managemen-dimensi.php     # Dimension management
│   ├── managemen-elemen.php      # Element management
│   ├── managemen-sub-elemen.php  # Sub-element management
│   ├── detail-project.php        # Project details
│   ├── mutasi-masuk.php          # Inbound student transfer
│   ├── mutasi-keluar.php         # Outbound student transfer
│   ├── lulusan.php               # Graduate data
│   ├── buku-induk.php            # Student master data book
│   ├── rekap-presensi.php        # Attendance recap
│   ├── nilai-akademik.php        # Academic grades
│   └── ... (voting, rekrutmen, etc.)
├── guru/                          # Panel Guru (Teacher)
│   ├── index.php                 # Dashboard shell
│   ├── content.php               # Page router (?pages=key → whitelist include)
│   ├── dashboard.php             # Teacher dashboard
│   ├── kelas-ku.php              # My classes
│   ├── anggota-kelas.php         # Class members (homeroom teacher)
│   ├── tujuan-pembelajaran.php   # Learning objectives (TP)
│   ├── tujuan-pembelajaran_umum.php # General learning objectives
│   ├── penilaian.php             # Assessment entry (formatif + sumatif)
│   ├── lager-nilai-kelas.php     # Grade ledger per class
│   ├── catatan-rapor.php         # Report card notes
│   ├── project-kelas.php         # Class projects (P5)
│   ├── detail-project.php        # Project details
│   ├── p5bk.php                  # P5 assessment
│   ├── kokurikuler.php           # Co-curricular
│   ├── penilaian-kokurikuler.php # Co-curricular assessment
│   ├── penilaian-profil-pancasila.php # Pancasila profile assessment
│   ├── ekstra.php                # Extracurricular (for advisors)
│   ├── prakerin.php              # Internship advisor view
│   ├── rapor-pkl.php             # PKL report
│   ├── rekap-presensi.php        # Attendance recap
│   ├── absensi-bk.cpp            # BK attendance
│   ├── piket-harian.php          # Daily picket
│   └── organisasi.php            # Student organization
├── api/
│   └── ping.php                  # PWA heartbeat
├── bot/
│   ├── wa/                       # WhatsApp bot (Fonnte API)
│   │   ├── functionbot.php       # Broadcast + wali contact functions
│   │   ├── token.php             # **HARDCODED TOKEN** (security issue)
│   │   ├── webhook.php           # Fonnte webhook receiver
│   │   └── kirim_pesan.php       # Send message handler
│   └── tg/                       # Telegram bot
│       ├── config.php            # **HARDCODED BOT TOKEN** (security issue)
│       ├── command.php           # Command handler
│       └── sendmessage.php       # Send message
├── assets/                        # Static frontend assets
├── migrations/                    # PWA + Remember token SQL
├── sessions/                      # PHP session files (gitignored)
├── storage/logs/                  # Log files (gitignored)
├── vendor/                        # Composer (dompdf, phpspreadsheet)
└── manifest.json + sw.js          # PWA manifest & service worker
```

### 2.2 Database Schema Saat Ini

Sistem saat ini memiliki **55+ tabel** MySQL. Berikut ringkasan domain dan tabel utama:

#### Domain: Autentikasi & Pengguna
| Tabel | Keterangan |
|---|---|
| `users` | Guru & TU (jabatan: 2=TU, 3=Guru). PK: `id_user`. Password via `password_hash()` |
| `remember_tokens` | Remember-me token (SHA-256 hashed, 30 hari) |
| `pwa_tokens` | PWA auto-login token (hash, 1 tahun) |
| `jabatan` | Referensi jabatan |
| `kepegawaian` | Referensi status kepegawaian |

#### Domain: Master Data
| Tabel | Keterangan |
|---|---|
| `sekolah` | Profil sekolah (NPSN, nama, alamat, logo, visi misi) |
| `siswa` | Data siswa (NISN, NIS, nama, orang tua, alamat) |
| `kelas` | Rombongan belajar (id_tingkat + id_kompetensi_keahlian + nama_kelas) |
| `tingkat` | Tingkat kelas (10, 11, 12) dengan fase KM (E, F, etc.) |
| `kompetensi_keahlian` | Program keahlian / jurusan |
| `mapel` | Mata pelajaran (id_kelompok, nama_mapel) |
| `kelompok_mapel` | Kelompok mapel (A, B, C, dll.) |
| `tahun_pelajaran` | Referensi tahun pelajaran |
| `semester` | Referensi semester (Ganjil/Genap) |

#### Domain: Penugasan & Relasi
| Tabel | Keterangan |
|---|---|
| `siswa_kelas` | Penugasan siswa ke kelas per tahun/semester (status: aktif/naik/dll) |
| `kelas_wali` | Penugasan wali kelas |
| `mapel_kelas` | Penugasan mapel ke kelas + guru pengajar |
| `mapel_siswa` | Mapel yang diambil siswa |
| `pembina_eskul` | Penugasan pembina eskul |

#### Domain: Penilaian
| Tabel | Keterangan |
|---|---|
| `tujuan_pembelajaran` | Tujuan Pembelajaran (TP) per mapel/kelas |
| `nilai_formatif` | Nilai formatif per TP per siswa |
| `nilai_sumatif_ph` | Nilai Sumatif PH (Penilaian Harian) per TP per siswa |
| `nilai_sumatif_ts` | Nilai Sumatif TS (Tengah Semester) per mapel/kelas |
| `nilai_sumatif_as` | Nilai Sumatif AS (Akhir Semester) per mapel/kelas |
| `lager_nilai_mapel` | Konsolidasi nilai akhir per mapel (formatif+PH+TS+AS) |
| `lager_nilai_mid` | Konsolidasi nilai mid-semester |
| `nilai_mapel` | Nilai akhir mapel (nilai + deskripsi + KKTP) |
| `nilai_mapel_mid` | Nilai mapel mid-semester |
| `nilai_mata_pelajaran` | Nilai mata pelajaran |
| `nilai_kelas` | Ringkasan nilai per kelas |
| `nilai_kelas_mid` | Ringkasan nilai mid per kelas |

#### Domain: P5 (Projek Penguatan Profil Pelajar Pancasila)
| Tabel | Keterangan |
|---|---|
| `dimensi` | 6 dimensi Profil Pancasila |
| `elemen` | Elemen per dimensi |
| `sub_elemen` | Sub-elemen dengan capaian |
| `proyek_tema` | Tema proyek P5 |
| `proyek_kelas` | Proyek P5 per kelas (judul, deskripsi) |
| `proyek_subelemen` | Mapping proyek → sub-elemen |
| `proyek_tujuan` | Tujuan proyek per dimensi |
| `mapel_proyek` | Mapel yang terkait proyek |
| `nilai_proyek` | Nilai proyek per siswa per dimensi/elemen |
| `nilai_assesmen_subelemen` | Assessmen sub-elemen per siswa |

#### Domain: Kokurikuler
| Tabel | Keterangan |
|---|---|
| `dimensi_kokurikuler` | Dimensi kokurikuler |
| `deskripsi_kokurikuler` | Deskripsi predikat (Kurang/Cukup/Baik/Sangat Baik) |
| `nilai_kokurikuler` | Nilai kokurikuler per proyek/siswa |

#### Domain: Ekstrakurikuler
| Tabel | Keterangan |
|---|---|
| `eskul` | Daftar eskul per sekolah |
| `siswa_eskul` | Peserta eskul (predikat + keterangan) |

#### Domain: Prakerin (Praktik Kerja Industri)
| Tabel | Keterangan |
|---|---|
| `prakerin` | Data prakerin (mitra, lokasi, tanggal, instruktur) |
| `siswa_prakerin` | Peserta prakerin |
| `nilai_prakerin` | Nilai prakerin per siswa per mapel |

#### Domain: Presensi & Kehadiran
| Tabel | Keterangan |
|---|---|
| `presensi` | Presensi harian siswa |
| `absen` | Referensi jenis absen |
| `harian` | Referensi hari |
| `piket_harian` | Jadwal piket guru |

#### Domain: Lain-lain
| Tabel | Keterangan |
|---|---|
| `catatan_wali` | Catatan wali kelas per siswa |
| `deskripsi_rapor` | Template deskripsi rapor (kriteria + contoh) |
| `pembagian_raport` | Tanggal pembagian rapor mid & semester |
| `kepala_sekolah` | Data kepala sekolah per tahun |
| `prestasi` | Prestasi siswa (tingkat + penyelenggara) |
| `mutasi_masuk` | Mutasi masuk siswa |
| `mutasi_keluar` | Mutasi keluar siswa |
| `lulusan` | Data kelulusan |
| `pengingat` | Pengingat otomatis |
| `surat_masuk` | Surat masuk |
| `rekrutmen` | PPDB/Penerimaan peserta didik baru |
| `laporan_wa` | Laporan pengiriman WhatsApp |

### 2.3 Alur Autentikasi Saat Ini

```
login.php
  ├── POST username + password + CSRF token
  ├── CSRF token validation (Session::checkCsrf)
  ├── Rate limit check (5 attempts / 15 min, file-based)
  ├── DB lookup: SELECT * FROM users WHERE username=?
  ├── password_verify(password, hash)
  ├── Session::loginUser()
  │   ├── session_regenerate_id(true)
  │   ├── Set session: id_user, jabatan, nama, foto, login_at, user_agent
  │   ├── If PWA mode → create pwa_token (1 year)
  │   ├── If remember → create remember_token (30 days)
  │   └── Reset login attempts
  ├── Role redirect:
  │   ├── jabatan=2 → tu/index.php  (Tata Usaha)
  │   └── jabatan=3 → guru/index.php (Guru)
  └── Error: SweetAlert2 message

Session Validation (on every page):
  ├── koneksi.php → Session::isValid()
  │   ├── Check id_user & jabatan in session
  │   ├── If PWA mode → skip timeout check
  │   ├── Check login_at < 2 hours
  │   ├── Check user_agent match
  │   └── Update last_active
  ├── POST requests → Session::requireCsrf()
  └── Role check → Session::requireRole('2') or ('3')

PWA Auto-login:
  ├── Cookie pwa_token → Session::tryPwaLogin()
  │   ├── Parse id_user:token from cookie
  │   ├── Hash token → lookup pwa_tokens table
  │   ├── If valid → loginUser()
  │   └── If invalid → clear cookie
  └── Service Worker ping every 30 min → api/ping.php

Remember Me:
  ├── Cookie remember_token → Session::tryRememberLogin()
  │   ├── Parse id_user:token from cookie
  │   ├── Hash token → lookup remember_tokens table
  │   ├── If valid → loginUser()
  │   └── If invalid → clear cookie
  └── Token expires in 30 days
```

### 2.4 Routing Saat Ini

Routing menggunakan query parameter `?pages=key` dengan whitelist array:

**TU Panel** (`tu/content.php`): 46 route keys  
**Guru Panel** (`guru/content.php`): 22 route keys

Setiap `.php` file mengandung **HTML + CSS + JS + PHP logic + SQL queries** dalam satu file (monolitik).

### 2.5 Keamanan Saat Ini

| Aspek | Implementation | Status |
|---|---|---|
| SQL Injection | Prepared statements via Database class | ✅ Baik |
| CSRF | Token-based per session | ✅ Baik |
| Rate Limiting | File-based, 5 attempts/15 min | ⚠️ OK (file-based, akan lebih baik di Laravel) |
| Session Security | Custom, HttpOnly, SameSite | ✅ Baik |
| Password Hashing | `password_hash()` / `password_verify()` | ✅ Baik |
| XSS Protection | Manual (no auto-escaping) | ❌ Rentan |
| Credential Storage | Hardcode di source code | ❌ Kritis |
| Input Validation | Manual per file | ⚠️ Tidak konsisten |

---

## 3. Sistem Target (To-Be)

### 3.1 Arsitektur Laravel 13

```
e-raporkm/
├── app/
│   ├── Models/                     # Eloquent Models
│   │   ├── User.php                # TU + Guru (with role)
│   │   ├── Siswa.php
│   │   ├── Kelas.php
│   │   ├── Mapel.php
│   │   ├── Tingkat.php
│   │   ├── KompetensiKeahlian.php
│   │   ├── SiswaKelas.php
│   │   ├── KelasWali.php
│   │   ├── MapelKelas.php
│   │   ├── Sekolah.php
│   │   ├── TujuanPembelajaran.php
│   │   ├── NilaiFormatif.php
│   │   ├── NilaiSumatifPh.php
│   │   ├── NilaiSumatifTs.php
│   │   ├── NilaiSumatifAs.php
│   │   ├── LagerNilaiMapel.php
│   │   ├── NilaiMapel.php
│   │   ├── Dimensi.php
│   │   ├── Elemen.php
│   │   ├── SubElemen.php
│   │   ├── ProyekKelas.php
│   │   ├── NilaiProyek.php
│   │   ├── NilaiAssesmenSubelemen.php
│   │   ├── NilaiKokurikuler.php
│   │   ├── Eskul.php
│   │   ├── SiswaEskul.php
│   │   ├── Prakerin.php
│   │   ├── SiswaPrakerin.php
│   │   ├── NilaiPrakerin.php
│   │   ├── Presensi.php
│   │   ├── CatatanWali.php
│   │   ├── DeskripsiRapor.php
│   │   ├── PembagianRapor.php
│   │   ├── KepalaSekolah.php
│   │   ├── Prestasi.php
│   │   ├── MutasiMasuk.php
│   │   ├── MutasiKeluar.php
│   │   ├── Lulusan.php
│   │   ├── Pengingat.php
│   │   ├── PiketHarian.php
│   │   ├── PembinaEskul.php
│   │   ├── ProyekTema.php
│   │   ├── ProyekSubelemen.php
│   │   ├── ProyekTujuan.php
│   │   ├── MapelProyek.php
│   │   ├── MapelSiswa.php
│   │   ├── SuratMasuk.php
│   │   ├── LaporanWa.php
│   │   ├── Organisasi.php
│   │   └── Rekrutmen.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── AuthenticatedSessionController.php  # Override Breeze
│   │   │   ├── TU/                   # Panel Tata Usaha
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── SekolahController.php
│   │   │   │   ├── PegawaiController.php
│   │   │   │   ├── KesiswaanController.php
│   │   │   │   ├── RombelController.php
│   │   │   │   ├── MapelController.php
│   │   │   │   ├── MapelKelasController.php
│   │   │   │   ├── MapelSiswaController.php
│   │   │   │   ├── AnggotaKelasController.php
│   │   │   │   ├── NaikKelasController.php
│   │   │   │   ├── EkstraController.php
│   │   │   │   ├── OrganisasiController.php
│   │   │   │   ├── KompetensiKeahlianController.php
│   │   │   │   ├── PrakerinController.php
│   │   │   │   ├── PiketHarianController.php
│   │   │   │   ├── P5bkController.php
│   │   │   │   ├── KokurikulerController.php
│   │   │   │   ├── DeskripsiRaporController.php
│   │   │   │   ├── PengaturanController.php
│   │   │   │   ├── MutasiController.php
│   │   │   │   ├── LulusanController.php
│   │   │   │   ├── PresensiController.php
│   │   │   │   ├── LaporanPendidikanController.php
│   │   │   │   └── PengingatController.php
│   │   │   └── Guru/                  # Panel Guru
│   │   │       ├── DashboardController.php
│   │   │       ├── KelasKuController.php
│   │   │       ├── AnggotaKelasController.php
│   │   │       ├── TujuanPembelajaranController.php
│   │   │       ├── PenilaianController.php
│   │   │       ├── LagerNilaiKelasController.php
│   │   │       ├── CatatanRaporController.php
│   │   │       ├── ProjectKelasController.php
│   │   │       ├── P5bkController.php
│   │   │       ├── KokurikulerController.php
│   │   │       ├── PenilaianKokurikulerController.php
│   │   │       ├── PenilaianProfilPancasilaController.php
│   │   │       ├── EkstraController.php
│   │   │       ├── PrakerinController.php
│   │   │       ├── RaporPklController.php
│   │   │       ├── PresensiController.php
│   │   │       ├── PiketHarianController.php
│   │   │       └── OrganisasiController.php
│   │   ├── Livewire/                 # Livewire components (optional)
│   │   │   ├── TU/
│   │   │   └── Guru/
│   │   ├── Middleware/
│   │   │   ├── EnsureRole.php        # Role check (TU/Guru)
│   │   │   └── EnsurePwaToken.php    # PWA token validation
│   │   └── Requests/                 # Form Request validation
│   │       ├── TU/
│   │       └── Guru/
│   ├── Policies/                     # Authorization policies
│   ├── Exports/                      # Laravel Excel export classes
│   ├── Imports/                      # Laravel Excel import classes
│   ├── Notifications/               # WA & Telegram notifications
│   ├── Services/                     # Business logic services
│   │   ├── NilaiService.php          # Calculation logic for grades
│   │   ├── RaporService.php          # Report card generation
│   │   ├── PresensiService.php       # Attendance calculation
│   │   └── DataMigrationService.php  # Old → New DB migration
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/                   # Laravel migrations ( redesigned)
│   ├── seeders/                      # Seeders for reference data
│   └── factories/                    # Model factories for testing
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php         # Main layout (sidebar + topbar)
│   │   │   ├── tu.blade.php          # TU layout
│   │   │   ├── guru.blade.php        # Guru layout
│   │   │   └── auth.blade.php        # Auth layout
│   │   ├── components/               # Reusable Blade components
│   │   │   ├── sidebar-tu.blade.php
│   │   │   ├── sidebar-guru.blade.php
│   │   │   ├── topbar.blade.php
│   │   │   ├── breadcrumb.blade.php
│   │   │   ├── flash-message.blade.php
│   │   │   └── nilai-table.blade.php
│   │   ├── auth/                     # Breeze auth views
│   │   ├── tu/                       # TU panel views
│   │   ├── guru/                     # Guru panel views
│   │   ├── rapor/                    # Report card PDF views
│   │   └── laporan/                  # Report views
│   ├── css/                          # Tailwind CSS
│   └── js/                           # Alpine.js + custom JS
├── routes/
│   ├── web.php                       # Main routes
│   └── api.php                       # API routes (PWA heartbeat, webhook)
├── public/
│   ├── manifest.json                 # PWA manifest
│   ├── sw.js                         # Service Worker
│   └── offline.html                  # Offline fallback
├── config/
│   └── e-rapor.php                   # Custom config (sekolah, WA, TG)
├── .env                              # Environment variables
└── composer.json
```

### 3.2 Redesign Database Schema

Prinsip redesign:
- Konversi snake_case dengan `id_` prefix → Eloquent convention (`id`, `foreign_id`)
- Tambah `created_at`, `updated_at`, `deleted_at` (soft deletes) di semua tabel
- Gunakan Laravel migration conventions (unsignedBigInteger untuk FK)
- Normalisasi referensi tabel (agama, jenis_kelamin, dll.) → bisa diganti dengan enum atau tetap tabel referensi
- Tambah unique constraints yang relevan
- Perbaiki tipe data (TEXT untuk string pendek → VARCHAR, TEXT untuk deskripsi panjang)
- Konsistensi tahun_pelajaran dan semester → gunakan semester Aktif via setting

#### 3.2.1 Tabel Utama (Redesigned)

**users** (redesigned)
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jabatan TINYINT NOT NULL DEFAULT 3, -- 2=TU, 3=Guru
    nama VARCHAR(100) NOT NULL,
    nip VARCHAR(30) NULL,
    nuptk VARCHAR(30) NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    kelamin TINYINT NULL,          -- FK to referensi (jenis_kelamin)
    agama TINYINT NULL,            -- FK to referensi (agama)
    kontak VARCHAR(20) NULL,
    id_kepegawaian TINYINT NULL,
    ijazah TINYINT NULL,
    id_tugas_tambahan TINYINT NULL,
    foto VARCHAR(255) NULL,
    moto TEXT NULL,
    email VARCHAR(100) NULL UNIQUE, -- NEW: for Laravel auth
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_jabatan (jabatan),
    INDEX idx_username (username)
);
```

**sekolah** (redesigned)
```sql
CREATE TABLE sekolah (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    npsn VARCHAR(20) NOT NULL,
    nama_sekolah VARCHAR(200) NOT NULL,
    id_jenjang TINYINT NOT NULL DEFAULT 1,
    bentuk_sekolah TINYINT NOT NULL DEFAULT 1,
    yayasan TEXT NULL,
    website VARCHAR(255) NULL,
    alamat TEXT NULL,
    email VARCHAR(100) NULL,
    kontak VARCHAR(20) NULL,
    desa VARCHAR(100) NULL,
    kecamatan VARCHAR(100) NULL,
    kabupaten VARCHAR(100) NULL,
    provinsi VARCHAR(100) NULL,
    logo_prov VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    gambar1 VARCHAR(255) NULL,
    lokasi INT NULL,
    visi TEXT NULL,
    misi TEXT NULL,
    frame_peta TEXT NULL,
    tahun_aktif INT NULL,          -- NEW: active academic year
    semester_aktif INT NULL,       -- NEW: active semester
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

**siswa** (redesigned)
```sql
CREATE TABLE siswa (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_siswa VARCHAR(100) NOT NULL,
    nik_pd VARCHAR(20) NULL,
    nkk VARCHAR(20) NULL,
    nisn VARCHAR(20) NOT NULL,
    nis VARCHAR(20) NOT NULL,
    tempat_lahir VARCHAR(100) NULL,
    tanggal_lahir DATE NULL,
    kelamin TINYINT NULL,
    agama TINYINT NULL,
    kontak_siswa VARCHAR(20) NULL,
    hub_keluarga TINYINT NULL,
    jumlah_saudara INT DEFAULT 0,
    anak_ke INT DEFAULT 1,
    nama_ayah VARCHAR(100) NULL,
    nik_ayah VARCHAR(20) NULL,
    tahun_ayah INT NULL,
    pendidikan_ayah VARCHAR(20) NULL,
    pekerjaan_ayah VARCHAR(30) NULL,
    kontak_ayah VARCHAR(14) NULL,
    nama_ibu VARCHAR(100) NULL,
    nik_ibu VARCHAR(20) NULL,
    tahun_ibu INT NULL,
    pendidikan_ibu VARCHAR(20) NULL,
    pekerjaan_ibu VARCHAR(30) NULL,
    kontak_ibu VARCHAR(14) NULL,
    alamat TEXT NULL,
    alamat_orang_tua TEXT NULL,
    nama_wali VARCHAR(100) NULL,
    alamat_wali TEXT NULL,
    pekerjaan_wali VARCHAR(30) NULL,
    kontak_wali VARCHAR(14) NULL,
    terima_tingkat TINYINT NULL,
    jurusan INT NULL,
    sekolah_asal TEXT NULL,
    terima_tanggal DATE NULL,
    terima_kelas VARCHAR(10) NULL,
    foto VARCHAR(255) NULL,
    jenis_siswa TINYINT NOT NULL DEFAULT 1,
    aktif TINYINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_nisn (nisn),
    INDEX idx_nis (nis),
    INDEX idx_aktif (aktif)
);
```

> **Catatan**: Kolom `username` dan `pass`/`password` di tabel `siswa` dihapus dari model siswa baru. Jika login siswa diimplementasikan di masa depan, akan menggunakan tabel `users` terpisah dengan `jabatan=4`.

**kelas** (redesigned)
```sql
CREATE TABLE kelas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tingkat_id BIGINT UNSIGNED NOT NULL,
    kompetensi_keahlian_id BIGINT UNSIGNED NOT NULL,
    nama_kelas VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (tingkat_id) REFERENCES tingkat(id),
    FOREIGN KEY (kompetensi_keahlian_id) REFERENCES kompetensi_keahlian(id)
);
```

**Penamaan kolom FK**: Semua kolom foreign key menggunakan konvensi Laravel `{tabel_singular}_id` (contoh: `tingkat_id` bukan `id_tingkat`).

#### 3.2.2 Tabel Referensi (Redesigned)

Referensi lookup tables dipertahankan sebagai tabel terpisah (bukan enum) agar tetap fleksibel untuk CRUD di panel TU:

| Tabel Lama | Tabel Baru | Keterangan |
|---|---|---|
| `absen` | `jenis_absen` | Jenis kehadiran (Hadir, Sakit, Izin, Alpa) |
| `agama` | `ref_agama` | Referensi agama |
| `jenis_kelamin` | `ref_jenis_kelamin` | Referensi jenis kelamin |
| `hubungan_keluarga` | `ref_hubungan_keluarga` | Referensi hubungan keluarga |
| `jabatan` | `ref_jabatan` | Referensi jabatan |
| `kepegawaian` | `ref_kepegawaian` | Referensi status kepegawaian |
| `pendidikan` | `ref_pendidikan` | Referensi tingkat pendidikan |
| `tugas_tambahan` | `ref_tugas_tambahan` | Referensi tugas tambahan |
| `harian` | `ref_hari` | Referensi hari (Senin-Minggu) |
| `bulan` | `ref_bulan` | Referensi bulan |
| `jenis_siswa` | `ref_jenis_siswa` | Referensi jenis siswa |
| `jenis_keluar` | `ref_jenis_keluar` | Referensi alasan keluar |
| `kelompok_mapel` | `kelompok_mapel` | Kelompok mapel (A, B, C) |
| `kurikulum` | `ref_kurikulum` | Referensi kurikulum |

#### 3.2.3 Tabel Penilaian (Redesigned)

Semua tabel penilaian mendapat kolom `created_at` dan `updated_at`, serta foreign key yang proper:

```sql
-- Contoh: nilai_formatif (redesigned)
CREATE TABLE nilai_formatif (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tahun_pelajaran_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    kelas_id BIGINT UNSIGNED NOT NULL,
    mapel_id BIGINT UNSIGNED NOT NULL,
    tujuan_pembelajaran_id BIGINT UNSIGNED NOT NULL,
    siswa_id BIGINT UNSIGNED NOT NULL,
    nilai INT NOT NULL DEFAULT 0,
    middle INT NOT NULL DEFAULT 0,
    nas INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (tahun_pelajaran_id) REFERENCES tahun_pelajaran(id),
    FOREIGN KEY (semester_id) REFERENCES semester(id),
    FOREIGN KEY (kelas_id) REFERENCES kelas(id),
    FOREIGN KEY (mapel_id) REFERENCES mapel(id),
    FOREIGN KEY (tujuan_pembelajaran_id) REFERENCES tujuan_pembelajaran(id),
    FOREIGN KEY (siswa_id) REFERENCES siswa(id),
    
    INDEX idx_nilai_formatif_lookup (tahun_pelajaran_id, semester_id, kelas_id, mapel_id),
    INDEX idx_nilai_formatif_siswa (siswa_id)
);
```

#### 3.2.4 Tabel Baru (Tambahan)

| Tabel Baru | Keterangan |
|---|---|
| `password_resets` | Laravel Breeze standard |
| `failed_jobs` | Laravel queue standard |
| `personal_access_tokens` | Laravel Sanctum standard |
| `activity_log` | Audit trail (spatie/laravel-activitylog) |
| `media` | File management (spatie/laravel-medialibrary) |
| `settings` | Key-value settings for sekolah config |

### 3.3 Routing Plan

```php
// routes/web.php

use App\Http\Middleware\EnsureRole;

// === AUTH ===
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// === TATA USAHA (Admin) ===
Route::middleware(['auth', EnsureRole::class.':2'])->prefix('tu')->name('tu.')->group(function () {
    Route::get('/dashboard', [TU\DashboardController::class, 'index'])->name('dashboard');
    
    // Master Data
    Route::resource('sekolah', TU\SekolahController::class)->only(['index', 'update']);
    Route::resource('pegawai', TU\PegawaiController::class);
    Route::resource('kesiswaan', TU\KesiswaanController::class);
    Route::post('kesiswaan/import', [TU\KesiswaanController::class, 'import'])->name('kesiswaan.import');
    Route::get('kesiswaan/export', [TU\KesiswaanController::class, 'export'])->name('kesiswaan.export');
    Route::resource('rombel', TU\RombelController::class);
    Route::resource('kompetensi', TU\KompetensiKeahlianController::class);
    Route::resource('mapel', TU\MapelController::class);
    Route::resource('mapel-kelas', TU\MapelKelasController::class);
    Route::resource('mapel-siswa', TU\MapelSiswaController::class);
    Route::resource('anggota-kelas', TU\AnggotaKelasController::class);
    Route::resource('naik-kelas', TU\NaikKelasController::class);
    
    // Penilaian (TU)
    Route::resource('p5bk', TU\P5bkController::class);
    Route::resource('p5bk.tema', TU\ProyekTemaController::class);
    Route::resource('p5bk.dimensi', TU\DimensiController::class);
    Route::resource('p5bk.elemen', TU\ElemenController::class);
    Route::resource('p5bk.sub-elemen', TU\SubElemenController::class);
    Route::resource('kokurikuler', TU\KokurikulerController::class);
    Route::resource('deskripsi-rapor', TU\DeskripsiRaporController::class);
    
    // Ekstra & Prakerin
    Route::resource('ekstra', TU\EkstraController::class);
    Route::resource('prakerin', TU\PrakerinController::class);
    Route::post('prakerin/import', [TU\PrakerinController::class, 'import'])->name('prakerin.import');
    Route::resource('organisasi', TU\OrganisasiController::class);
    
    // Kehadiran
    Route::resource('piket-harian', TU\PiketHarianController::class);
    Route::get('rekap-presensi', [TU\PresensiController::class, 'rekap'])->name('presensi.rekap');
    
    // Mutasi & Kelulusan
    Route::resource('mutasi-masuk', TU\MutasiMasukController::class);
    Route::resource('mutasi-keluar', TU\MutasiKeluarController::class);
    Route::resource('lulusan', TU\LulusanController::class);
    
    // Pengaturan
    Route::get('pengaturan', [TU\PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('pengaturan', [TU\PengaturanController::class, 'update'])->name('pengaturan.update');
    
    // Laporan
    Route::get('laporan-pendidikan', [TU\LaporanPendidikanController::class, 'index'])->name('laporan-pendidikan');
    Route::get('laporan-wa', [TU\LaporanWaController::class, 'index'])->name('laporan-wa');
    
    // Pengingat
    Route::resource('pengingat', TU\PengingatController::class)->only(['index', 'store', 'destroy']);
});

// === GURU (Teacher) ===
Route::middleware(['auth', EnsureRole::class.':3'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [Guru\DashboardController::class, 'index'])->name('dashboard');
    
    // Kelas Saya
    Route::get('kelas-ku', [Guru\KelasKuController::class, 'index'])->name('kelas-ku');
    Route::get('anggota-kelas/{kelas}', [Guru\AnggotaKelasController::class, 'index'])->name('anggota-kelas');
    
    // Tujuan Pembelajaran
    Route::resource('tujuan-pembelajaran', Guru\TujuanPembelajaranController::class);
    
    // Penilaian
    Route::get('penilaian/{kelas}/{mapel}', [Guru\PenilaianController::class, 'index'])->name('penilaian');
    Route::post('penilaian/formatif', [Guru\PenilaianController::class, 'storeFormatif'])->name('penilaian.formatif');
    Route::post('penilaian/sumatif-ph', [Guru\PenilaianController::class, 'storeSumatifPh'])->name('penilaian.sumatif-ph');
    Route::post('penilaian/sumatif-ts', [Guru\PenilaianController::class, 'storeSumatifTs'])->name('penilaian.sumatif-ts');
    Route::post('penilaian/sumatif-as', [Guru\PenilaianController::class, 'storeSumatifAs'])->name('penilaian.sumatif-as');
    Route::get('lager-nilai-kelas/{kelas}', [Guru\LagerNilaiKelasController::class, 'index'])->name('lager-nilai-kelas');
    
    // Catatan Rapor
    Route::resource('catatan-rapor', Guru\CatatanRaporController::class)->only(['index', 'store']);
    
    // P5BK
    Route::resource('project-kelas', Guru\ProjectKelasController::class);
    Route::resource('p5bk', Guru\P5bkController::class);
    Route::resource('penilaian-profil-pancasila', Guru\PenilaianProfilPancasilaController::class);
    
    // Kokurikuler
    Route::resource('kokurikuler', Guru\KokurikulerController::class);
    Route::resource('penilaian-kokurikuler', Guru\PenilaianKokurikulerController::class);
    
    // Ekstra
    Route::resource('ekstra', Guru\EkstraController::class)->only(['index', 'show']);
    
    // Prakerin
    Route::resource('prakerin', Guru\PrakerinController::class)->only(['index', 'show']);
    Route::get('rapor-pkl/{siswa}', [Guru\RaporPklController::class, 'show'])->name('rapor-pkl');
    
    // Kehadiran
    Route::resource('piket-harian', Guru\PiketHarianController::class)->only(['index']);
    Route::get('rekap-presensi', [Guru\PresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::resource('absensi-bk', Guru\AbsensiBkController::class)->only(['index', 'store']);
    
    // Organisasi
    Route::resource('organisasi', Guru\OrganisasiController::class)->only(['index']);
    
    // Cetak Rapor
    Route::get('cetak-rapor/{kelas}', [Guru\CetakRaporController::class, 'cetak'])->name('cetak-rapor');
});

// RAPOR PDF (accessible by both roles)
Route::middleware('auth')->group(function () {
    Route::get('rapor/pdf/{kelas}/{siswa?}', [RaporController::class, 'pdf'])->name('rapor.pdf');
});

// PWA
Route::get('/api/ping', [PwaController::class, 'ping'])->middleware('auth:sanctum');
```

```php
// routes/api.php
use App\Http\Middleware\EnsureRole;

// WhatsApp Webhook
Route::post('/bot/wa/webhook', [Bot\WhatsAppController::class, 'webhook']);

// Telegram Webhook
Route::post('/bot/tg/webhook', [Bot\TelegramController::class, 'webhook']);

// PWA Heartbeat (Sanctum)
Route::middleware('auth:sanctum')->post('/api/ping', [PwaController::class, 'ping']);
```

### 3.4 Middleware & Policies

```php
// app/Http/Middleware/EnsureRole.php
class EnsureRole {
    public function handle($request, Closure $next, ...$roles) {
        if (!in_array($request->user()->jabatan, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        return $next($request);
    }
}

// app/Policies/ separation
// - SiswaPolicy: viewAny, view, create, update, delete, import, export
// - KelasPolicy: viewAny, view, create, update, delete
// - NilaiPolicy: viewAny, view, create, update (guru hanya kelas sendiri)
// - RaporPolicy: viewAny, view, create, update
```

### 3.5 Key Business Logic Services

#### NilaiService — Kalkulasi Nilai

Mengimplementasikan logika penilaian Kurikulum Merdeka:

```php
class NilaiService {
    /**
     * Hitung Nilai Akhir Mapel (Lager Nilai) — Rapor Semester
     *
     * Formula:
     *   NA = (rata_formatif * 0.40) + (rata_sumatif_ph * 0.30) + (sumatif_as * 0.30)
     *
     * Keterangan:
     *   - rata_formatif   : rata-rata semua nilai formatif per TP (bobot 40%)
     *   - rata_sumatif_ph : rata-rata semua nilai sumatif PH per TP (bobot 30%)
     *   - sumatif_as      : nilai sumatif akhir semester per mapel (bobot 30%)
     *   - Sumatif TS (Tengah Semester) TIDAK masuk ke NA — berdiri sendiri untuk rapor mid
     *
     * Formula Rapor Mid-Semester:
     *   Nilai Mid = Sumatif TS (individu, tanpa bobot gabungan)
     */
    public function hitungNilaiAkhirMapel(int $siswaId, int $mapelKelasId, int $tahun, int $semester): array;
    
    /**
     * Hitung Nilai Akhir PKL (Prakerin)
     *
     * Formula:
     *   Guru input 4 Tujuan Pembelajaran (TP) untuk PKL
     *   Setiap TP memiliki bobot yang sama (25%)
     *   Nilai PKL = (TP1 + TP2 + TP3 + TP4) / 4
     */
    public function hitungNilaiPkl(int $siswaPrakerinId): float;

    /**
     * Generate deskripsi otomatis berdasarkan nilai + template deskripsi_rapor
     */
    public function generateDeskripsi(int $nilai, int $kktp): string;
    
    /**
     * Hitung rata-rata kelas per mapel
     */
    public function hitungRataKelas(int $kelasId, int $mapelId, int $tahun, int $semester): float;
    
    /**
     * Determine predikat (Sangat Baik/Baik/Cukup/Perlu Bimbingan)
     */
    public function getPredikat(int $nilai): string;
}
```

#### RaporService — Cetak Rapor

```php
class RaporService {
    /**
     * Generate PDF rapor semester
     */
    public function generateRaporPdf(int $siswaId, int $tahun, int $semester): \Barryvdh\DomPDF\PDF;
    
    /**
     * Generate PDF rapor mid-semester
     */
    public function generateRaporMidPdf(int $siswaId, int $tahun, int $semester): \Barryvdh\DomPDF\PDF;
    
    /**
     * Generate PDF rapor PKL
     */
    public function generateRaporPklPdf(int $prakerinId): \Barryvdh\DomPDF\PDF;
}
```

---

## 4. Fitur Requirements

### 4.1 FR-AUTH: Autentikasi & Otorisasi

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-AUTH-01 | Login | Form login dengan username/password, CSRF, rate limiting | P0 | ✅ Ada |
| FR-AUTH-02 | Logout | Destruksi session + hapus remember/PWA token | P0 | ✅ Ada |
| FR-AUTH-03 | Remember Me | Cookie-based remember 30 hari | P1 | ✅ Ada |
| FR-AUTH-04 | PWA Auto-login | Token-based persistent login 1 tahun untuk PWA | P1 | ✅ Ada |
| FR-AUTH-05 | Role-based redirect | TU→/tu, Guru→/guru, Kepsek→/guru (akses guru + view rapor semua kelas) | P0 | ✅ Ada |
| FR-AUTH-06 | Session timeout | 2 jam timeout, PWA exempt | P0 | ✅ Ada |
| FR-AUTH-07 | User-agent validation | Cek user agent saat validasi session | P1 | ✅ Ada |
| FR-AUTH-08 | Reset password | Fitur baru: reset password via email/no HP | P2 | ❌ Baru |
| FR-AUTH-09 | Profile update | Edit profil, foto, password | P1 | ❌ Baru |
| FR-AUTH-10 | Activity log | Log login/logout dan perubahan data penting | P2 | ❌ Baru |

### 4.2 FR-TU: Panel Tata Usaha

#### FR-TU-MASTER: Data Master

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-TU-01 | Dashboard | Statistik ringkasan (total siswa, kelas, mapel, guru) | P0 | ✅ Ada |
| FR-TU-02 | Profil Sekolah | CRUD data sekolah (NPSN, logo, visi misi, alamat) | P0 | ✅ Ada |
| FR-TU-03 | Pegawai/Guru | CRUD data pegawai/guru | P0 | ✅ Ada |
| FR-TU-04 | Siswa | CRUD data siswa lengkap (data pribadi + orang tua) | P0 | ✅ Ada |
| FR-TU-05 | Import Siswa | Import data siswa via CSV/Excel | P1 | ✅ Ada |
| FR-TU-06 | Export Siswa | Export data siswa ke Excel | P1 | ✅ Ada |
| FR-TU-07 | Kelas/Rombel | CRUD kelas dengan tingkat + jurusan | P0 | ✅ Ada |
| FR-TU-08 | Anggota Kelas | Assign siswa ke kelas per tahun ajaran | P0 | ✅ Ada |
| FR-TU-09 | Naik Kelas | Proses kenaikan kelas siswa | P0 | ✅ Ada |
| FR-TU-10 | Kompetensi Keahlian | CRUD program keahlian/jurusan | P1 | ✅ Ada |
| FR-TU-11 | Mapel | CRUD mata pelajaran | P0 | ✅ Ada |
| FR-TU-12 | Mapel-Kelas | Assign mapel ke kelas + guru pengajar | P0 | ✅ Ada |
| FR-TU-13 | Mapel-Siswa | Mapel yang diambil siswa | P1 | ✅ Ada |
| FR-TU-14 | Kelompok Mapel | CRUD kelompok mapel (A, B, C) | P1 | ✅ Ada |

#### FR-TU-PENILAIAN: Penilaian (TU)

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-TU-15 | P5BK | Management Projek P5 (tema, dimensi, elemen, sub-elemen) | P0 | ✅ Ada |
| FR-TU-16 | Kokurikuler | Management kegiatan kokurikuler | P1 | ✅ Ada |
| FR-TU-17 | Penilaian Kokurikuler | Penilaian kokurikuler per siswa | P1 | ✅ Ada |
| FR-TU-18 | Deskripsi Rapor | Template deskripsi rapor per predikat | P0 | ✅ Ada |
| FR-TU-19 | Penilaian Profil Pancasila | Assessment dimensi Pancasila per proyek | P1 | ✅ Ada |

#### FR-TU-SUPPORT: Fitur Pendukung

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-TU-20 | Ekstrakurikuler | CRUD eskul + pembina | P1 | ✅ Ada |
| FR-TU-21 | Prakerin | Management prakerin (mitra, tanggal, instruktur) | P1 | ✅ Ada |
| FR-TU-22 | Import Prakerin | Import data prakerin via CSV/Excel | P2 | ✅ Ada |
| FR-TU-23 | Organisasi | Management organisasi siswa | P2 | ✅ Ada |
| FR-TU-24 | Piket Harian | Jadwal piket guru | P2 | ✅ Ada |
| FR-TU-25 | Pengingat | Sistem pengingat jadwal | P2 | ✅ Ada |
| FR-TU-26 | Mutasi Masuk | Penerimaan siswa pindahan | P1 | ✅ Ada |
| FR-TU-27 | Mutasi Keluar | Pemrosesan siswa pindah/keluar | P1 | ✅ Ada |
| FR-TU-28 | Kelulusan | Data kelulusan + lanjutan studi | P1 | ✅ Ada |
| FR-TU-29 | Presensi | Rekap kehadiran siswa (jumlah Sakit/Izin/Alpa per siswa, tampil di rapor) | P1 | ✅ Ada |
| FR-TU-30 | Pengaturan | Setting tahun ajaran, semester, tanggal rapor | P0 | ✅ Ada |

#### FR-TU-LAPORAN: Laporan

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-TU-31 | Laporan Pendidikan | Rekap laporan pendidikan | P2 | ✅ Ada |
| FR-TU-32 | Laporan WA | Riwayat pengiriman WhatsApp | P2 | ✅ Ada |

### 4.3 FR-GURU: Panel Guru

#### FR-GURU-PENILAIAN: Penilaian (Guru)

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-GURU-01 | Dashboard | Statistik kelas yang diwalikan + mapel yang diajar | P0 | ✅ Ada |
| FR-GURU-02 | Kelas-ku | Daftar kelas yang diajar/diwalikan | P0 | ✅ Ada |
| FR-GURU-03 | Anggota Kelas | Daftar siswa per kelas | P0 | ✅ Ada |
| FR-GURU-04 | Tujuan Pembelajaran | CRUD TP per mapel/kelas | P0 | ✅ Ada |
| FR-GURU-05 | Penilaian Formatif | Input nilai formatif per TP per siswa | P0 | ✅ Ada |
| FR-GURU-06 | Penilaian Sumatif PH | Input nilai sumatif PH per TP per siswa | P0 | ✅ Ada |
| FR-GURU-07 | Penilaian Sumatif TS | Input nilai sumatif TS per mapel per siswa | P0 | ✅ Ada |
| FR-GURU-08 | Penilaian Sumatif AS | Input nilai sumatif AS per mapel per siswa | P0 | ✅ Ada |
| FR-GURU-09 | Lager Nilai | Konsolidasi nilai per kelas per mapel | P0 | ✅ Ada |
| FR-GURU-10 | Catatan Rapor | Catatan wali kelas per siswa | P0 | ✅ Ada |

#### FR-GURU-P5: P5 (Projek Penguatan Profil Pancasila)

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-GURU-11 | Project Kelas | CRUD project per kelas | P0 | ✅ Ada |
| FR-GURU-12 | Penilaian P5 | Input assessment P5 per sub-elemen per siswa | P0 | ✅ Ada |
| FR-GURU-13 | Penilaian Profil Pancasila | Assessment dimensi Pancasila | P1 | ✅ Ada |

#### FR-GURU-KOKURIKULER: Kokurikuler

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-GURU-14 | Kokurikuler | Lihat kokurikuler per kelas | P1 | ✅ Ada |
| FR-GURU-15 | Penilaian Kokurikuler | Input nilai kokurikuler per siswa | P1 | ✅ Ada |

#### FR-GURU-SUPPORT: Fitur Pendukung

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-GURU-16 | Ekstra | Daftar eskul yang dibina | P2 | ✅ Ada |
| FR-GURU-17 | Prakerin | Lihat data prakerin | P2 | ✅ Ada |
| FR-GURU-18 | Rapor PKL | Cetak rapor PKL | P1 | ✅ Ada |
| FR-GURU-19 | Presensi | Input presensi harian oleh Guru Piket untuk semua kelas; rekap otomatis tampil di rapor (Sakit/Izin/Alpa) | P1 | ✅ Ada |
| FR-GURU-20 | Absensi BK | Input absensi BK | P2 | ✅ Ada |
| FR-GURU-21 | Piket Harian | Lihat jadwal piket | P2 | ✅ Ada |
| FR-GURU-22 | Organisasi | Lihat data organisasi | P2 | ✅ Ada |

### 4.4 FR-RAPOR: Cetak Rapor

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-RAPOR-01 | Rapor Semester | Cetak rapor semester (PDF) | P0 | ✅ Ada |
| FR-RAPOR-02 | Rapor Mid | Cetak rapor mid-semester (PDF) | P0 | ✅ Ada |
| FR-RAPOR-03 | Rapor PKL | Cetak rapor PKL (PDF) | P1 | ✅ Ada |
| FR-RAPOR-04 | Lager Nilai | Cetak lager nilai (PDF) | P1 | ✅ Ada |
| FR-RAPOR-05 | Export Excel | Export nilai ke Excel | P1 | ✅ Ada |

### 4.5 FR-BOT: Integrasi Bot

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-BOT-01 | WhatsApp Notification | Broadcast nilai/rapor via WA (Fonnte API) | P2 | ✅ Ada |
| FR-BOT-02 | WhatsApp Webhook | Terima pesan masuk WA | P2 | ✅ Ada |
| FR-BOT-03 | Telegram Bot | Kirim notifikasi via Telegram | P2 | ✅ Ada |
| FR-BOT-04 | Telegram Webhook | Terima command via Telegram | P2 | ✅ Ada |

### 4.6 FR-PWA: Progressive Web App

| ID | Fitur | Deskripsi | Prioritas | Status Lama |
|---|---|---|---|---|
| FR-PWA-01 | Service Worker | Cache-first untuk assets, network-first untuk halaman | P1 | ✅ Ada |
| FR-PWA-02 | Manifest | PWA manifest dengan icon 192/512 | P1 | ✅ Ada |
| FR-PWA-03 | Offline Page | Halaman fallback offline | P2 | ✅ Ada |
| FR-PWA-04 | Heartbeat | Ping API setiap 30 menit untuk maintain session | P1 | ✅ Ada |
| FR-PWA-05 | Install Prompt | Banner install PWA | P2 | ✅ Ada |
| FR-PWA-06 | Auto-login | PWA persistent login via Sanctum token | P1 | ✅ Ada |

### 4.7 NFR: Non-Functional Requirements

| ID | Kategori | Deskripsi | Target |
|---|---|---|---|
| NFR-01 | Performance | Halaman dashboard ≤ 2 detik load time | < 2s |
| NFR-02 | Performance | Input nilai batch (40 siswa) ≤ 3 detik | < 3s |
| NFR-03 | Performance | PDF rapor generation ≤ 5 detik per siswa | < 5s |
| NFR-04 | Security | Sembab credentials di `.env`, tidak di source code | 0 hardcode |
| NFR-05 | Security | CSRF protection di semua form | 100% |
| NFR-06 | Security | Rate limiting login: 5 attempts / 15 menit | 5/15min |
| NFR-07 | Security | XSS prevention via Blade auto-escaping | 100% |
| NFR-08 | Security | SQL injection prevention via Eloquent ORM | 100% |
| NFR-09 | Reliability | Error logging ke storage/logs | 100% |
| NFR-10 | Usability | Mobile-responsive (Bootstrap → Tailwind) | 100% |
| NFR-11 | Maintainability | Test coverage minimal 60% untuk services | 60% |
| NFR-12 | Compatibility | Chrome 90+, Firefox 88+, Safari 14+ | Modern browsers |
| NFR-13 | Compatibility | MySQL 8.0+ / MariaDB 10.6+ | Supported |
| NFR-14 | Availability | Uptime ≥ 99% untuk semester aktif | 99% |

---

## 5. Database Migration Plan

### 5.1 Pendekatan

Database akan **di-redesign dari awal** menggunakan Laravel migrations. Semua tabel baru akan mengikuti konvensi penamaan Laravel (`snake_case`, `id` sebagai PK, `{model}_id` sebagai FK).

### 5.2 Mapping Tabel Lama → Baru

| Tabel Lama | Tabel Baru | Perubahan |
|---|---|---|
| `users` | `users` | +`email`, +`remember_token` (Laravel), +`created_at/updated_at/deleted_at`, −`pass`, −`username` (→ Laravel default) |
| `siswa` | `siswa` | −`username`, −`pass`, −`password`, +`created_at/updated_at/deleted_at` |
| `kelas` | `kelas` | `id_tingkat` → `tingkat_id`, `id_kompetensi_keahlian` → `kompetensi_keahlian_id`, +timestamps |
| `mapel` | `mapel` | `id_kelompok` → `kelompok_mapel_id`, +timestamps |
| `nilai_formatif` | `nilai_formatif` | FK naming convention, +timestamps |
| *...dan seterusnya* | | |

### 5.3 Data Migration Strategy

Setelah schema Laravel siap, data dari database lama akan dimigrasikan menggunakan seeder khusus:

```php
// database/seeders/DataMigrationSeeder.php
class DataMigrationSeeder extends Seeder {
    public function run() {
        // Koneksi ke database lama
        $oldDb = DB::connection('mysql_old');
        
        // Migrasi per tabel dengan mapping
        $this->migrateUsers($oldDb);
        $this->migrateSiswa($oldDb);
        $this->migrateKelas($oldDb);
        // ...dst
        
        // Rehash password lama jika perlu
        // (password_hash sudah compatible, jadi tidak perlu rehash)
    }
}
```

Tambahkan koneksi `mysql_old` di `config/database.php` untuk mengakses DB lama.

---

## 6. UI/UX Design

### 6.1 Layout

Mempertahankan layout yang mirip dengan sistem lama:
- **Sidebar kiri** — navigasi menu
- **Topbar atas** — user info, tahun ajaran selector, logout
- **Content area** — konten halaman

### 6.2 Framework UI

- **Tailwind CSS** (menggantikan Bootstrap 4 custom)
- **Alpine.js** untuk interaksi client-side (menggantikan jQuery)
- **Livewire 3** untuk komponen dinamis (form, tabel, modals)
- **SweetAlert2** dipertahankan untuk konfirmasi

### 6.3 Komponen UI Baru

| Komponen | Deskripsi |
|---|---|
| `x-sidebar-tu` | Sidebar Tata Usaha (menu navigasi) |
| `x-sidebar-guru` | Sidebar Guru (menu navigasi) |
| `x-topbar` | Top bar dengan dropdown user & tahun ajaran |
| `x-flash-message` | Alert sukses/error dari session flash |
| `x-nilai-table` | Tabel input nilai (formatif/sumatif) |
| `x-data-table` | DataTables-compatible Livewire table |
| `x-select2` | Dropdown with search (Livewire + Alpine) |
| `x-modal` | Modal dialog (Livewire) |
| `x-confirm-delete` | Konfirmasi hapus (SweetAlert2) |

---

## 7. Phased Implementation Plan

### Fase 0: Foundation (Minggu 1–2)

| Task | Detail | Estimasi |
|---|---|---|
| F0-01 | Setup Laravel 13 project (`laravel new e-raporkm`) | 0.5 hari |
| F0-02 | Install dependencies: Breeze (Livewire), DomPDF, Excel, Sanctum, ActivityLog | 0.5 hari |
| F0-03 | Configure `.env` (DB, app name, timezone) | 0.5 hari |
| F0-04 | Setup Tailwind CSS + purge config | 0.5 hari |
| F0-05 | Create all migrations (55+ tabel redesigned) | 3 hari |
| F0-06 | Create all Eloquent models with relationships | 2 hari |
| F0-07 | Create seeders (reference data: agama, jabatan, dll) | 1 hari |
| F0-08 | Setup authentication (Breeze + role redirect) | 1 hari |
| F0-09 | Create middleware (EnsureRole) + register | 0.5 hari |
| F0-10 | Create base layouts (TU sidebar, Guru sidebar, topbar) | 2 hari |
| F0-11 | Create Shared Blade components (flash, modal, etc) | 1 hari |

**Deliverables**: Project Laravel yang bisa jalan, login/logout berfungsi, base layout siap.

### Fase 1: Authentication & TU Dashboard (Minggu 3–4)

| Task | Detail | Estimasi |
|---|---|---|
| F1-01 | Auth: Login page dengan role redirect | 1 hari |
| F1-02 | Auth: Logout + session management | 0.5 hari |
| F1-03 | Auth: Remember me (Laravel built-in) | 0.5 hari |
| F1-04 | Auth: PWA token (Sanctum) | 1 hari |
| F1-05 | Auth: Rate limiting (Laravel throttle) | 0.5 hari |
| F1-06 | TU Dashboard: Statistik ringkasan | 1 hari |
| F1-07 | TU Sekolah: CRUD profil sekolah | 1 hari |
| F1-08 | TU Pegawai: CRUD guru/pegawai + form request validation | 2 hari |
| F1-09 | TU Pengaturan: Setting tahun ajaran + semester + tanggal rapor | 1 hari |
| F1-10 | TU Deskripsi Rapor: Template deskripsi | 1 hari |

**Deliverables**: Login/logout works, TU dashboard + data master sekolah/pegawai.

### Fase 2: Manajemen Siswa & Kelas (Minggu 5–8)

| Task | Detail | Estimasi |
|---|---|---|
| F2-01 | TU Siswa: CRUD + search + pagination (Livewire) | 3 hari |
| F2-02 | TU Siswa: Import CSV/Excel | 1.5 hari |
| F2-03 | TU Siswa: Export Excel | 1 hari |
| F2-04 | TU Kelas/Rombel: CRUD + assign siswa | 2 hari |
| F2-05 | TU Anggota Kelas: Assign siswa ke kelas | 1.5 hari |
| F2-06 | TU Mapel: CRUD mata pelajaran | 1.5 hari |
| F2-07 | TU Mapel-Kelas: Assign mapel ke kelas + guru | 2 hari |
| F2-08 | TU Mapel-Siswa: Mapel per siswa | 1.5 hari |
| F2-09 | TU Kompetensi Keahlian: CRUD jurusan | 1 hari |
| F2-10 | TU Naik Kelas: Proses kenaikan kelas | 2 hari |
| F2-11 | TU Kelompok Mapel: CRUD kelompok | 0.5 hari |
| F2-12 | TU Tingkat: CRUD tingkat + fase | 0.5 hari |

**Deliverables**: Full CRUD untuk data master siswa dan kelas.

### Fase 3: Panel TU — Fitur Lengkap (Minggu 9–12)

| Task | Detail | Estimasi |
|---|---|---|
| F3-01 | TU Ekstrakurikuler: CRUD + pembina | 2 hari |
| F3-02 | TU Prakerin: CRUD + import | 2 hari |
| F3-03 | TU Organisasi: CRUD organisasi siswa | 1.5 hari |
| F3-04 | TU P5BK: Tema, Dimensi, Elemen, Sub-elemen | 3 hari |
| F3-05 | TU P5BK: Project management (proyek kelas) | 2 hari |
| F3-06 | TU Kokurikuler: CRUD + penilaian | 2 hari |
| F3-07 | TU Penilaian Profil Pancasila | 2 hari |
| F3-08 | TU Mutasi Masuk + Keluar | 2 hari |
| F3-09 | TU Kelulusan | 1.5 hari |
| F3-10 | TU Presensi: Rekap kehadiran | 2 hari |
| F3-11 | TU Piket Harian | 1 hari |
| F3-12 | TU Pengingat | 0.5 hari |
| F3-13 | TU Prestasi Siswa | 1 hari |
| F3-14 | TU Buku Induk | 1.5 hari |
| F3-15 | TU Laporan Pendidikan | 2 hari |

**Deliverables**: Panel TU 100% fitur lengkap.

### Fase 4: Panel Guru — Penilaian (Minggu 13–16)

| Task | Detail | Estimasi |
|---|---|---|
| F4-01 | Guru Dashboard: Statistik kelas | 1.5 hari |
| F4-02 | Guru Kelas-ku: Daftar kelas | 1 hari |
| F4-03 | Guru Anggota Kelas: Daftar siswa | 1 hari |
| F4-04 | Guru Tujuan Pembelajaran: CRUD TP | 2 hari |
| F4-05 | Guru Penilaian: Input formatif per TP per siswa | 3 hari |
| F4-06 | Guru Penilaian: Input sumatif PH per TP per siswa | 2 hari |
| F4-07 | Guru Penilaian: Input sumatif TS per mapel | 1.5 hari |
| F4-08 | Guru Penilaian: Input sumatif AS per mapel | 1.5 hari |
| F4-09 | Guru Lager Nilai: Konsolidasi nilai per kelas per mapel | 2 hari |
| F4-10 | Guru Catatan Rapor: Catatan wali per siswa | 1.5 hari |
| F4-11 | NilaiService: Kalkulasi otomatis nilai akhir | 2 hari |
| F4-12 | Guru P5BK: Project kelas + assessment | 3 hari |
| F4-13 | Guru Kokurikuler: Penilaian | 1.5 hari |
| F4-14 | Guru Penilaian Profil Pancasila | 1.5 hari |
| F4-15 | Guru Ekstra | 1 hari |
| F4-16 | Guru Prakerin + Rapor PKL | 2 hari |
| F4-17 | Guru Presensi | 1.5 hari |
| F4-18 | Guru Absensi BK | 1 hari |
| F4-19 | Guru Piket Harian | 0.5 hari |
| F4-20 | Guru Organisasi | 0.5 hari |

**Deliverables**: Panel Guru 100% fitur lengkap.

### Fase 5: Rapor & Cetak (Minggu 17–18)

| Task | Detail | Estimasi |
|---|---|---|
| F5-01 | RaporService: Logic generation rapor | 2 hari |
| F5-02 | Rapor Semester PDF (full rapor) | 3 hari |
| F5-03 | Rapor Mid-Semester PDF | 2 hari |
| F5-04 | Rapor PKL PDF | 1.5 hari |
| F5-05 | Lager Nilai PDF | 1.5 hari |
| F5-06 | Export Excel (nilai, presensi, data siswa) | 2 hari |
| F5-07 | Laporan Pendidikan | 1.5 hari |

**Deliverables**: Semua rapor dan laporan bisa dicetak/diexport.

### Fase 6: Bot & PWA (Minggu 19)

| Task | Detail | Estimasi |
|---|---|---|
| F6-01 | WhatsApp Notification Channel (Fonnte API) | 2 hari |
| F6-02 | Telegram Notification Channel | 1.5 hari |
| F6-03 | PWA: manifest.json + icons | 0.5 hari |
| F6-04 | PWA: Service Worker (cache strategies) | 1 hari |
| F6-05 | PWA: Auto-login via Sanctum | 1 hari |
| F6-06 | PWA: Heartbeat ping | 0.5 hari |
| F6-07 | Webhook handlers (WA + TG) | 1.5 hari |

**Deliverables**: Notifikasi WA/TG + PWA berfungsi.

### Fase 7: Data Migration & Testing (Minggu 20–22)

| Task | Detail | Estimasi |
|---|---|---|
| F7-01 | DataMigrationSeeder: Transfer data dari DB lama | 3 hari |
| F7-02 | Password compatibility check | 0.5 hari |
| F7-03 | Unit tests: Services (NilaiService, RaporService) | 3 hari |
| F7-04 | Feature tests: Auth, CRUD, penilaian | 5 hari |
| F7-05 | Browser tests: Critical flows | 2 hari |
| F7-06 | Performance testing (load test) | 1 hari |
| F7-07 | Security review (OWASP Top 10) | 1 hari |
| F7-08 | UAT with stakeholders | 3 hari |
| F7-09 | Bug fixes | 2 hari |

**Deliverables**: Sistem siap production, semua data termigrasi, testing lulus.

### Fase 8: Deploy & Handover (Minggu 22–23)

| Task | Detail | Estimasi |
|---|---|---|
| F8-01 | Server setup (PHP 8.4, MySQL 8, Nginx via aaPanel) di `km.smkan.sch.id` | 1 hari |
| F8-02 | Deployment (Git pull + artisan migrate + storage link) | 1 hari |
| F8-03 | DNS switchover ke `km.smkan.sch.id` | 0.5 hari |
| F8-04 | Smoke test production | 1 hari |
| F8-05 | Documentation (user manual) | 2 hari |
| F8-06 | Training session untuk TU & Guru | 1 hari |

**Deliverables**: Sistem live di production.

---

## 8. Migration Mapping — Old Routes → New Routes

### 8.1 Panel TU

| Route Lama (`?pages=`) | Route Baru Laravel | Method |
|---|---|---|
| `profil` | `GET /tu/sekolah` + `PUT /tu/sekolah` | index/update |
| `pegawai` | `GET /tu/pegawai` (CRUD) | resource |
| `kesiswaan` | `GET /tu/kesiswaan` (CRUD) | resource |
| `rombel` | `GET /tu/rombel` (CRUD) | resource |
| `mapel` | `GET /tu/mapel` (CRUD) | resource |
| `ekstra` | `GET /tu/ekstra` (CRUD) | resource |
| `anggota-kelas` | `GET /tu/anggota-kelas/{kelas}` | show |
| `mapel-kelas` | `GET /tu/mapel-kelas` (CRUD) | resource |
| `mapel-siswa` | `GET /tu/mapel-siswa` | index |
| `naik-kelas` | `GET /tu/naik-kelas` | index/store |
| `prakerin` | `GET /tu/prakerin` (CRUD) | resource |
| `p5bk` | `GET /tu/p5bk` (CRUD) | resource |
| `kokurikuler` | `GET /tu/kokurikuler` | index |
| `penilaian-kokurikuler` | `GET /tu/kokurikuler/{id}/penilaian` | show/store |
| `deskripsi-rapor` | `GET /tu/deskripsi-rapor` | index/store |
| `pengaturan` | `GET /tu/pengaturan` + `PUT` | index/update |
| `mutasi-masuk` | `GET /tu/mutasi-masuk` (CRUD) | resource |
| `mutasi-keluar` | `GET /tu/mutasi-keluar` (CRUD) | resource |
| `lulusan` | `GET /tu/lulusan` (CRUD) | resource |
| `kesiswaan-upload` | `POST /tu/kesiswaan/import` | store |
| `piket-harian` | `GET /tu/piket-harian` | index/store |
| `pengingat` | `GET /tu/pengingat` | index/store/destroy |
| `laporan_wa` | `GET /tu/laporan-wa` | index |
| `laporan-pendidikan` | `GET /tu/laporan-pendidikan` | index |

### 8.2 Panel Guru

| Route Lama (`?pages=`) | Route Baru Laravel | Method |
|---|---|---|
| `kelas-ku` | `GET /guru/kelas-ku` | index |
| `anggota-kelas` | `GET /guru/anggota-kelas/{kelas}` | show |
| `tujuan-pembelajaran` | `GET /guru/tujuan-pembelajaran` (CRUD) | resource |
| `penilaian` | `GET /guru/penilaian/{kelas}/{mapel}` | show/store |
| `lager-nilai-kelas` | `GET /guru/lager-nilai-kelas/{kelas}` | show |
| `catatan-rapor` | `GET /guru/catatan-rapor` | index/store |
| `project-kelas` | `GET /guru/project-kelas` (CRUD) | resource |
| `p5bk` | `GET /guru/p5bk` (CRUD) | resource |
| `kokurikuler` | `GET /guru/kokurikuler` | index |
| `penilaian-kokurikuler` | `GET /guru/penilaian-kokurikuler` | index/store |
| `penilaian-profil-pancasila` | `GET /guru/penilaian-profil-pancasila` | index/store |
| `ekstra` | `GET /guru/ekstra` | index/show |
| `prakerin` | `GET /guru/prakerin` | index/show |
| `rapor-pkl` | `GET /guru/rapor-pkl/{siswa}` | show |
| `rekap-presensi` | `GET /guru/rekap-presensi` | index |
| `absensi-bk` | `GET /guru/absensi-bk` | index/store |
| `piket-harian` | `GET /guru/piket-harian` | index |
| `organisasi` | `GET /guru/organisasi` | index |

---

## 9. risk Register

| Risk | Impact | Probability | Mitigation |
|---|---|---|---|
| Database migration data loss | Tinggi | Rendah | Full backup, dry-run migration, data validation script |
| Logic penilaian berbeda dari sistem lama | Tinggi | Sedang | Document semua formula, UAT dengan guru |
| Perubahan requirement selama migrasi | Sedang | Tinggi | Iterative approach, perubahan didokumentasikan dan disetujui developer sebelum diimplementasikan |
| Performance issue di input nilai batch | Sedang | Rendah | Livewire real-time validation, batch insert |
| PWA compatibility di device lama | Rendah | Sedang | Graceful degradation, test di device target |
| Bot API changes (Fonnte/Telegram) | Sedang | Rendah | Abstraksi via Notification Channel, swap implementation |
| Password hashing compatibility | Tinggi | Rendah | Sistem lama sudah pakai `password_hash()`, compatible dengan Laravel |

---

## 10. Success Criteria

| Kriteria | Target |
|---|---|
| Semua fitur P0 berfungsi | 100% |
| Semua data termigrasikan tanpa kehilangan | 100% |
| Response time dashboard | < 2 detik |
| Response time input nilai batch (40 siswa) | < 3 detik |
| Rapor PDF generation | < 5 detik/siswa |
| Uptime semester aktif | ≥ 99% |
| Zero hardcoded credentials | 0 |
| Test coverage (services) | ≥ 60% |
| Guru & TU able to use system without training > 1 jam | Ya |

---

---

## 12. Formula Penilaian Lengkap

### 12.1 Rapor Semester (Nilai Akhir Mapel)

Nilai Akhir Mapel dihitung dari tiga komponen dengan bobot tetap:

| Komponen | Bobot | Keterangan |
|---|---|---|
| Rata-rata Formatif | 40% | Rata-rata semua nilai formatif per Tujuan Pembelajaran |
| Rata-rata Sumatif PH | 30% | Rata-rata semua nilai Sumatif Penilaian Harian per TP |
| Sumatif AS | 30% | Nilai Sumatif Akhir Semester per mapel |

**Formula:**
```
NA = (rata_formatif × 0.40) + (rata_sumatif_ph × 0.30) + (sumatif_as × 0.30)
```

Berlaku untuk **semua mapel reguler** tanpa pengecualian bobot.

### 12.2 Rapor Mid-Semester (Sumatif TS)

Nilai Mid-Semester menggunakan **Sumatif Tengah Semester (TS) secara individu** — tidak digabungkan dengan komponen lain. Tidak ada bobot tambahan.

```
Nilai Mid = Sumatif TS
```

### 12.3 Rapor PKL (Prakerin)

Guru input **4 Tujuan Pembelajaran** untuk setiap siswa yang menjalani PKL. Setiap TP memiliki bobot yang sama.

**Formula:**
```
Nilai PKL = (TP1 + TP2 + TP3 + TP4) / 4
```

### 12.4 Predikat Nilai

| Rentang | Predikat |
|---|---|
| ≥ 90 | Sangat Baik |
| 75 – 89 | Baik |
| 60 – 74 | Cukup |
| < 60 | Perlu Bimbingan |

> **Catatan**: Rentang predikat di atas mengikuti sistem lama. Konfirmasi ulang dengan TU jika ada perubahan kebijakan sekolah.

---

## 13. Role & Akses Pengguna

| Jabatan | Kode | Akses |
|---|---|---|
| Tata Usaha | 2 | Panel `/tu` — semua fitur master data, pengaturan, laporan |
| Guru | 3 | Panel `/guru` — kelas sendiri, input penilaian, cetak rapor |
| Kepala Sekolah | 4 | Panel `/guru` — akses setara Guru + nama/TTD muncul di semua rapor cetak |

**Catatan Kepala Sekolah:**
- Login menggunakan akun user biasa dengan `jabatan = 4`
- Dapat berperan sebagai Guru (mengajar mapel tertentu) sekaligus sebagai Kepala Sekolah
- Nama Kepala Sekolah ditarik dari tabel `kepala_sekolah` berdasarkan tahun pelajaran aktif untuk dicetak di rapor

---

## 14. Deployment & Infrastruktur

### 14.1 Server Production

| Aspek | Detail |
|---|---|
| Domain | `km.smkan.sch.id` |
| Panel Server | aaPanel |
| Web Server | Nginx |
| PHP | 8.4+ |
| Database | MySQL 8.x / MariaDB 10.6+ |
| Storage | Lokal (aaPanel file manager) |

### 14.2 Strategi Parallel-Run

Sistem lama dan sistem baru akan berjalan **paralel selama ± beberapa bulan** setelah go-live. Selama periode ini:

- Sistem lama tetap aktif sebagai **fallback**
- Data entry dilakukan di sistem baru
- Sistem lama hanya diakses jika ada masalah kritis di sistem baru
- Keputusan untuk mematikan sistem lama diambil oleh **developer (Aminu Bil Huda, S.Kom)** setelah yakin tidak ada isu

### 14.3 Rollback Plan

Jika setelah go-live ditemukan bug kritis yang tidak bisa di-hotfix dalam 1 hari kerja:

1. DNS dikembalikan ke sistem lama
2. Bug diinvestigasi dan diperbaiki di environment development
3. Fix di-deploy ulang setelah testing
4. Switchover diulang

**Backup**: Full database backup dilakukan sebelum setiap deployment dan setiap malam via aaPanel scheduled backup.

### 14.4 Out of Scope

Fitur berikut **tidak termasuk** dalam migrasi ini:

| Fitur | Alasan |
|---|---|
| PPDB / Rekrutmen | Tidak dimigrasikan — scope terpisah jika dibutuhkan |
| Voting | Tidak dimigrasikan — fitur non-inti |
| Login Siswa | Tidak diimplementasikan saat ini — jika dibutuhkan di masa depan, gunakan `jabatan = 5` di tabel `users` |

---

## 15. Glossary

| Istilah | Penjelasan |
|---|---|
| **TU** | Tata Usaha (admin sekolah, jabatan=2) |
| **Guru** | Wali kelas/pengajar (jabatan=3) |
| **Kepsek** | Kepala Sekolah (jabatan=4, akses setara Guru) |
| **KM** | Kurikulum Merdeka |
| **Rapor** | Laporan Hasil Belajar |
| **TP** | Tujuan Pembelajaran |
| **KKTP** | Kriteria Ketercapaian Tujuan Pembelajaran |
| **PH** | Penilaian Harian (Sumatif PH) |
| **TS** | Tengah Semester (Sumatif TS) |
| **AS** | Akhir Semester (Sumatif AS) |
| **P5** | Projek Penguatan Profil Pelajar Pancasila |
| **Lager** | Konsolidasi/ledger nilai |
| **Rombel** | Rombongan Belajar (kelas) |
| **Prakerin** | Praktik Kerja Industri |
| **PKL** | Praktik Kerja Lapangan |
| **Eskul** | Ekstrakurikuler |
| **NPSN** | Nomor Pokok Sekolah Nasional |
| **NISN** | Nomor Induk Siswa Nasional |
| **NIS** | Nomor Induk Siswa |
| **NUPTK** | Nomor Unik Pendidik dan Tenaga Kependidikan |

---

*Akhir dokumen PRD*