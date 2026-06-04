<?php

namespace App\Services;

use App\Models\DapodikSyncLog;
use App\Models\Kelas;
use App\Models\KelompokMapel;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
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

    private function log(string $endpoint, string $status, int $count, ?string $message = null): void
    {
        DapodikSyncLog::create([
            'endpoint' => $endpoint,
            'status' => $status,
            'records_count' => $count,
            'message' => $message,
        ]);
    }

    public function syncSekolahan(): array
    {
        $data = $this->httpGet('getSekolah');

        if (empty($data)) {
            $this->log('getSekolah', 'error', 0, 'Data kosong.');

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
        $this->log('getSekolah', 'success', 1, $msg);

        return ['success' => 1, 'message' => $msg];
    }

    public function syncPesertaDidik(): array
    {
        $data = $this->httpGet('getPesertaDidik');

        if (empty($data)) {
            $this->log('getPesertaDidik', 'error', 0, 'Data kosong.');

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
        $this->log('getPesertaDidik', $failed > 0 ? 'error' : 'success', $success, $msg);

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncRombonganBelajar(): array
    {
        $data = $this->httpGet('getRombonganBelajar');

        if (empty($data)) {
            $this->log('getRombonganBelajar', 'error', 0, 'Data kosong.');

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
        $this->log('getRombonganBelajar', $failed > 0 ? 'error' : 'success', $success, $msg);

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncPengguna(): array
    {
        $data = $this->httpGet('getPengguna');

        if (empty($data)) {
            $this->log('getPengguna', 'error', 0, 'Data kosong.');

            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $item) {
            $item = (array) $item;
            $username = $item['username'] ?? null;
            if (! $username) {
                $failed++;

                continue;
            }

            $kelamin = match (strtoupper($item['jenis_kelamin'] ?? '')) {
                'L', 'Laki-laki', 'LAKI-LAKI' => 1,
                'P', 'Perempuan', 'PEREMPUAN' => 2,
                default => null,
            };

            $role = match ($item['peran_id_str'] ?? '') {
                'Operator Sekolah', 'Tata Usaha', 'Admin', 'Bendahara BOS' => 2,
                'Kepala Sekolah', 'Kepsek' => 4,
                default => 3,
            };

            try {
                User::updateOrCreate(
                    ['username' => $username],
                    [
                        'nama' => $item['nama'] ?? $item['nama_lengkap'] ?? '-',
                        'nip' => $item['nip'] ?? null,
                        'password' => bcrypt($username),
                        'email' => $username,
                        'jabatan' => $role,
                        'kelamin' => $kelamin,
                        'kontak' => $item['no_hp'] ?? $item['no_telepon'] ?? null,
                    ]
                );
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
        $this->log('getPengguna', $failed > 0 ? 'error' : 'success', $success, $msg);

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncGtk(): array
    {
        $data = $this->httpGet('getGtk');

        if (empty($data)) {
            $this->log('getGtk', 'error', 0, 'Data kosong.');

            return ['success' => 0, 'failed' => 0, 'message' => 'Data kosong.'];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $item) {
            $item = (array) $item;
            $nuptk = $item['nuptk'] ?? null;
            $nik = $item['nik'] ?? null;
            if (! $nuptk && ! $nik) {
                $failed++;

                continue;
            }

            $kelamin = match (strtoupper($item['jenis_kelamin'] ?? '')) {
                'L', 'Laki-laki', 'LAKI-LAKI' => 1,
                'P', 'Perempuan', 'PEREMPUAN' => 2,
                default => null,
            };

            $existingUser = null;
            if ($nuptk) {
                $existingUser = User::where('nuptk', $nuptk)->first();
            }
            if (! $existingUser && $nik) {
                $existingUser = User::where('nik', $nik)->first();
            }

            $username = $existingUser?->username ?? $nuptk ?? $nik ?? 'user-'.uniqid();
            $email = $existingUser?->email ?? "{$username}@gtk.e-rapor.sch.id";

            $keyField = $nuptk ? 'nuptk' : 'nik';
            $key = $nuptk ?: $nik;

            try {
                User::updateOrCreate(
                    [$keyField => $key],
                    [
                        'nama' => $item['nama'] ?? '-',
                        'nik' => $nik,
                        'nip' => $item['nip'] ?? null,
                        'nuptk' => $nuptk,
                        'ptk_id' => $item['ptk_id'] ?? null,
                        'username' => $username,
                        'password' => $existingUser ? $existingUser->password : bcrypt($username),
                        'email' => $email,
                        'jabatan' => $existingUser?->jabatan ?? 3,
                        'kelamin' => $kelamin,
                        'tempat_lahir' => $item['tempat_lahir'] ?? null,
                        'tanggal_lahir' => isset($item['tanggal_lahir']) ? date('Y-m-d', strtotime($item['tanggal_lahir'])) : null,
                        'agama' => isset($item['agama_id']) ? (int) $item['agama_id'] : null,
                        'pendidikan_terakhir' => $item['pendidikan_terakhir'] ?? null,
                        'bidang_studi_terakhir' => $item['bidang_studi_terakhir'] ?? null,
                        'kontak' => $item['no_hp'] ?? null,
                    ]
                );
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
        $this->log('getGtk', $failed > 0 ? 'error' : 'success', $success, $msg);

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncPembelajaran(): array
    {
        $data = $this->httpGet('getRombonganBelajar');

        if (empty($data)) {
            $this->log('syncPembelajaran', 'error', 0, 'Data rombel kosong.');

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
            $this->log('syncPembelajaran', 'error', 0, 'Tahun pelajaran atau semester belum tersedia. Jalankan sync Rombongan Belajar terlebih dahulu.');

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

                    $guru = $ptkId ? User::where('ptk_id', $ptkId)->first() : null;

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
        $this->log('syncPembelajaran', $failed > 0 ? 'error' : 'success', $success, $msg);

        return ['success' => $success, 'failed' => $failed, 'message' => $msg];
    }

    public function syncAll(): array
    {
        set_time_limit(0);

        $results = [
            'Sekolah' => $this->syncSekolahan(),
            'Peserta Didik' => $this->syncPesertaDidik(),
            'Rombongan Belajar' => $this->syncRombonganBelajar(),
            'Pengguna' => $this->syncPengguna(),
            'GTK' => $this->syncGtk(),
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
