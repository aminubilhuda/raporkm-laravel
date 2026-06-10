<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\KelompokMapel;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\Ptk;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DapodikService
{
    private function config(string $key, mixed $default = null): mixed
    {
        return DB::table('settings')->where('key', $key)->value('value') ?? $default;
    }

    private function httpGet(string $endpoint): array
    {
        $url = rtrim((string) $this->config('dapodik_url'), '/');
        $npsn = (string) $this->config('dapodik_npsn');
        $token = (string) $this->config('dapodik_token');

        if ($url === '' || $npsn === '' || $token === '') {
            throw new \Exception('Konfigurasi Dapodik belum lengkap. Silakan isi URL, NPSN, dan Token di halaman Pengaturan Dapodik.');
        }

        $response = Http::withToken($token)
            ->timeout(60)
            ->get("{$url}/{$endpoint}?npsn={$npsn}");

        if (! $response->successful()) {
            throw new \Exception("HTTP {$response->status()}: {$response->body()}");
        }

        $json = $response->json();

        return $this->extractRows($json);
    }

    private function extractRows(?array $json): array
    {
        $rows = $json['rows'] ?? [];

        if (is_array($rows)) {
            $isAssoc = array_keys($rows) !== range(0, count($rows) - 1);

            return $isAssoc ? [$rows] : $rows;
        }

        return [];
    }

    public function syncSekolahan(): array
    {
        $data = $this->httpGet('getSekolah');

        if (empty($data)) {
            return ['success' => 0, 'message' => 'Data kosong.'];
        }

        $item = (array) $data[0];
        $sekolah = Sekolah::first() ?? new Sekolah;

        $sekolah->fill([
            'dapodik_id' => $item['sekolah_id'] ?? null,
            'npsn' => $item['npsn'] ?? $sekolah->npsn,
            'nama_sekolah' => $item['nama'] ?? $item['nama_sekolah'] ?? '-',
            'id_jenjang' => isset($item['bentuk_pendidikan_id']) ? (int) $item['bentuk_pendidikan_id'] : null,
            'bentuk_sekolah' => $item['bentuk_pendidikan_id_str'] ?? null,
            'alamat' => $item['alamat_jalan'] ?? $item['alamat'] ?? null,
            'desa' => $item['desa_kelurahan'] ?? $item['desa'] ?? null,
            'kecamatan' => $item['kecamatan'] ?? null,
            'kabupaten' => $item['kabupaten_kota'] ?? $item['kabupaten'] ?? null,
            'provinsi' => $item['provinsi'] ?? null,
            'email' => $item['email'] ?? null,
            'kontak' => $item['nomor_telepon'] ?? $item['kontak'] ?? null,
            'website' => $item['website'] ?? null,
        ])->save();

        $tahun = TahunPelajaran::where('status', 1)->first();
        $semester = Semester::where('status', 1)->first();
        if ($tahun && ! $sekolah->tahun_aktif) {
            $sekolah->tahun_aktif = $tahun->id;
        }
        if ($semester && ! $sekolah->semester_aktif) {
            $sekolah->semester_aktif = $semester->id;
        }
        if ($sekolah->isDirty()) {
            $sekolah->save();
        }

        $msg = '1 data sekolah berhasil disinkron.';

        return ['success' => 1, 'message' => $msg];
    }

    public function syncPesertaDidik(): array
    {
        $data = $this->httpGet('getPesertaDidik');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $item) {
            $item = (array) $item;
            $nisn = $item['nisn'] ?? null;
            if (! $nisn) {
                $failed++;

                continue;
            }

            $kelamin = match (strtoupper($item['jenis_kelamin'] ?? '')) {
                'L', 'Laki-laki', 'LAKI-LAKI' => 1,
                'P', 'Perempuan', 'PEREMPUAN' => 2,
                default => null,
            };

            try {
                Siswa::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nis' => $item['nis'] ?? $item['nisn'] ?? $nisn,
                        'nama_siswa' => $item['nama'] ?? $item['nama_siswa'] ?? '-',
                        'nik_pd' => $item['nik'] ?? $item['nik_pd'] ?? null,
                        'nkk' => $item['nkk'] ?? null,
                        'dapodik_pd_id' => $item['peserta_didik_id'] ?? null,
                        'tempat_lahir' => $item['tempat_lahir'] ?? null,
                        'tanggal_lahir' => isset($item['tanggal_lahir']) ? date('Y-m-d', strtotime($item['tanggal_lahir'])) : null,
                        'kelamin' => $kelamin,
                        'agama' => isset($item['agama_id']) ? (int) $item['agama_id'] : null,
                        'alamat' => $item['alamat_jalan'] ?? $item['alamat'] ?? null,
                        'kontak_siswa' => $item['nomor_telepon_seluler'] ?? $item['kontak_siswa'] ?? null,
                        'nama_ayah' => $item['nama_ayah'] ?? null,
                        'pekerjaan_ayah' => $item['pekerjaan_ayah_id_str'] ?? null,
                        'nama_ibu' => $item['nama_ibu'] ?? null,
                        'pekerjaan_ibu' => $item['pekerjaan_ibu_id_str'] ?? null,
                        'nik_ayah' => $item['nik_ayah'] ?? null,
                        'nik_ibu' => $item['nik_ibu'] ?? null,
                        'anak_ke' => isset($item['anak_keberapa']) ? (int) $item['anak_keberapa'] : null,
                        'sekolah_asal' => $item['sekolah_asal'] ?? null,
                        'aktif' => 1,
                    ]
                );
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "NISN {$nisn}: {$e->getMessage()}";
            }
        }

        $msg = "{$success} berhasil, {$failed} gagal.";
        if (! empty($errors)) {
            $msg .= ' '.implode('; ', array_slice($errors, 0, 5));
        }

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncRombonganBelajar(): array
    {
        $data = $this->httpGet('getRombonganBelajar');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        $tahunAktif = TahunPelajaran::where('status', 1)->first();
        $semesterAktif = Semester::where('status', 1)->first();

        $first = $data[0] ?? null;
        if ($first && $semesterId = ($first['semester_id'] ?? null)) {
            $tahunAwal = substr((string) $semesterId, 0, 4);
            $tahunAkhir = (int) $tahunAwal + 1;
            $semesterAngka = (int) substr((string) $semesterId, -1);

            if (strlen($tahunAwal) === 4) {
                $tahunLabel = "{$tahunAwal}/{$tahunAkhir}";

                $tahunAktif = TahunPelajaran::firstOrCreate(
                    ['tahun' => $tahunLabel],
                    ['status' => 1]
                );
                $tahunAktif->status = 1;
                $tahunAktif->save();

                $semesterAktif = Semester::firstOrCreate(
                    ['urutan' => $semesterAngka],
                    [
                        'nama' => $semesterAngka === 1 ? 'Ganjil' : 'Genap',
                        'status' => 1,
                    ]
                );
                $semesterAktif->status = 1;
                $semesterAktif->save();

                $sekolah = Sekolah::first();
                if ($sekolah) {
                    $sekolah->tahun_aktif = $tahunAktif->id;
                    $sekolah->semester_aktif = $semesterAktif->id;
                    $sekolah->save();
                }
            }
        }

        foreach ($data as $item) {
            $item = (array) $item;

            try {
                $namaRombel = $item['nama'] ?? null;
                $tingkatRaw = $item['tingkat_pendidikan_id_str'] ?? '';
                $tingkatAngka = (int) filter_var($tingkatRaw, FILTER_SANITIZE_NUMBER_INT);
                $jurusanNama = $item['jurusan_id_str'] ?? null;

                if (! $namaRombel) {
                    $failed++;

                    continue;
                }

                $tingkat = null;
                if ($tingkatAngka) {
                    $tingkat = Tingkat::firstOrCreate(
                        ['angka' => $tingkatAngka],
                        ['nama' => "Kelas {$tingkatAngka}", 'fase' => match ($tingkatAngka) {
                            10 => 'E', 11 => 'F', 12 => 'F', default => 'E'
                        }, 'urutan' => $tingkatAngka]
                    );
                }

                $jurusan = null;
                if ($jurusanNama) {
                    $jurusan = KompetensiKeahlian::firstOrCreate(
                        ['nama' => $jurusanNama],
                        ['singkatan' => strtoupper(Str::substr($jurusanNama, 0, 3))]
                    );
                }

                $kelas = Kelas::firstOrCreate(
                    [
                        'tingkat_id' => $tingkat?->id,
                        'kompetensi_keahlian_id' => $jurusan?->id,
                        'nama_kelas' => $namaRombel,
                    ],
                    [
                        'dapodik_id' => $item['rombongan_belajar_id'] ?? null,
                        'tahun_pelajaran_id' => $tahunAktif?->id,
                        'semester_id' => $semesterAktif?->id,
                    ]
                );

                $anggota = $item['anggota_rombel'] ?? [];
                if (is_array($anggota) && $tahunAktif && $semesterAktif) {
                    foreach ($anggota as $a) {
                        $a = (array) $a;
                        $pdId = $a['peserta_didik_id'] ?? null;
                        if (! $pdId) {
                            continue;
                        }
                        $siswa = Siswa::where('dapodik_pd_id', $pdId)->first();
                        if ($siswa) {
                            SiswaKelas::firstOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'kelas_id' => $kelas->id,
                                    'tahun_pelajaran_id' => $tahunAktif->id,
                                    'semester_id' => $semesterAktif->id,
                                ],
                                ['status' => 'aktif']
                            );

                            if ($jurusan) {
                                $siswa->update(['jurusan' => $jurusan->id]);
                            }
                        }
                    }
                }

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Rombel {$namaRombel}: {$e->getMessage()}";
            }
        }

        $msg = "{$success} rombel berhasil, {$failed} gagal.";
        if (! empty($errors)) {
            $msg .= ' '.implode('; ', array_slice($errors, 0, 5));
        }

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncPengguna(): array
    {
        $data = $this->httpGet('getPengguna');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        // Deduplikasi: jika 1 orang punya 2 pengguna_id (PTK + Kepsek), pilih prioritas tertinggi
        $deduplicated = [];
        foreach ($data as $item) {
            $item = (array) $item;
            $ptkId = $item['ptk_id'] ?? null;
            $roleStr = $item['peran_id_str'] ?? '';

            $priority = match ($roleStr) {
                'Kepala Sekolah', 'Kepsek' => 1,
                'Operator Sekolah', 'Tata Usaha', 'Admin', 'Bendahara BOS' => 2,
                default => 3,
            };

            $key = $ptkId ?: ($item['username'] ?? uniqid());

            if (! isset($deduplicated[$key]) || $priority < $deduplicated[$key]['_priority']) {
                $item['_priority'] = $priority;
                $deduplicated[$key] = $item;
            }
        }

        foreach ($deduplicated as $item) {
            $email = $item['username'] ?? null;
            if (! $email) {
                $failed++;

                continue;
            }

            $username = $email;
            $ptk_id = $item['ptk_id'] ?? null;

            $role = match ($item['peran_id_str'] ?? '') {
                'Operator Sekolah', 'Tata Usaha', 'Admin', 'Bendahara BOS' => 2,
                'Kepala Sekolah', 'Kepsek' => 4,
                default => 3,
            };

            try {
                // Cari user: via gtk.ptk_id -> email -> username -> nama+jabatan
                $existingUser = null;

                if ($ptk_id) {
                    $ptk = Ptk::where('ptk_id', $ptk_id)->first();
                    $existingUser = $ptk?->user;
                }
                if (! $existingUser) {
                    $existingUser = User::where('email', $email)->first();
                }
                if (! $existingUser) {
                    $existingUser = User::where('username', $username)->first();
                }
                if (! $existingUser) {
                    $existingUser = User::whereRaw('LOWER(nama) = ?', [strtolower($item['nama'] ?? '')])
                        ->where('jabatan', $role)
                        ->first();
                }

                if ($existingUser) {
                    // Update user fields saja (bukan GTK fields)
                    $updateData = [];
                    $updateData['username'] = $username;
                    $updateData['email'] = $email;
                    $updateData['jabatan'] = $role;
                    if (! empty($item['no_hp'])) {
                        $updateData['kontak'] = $item['no_hp'];
                    } elseif (! empty($item['no_telepon'])) {
                        $updateData['kontak'] = $item['no_telepon'];
                    }
                    if (($item['nama'] ?? null) && $item['nama'] !== $existingUser->nama) {
                        $updateData['nama'] = $item['nama'];
                    }

                    if (! empty($updateData)) {
                        $updateData['updated_at'] = now();
                        $existingUser->update($updateData);
                    }

                    // Link ptk_id jika user belum punya tapi GTK record ada
                    if (! $existingUser->ptk_id && $ptk_id) {
                        $ptk = Ptk::where('ptk_id', $ptk_id)->first();
                        if ($ptk) {
                            $existingUser->update(['ptk_id' => $ptk->id]);
                        }
                    }
                } else {
                    // Buat user baru
                    $newUser = User::create([
                        'nama' => $item['nama'] ?? '-',
                        'username' => $username,
                        'email' => $email,
                        'password' => bcrypt($username),
                        'jabatan' => $role,
                        'kontak' => $item['no_hp'] ?? $item['no_telepon'] ?? null,
                    ]);

                    // Link ke GTK record jika ada
                    if ($ptk_id) {
                        $ptk = Ptk::where('ptk_id', $ptk_id)->first();
                        if ($ptk) {
                            $newUser->update(['ptk_id' => $ptk->id]);
                        }
                    }
                }

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "User {$username}: {$e->getMessage()}";
            }
        }

        $msg = "{$success} berhasil, {$failed} gagal.";
        if (! empty($errors)) {
            $msg .= ' '.implode('; ', array_slice($errors, 0, 5));
        }

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncGtk(): array
    {
        $data = $this->httpGet('getGtk');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $item) {
            $item = (array) $item;
            $nuptk = $item['nuptk'] ?? null;
            $nik = $item['nik'] ?? null;
            $ptk_id = $item['ptk_id'] ?? null;

            if (! $nuptk && ! $nik && ! $ptk_id) {
                $failed++;

                continue;
            }

            $kelamin = match (strtoupper($item['jenis_kelamin'] ?? '')) {
                'L', 'Laki-laki', 'LAKI-LAKI' => 1,
                'P', 'Perempuan', 'PEREMPUAN' => 2,
                default => null,
            };

            $ptkData = [
                'ptk_id' => $ptk_id,
                'nuptk' => $nuptk,
                'nik' => $nik,
                'nip' => $item['nip'] ?? null,
                'kelamin' => $kelamin,
                'tempat_lahir' => $item['tempat_lahir'] ?? null,
                'tanggal_lahir' => isset($item['tanggal_lahir']) ? date('Y-m-d', strtotime($item['tanggal_lahir'])) : null,
                'agama' => isset($item['agama_id']) ? (int) $item['agama_id'] : null,
                'pendidikan_terakhir' => $item['pendidikan_terakhir'] ?? null,
                'bidang_studi_terakhir' => $item['bidang_studi_terakhir'] ?? null,
                'pangkat_golongan' => $item['pangkat_golongan_terakhir'] ?? null,
                'status_kepegawaian' => $item['status_kepegawaian_id_str'] ?? null,
                'jenis_ptk' => $item['jenis_ptk_id_str'] ?? null,
                'jabatan_ptk' => $item['jabatan_ptk_id_str'] ?? null,
            ];

            $keyField = $nuptk ? 'nuptk' : ($nik ? 'nik' : 'ptk_id');
            $key = $nuptk ?: ($nik ?: $ptk_id);

            try {
                // Cari existing GTK record via ptk_id/nuptk/nik
                $existingPtk = null;
                if ($ptk_id) {
                    $existingPtk = Ptk::where('ptk_id', $ptk_id)->first();
                }
                if (! $existingPtk && $nuptk) {
                    $existingPtk = Ptk::where('nuptk', $nuptk)->first();
                }
                if (! $existingPtk && $nik) {
                    $existingPtk = Ptk::where('nik', $nik)->first();
                }

                if ($existingPtk) {
                    // Update gtk record
                    $existingPtk->update($ptkData);
                } else {
                    // Cari atau buat user, lalu buat gtk record
                    $existingUser = null;
                    if ($ptk_id) {
                        $ptk = Ptk::where('ptk_id', $ptk_id)->first();
                        $existingUser = $ptk?->user;
                    }
                    if (! $existingUser) {
                        $existingUser = User::whereRaw('LOWER(nama) = ?', [strtolower($item['nama'] ?? '')])
                            ->where('jabatan', 3)
                            ->first();
                    }

                    if (! $existingUser) {
                        $username = $nuptk ?? $nik ?? 'gtk-'.uniqid();
                        $existingUser = User::create([
                            'nama' => $item['nama'] ?? '-',
                            'username' => $username,
                            'email' => "{$username}@gtk.e-rapor.sch.id",
                            'password' => bcrypt($username),
                            'jabatan' => 3,
                        ]);
                    }

                    $ptk = Ptk::create(array_merge($ptkData, [
                        'user_id' => $existingUser->id,
                    ]));

                    // Link ptk_id ke user jika belum ada
                    if (! $existingUser->ptk_id) {
                        $existingUser->update(['ptk_id' => $ptk->id]);
                    }
                }

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "{$keyField} {$key}: {$e->getMessage()}";
            }
        }

        $msg = "{$success} berhasil, {$failed} gagal.";
        if (! empty($errors)) {
            $msg .= ' '.implode('; ', array_slice($errors, 0, 5));
        }

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncPembelajaran(): array
    {
        $data = $this->httpGet('getRombonganBelajar');

        if (empty($data)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Data rombel kosong.'];
        }

        $sekolah = Sekolah::first();
        $taId = $sekolah?->tahun_aktif;
        $semesterId = $sekolah?->semester_aktif;
        $success = 0;
        $failed = 0;
        $errors = [];

        if (! $taId) {
            $tp = TahunPelajaran::where('status', 1)->orderBy('id', 'desc')->first();
            $taId = $tp?->id;
        }
        if (! $semesterId) {
            $sem = Semester::where('status', 1)->orderBy('id', 'desc')->first();
            $semesterId = $sem?->id;
        }
        if (! $taId || ! $semesterId) {
            return ['success' => 0, 'failed' => 0, 'message' => 'Tahun pelajaran atau semester belum tersedia.'];
        }

        $kelompokMapel = KelompokMapel::first();
        if (! $kelompokMapel) {
            $kelompokMapel = KelompokMapel::create([
                'nama' => 'Umum',
                'keterangan' => 'Default dari sync Dapodik',
            ]);
        }
        $kelompokMapelId = $kelompokMapel->id;

        foreach ($data as $item) {
            $item = (array) $item;
            $namaRombel = $item['nama'] ?? null;
            $pembelajaran = $item['pembelajaran'] ?? [];

            if (! $namaRombel || ! is_array($pembelajaran)) {
                continue;
            }

            $kelas = Kelas::where('nama_kelas', $namaRombel)
                ->where('tahun_pelajaran_id', $taId)
                ->first();
            if (! $kelas) {
                $failed++;
                $errors[] = "Kelas {$namaRombel} belum di-sync.";

                continue;
            }

            foreach ($pembelajaran as $pel) {
                $pel = (array) $pel;
                $namaMapel = $pel['nama_mata_pelajaran'] ?? $pel['mata_pelajaran_id_str'] ?? null;
                $ptkId = $pel['ptk_id'] ?? null;
                $kodeMapel = isset($pel['mata_pelajaran_id']) ? (string) $pel['mata_pelajaran_id'] : null;

                if (! $namaMapel) {
                    $failed++;

                    continue;
                }

                try {
                    $mapel = Mapel::firstOrCreate(
                        ['nama_mapel' => $namaMapel],
                        [
                            'dapodik_id' => $kodeMapel,
                            'kode' => $kodeMapel,
                            'kelompok_mapel_id' => $kelompokMapelId,
                            'kkm' => 75,
                        ]
                    );

                    $guru = null;
                    if ($ptkId) {
                        $ptk = Ptk::where('ptk_id', $ptkId)->first();
                        $guru = $ptk?->user;
                    }

                    MapelKelas::updateOrCreate(
                        [
                            'mapel_id' => $mapel->id,
                            'kelas_id' => $kelas->id,
                            'tahun_pelajaran_id' => $taId,
                            'semester_id' => $semesterId,
                        ],
                        [
                            'dapodik_id' => $pel['pembelajaran_id'] ?? null,
                            'user_id' => $guru?->id,
                            'kkm' => 75,
                        ]
                    );

                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "{$namaMapel} di {$namaRombel}: {$e->getMessage()}";
                }
            }
        }

        $msg = "{$success} berhasil, {$failed} gagal.";
        if (! empty($errors)) {
            $msg .= ' '.implode('; ', array_slice($errors, 0, 5));
        }

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncAll(): array
    {
        set_time_limit(0);

        $results = [
            'Sekolah' => $this->syncSekolahan(),
            'GTK' => $this->syncGtk(),
            'Pengguna' => $this->syncPengguna(),
            'Peserta Didik' => $this->syncPesertaDidik(),
            'Rombongan Belajar' => $this->syncRombonganBelajar(),
            'Pembelajaran' => $this->syncPembelajaran(),
        ];

        $totalSuccess = 0;
        $totalFailed = 0;
        $lines = [];

        foreach ($results as $label => $r) {
            $totalSuccess += $r['success'] ?? 0;
            $totalFailed += $r['failed'] ?? 0;
            $lines[] = "{$label}: {$r['success']} berhasil";
            if (($r['failed'] ?? 0) > 0) {
                $lines[count($lines) - 1] .= ", {$r['failed']} gagal";
            }
        }

        $msg = implode(' | ', $lines);

        return ['success' => $totalSuccess, 'failed' => $totalFailed, 'message' => $msg];
    }
}
