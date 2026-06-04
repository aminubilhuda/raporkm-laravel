<?php

namespace Database\Seeders;

use App\Models\CatatanWali;
use App\Models\DeskripsiKokurikuler;
use App\Models\Dimensi;
use App\Models\DimensiKokurikuler;
use App\Models\Elemen;
use App\Models\Eskul;
use App\Models\Kelas;
use App\Models\KelasWali;
use App\Models\KelompokMapel;
use App\Models\KompetensiKeahlian;
use App\Models\Lulusan;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\MutasiKeluar;
use App\Models\MutasiMasuk;
use App\Models\NilaiFormatif;
use App\Models\NilaiSumatifAs;
use App\Models\NilaiSumatifPh;
use App\Models\Organisasi as OrganisasiModel;
use App\Models\PembagianRaport;
use App\Models\PembinaEskul;
use App\Models\Pengingat;
use App\Models\PiketHarian;
use App\Models\Prakerin;
use App\Models\Prestasi;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\RefHari;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaEskul;
use App\Models\SiswaKelas;
use App\Models\SiswaPrakerin;
use App\Models\SubElemen;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    private TahunPelajaran $tahunAktif;

    private Semester $semesterAktif;

    public function run(): void
    {
        DB::transaction(function () {
            $this->tahunAktif = TahunPelajaran::where('status', 1)->firstOrFail();
            $this->semesterAktif = Semester::where('status', 1)->firstOrFail();

            $this->command->info('▶ Memulai seeding demo data...');

            $sekolah = $this->seedSekolah();
            $allGuru = $this->seedGuruTambahan();
            $mapels = $this->seedMapel();
            $kelasList = $this->seedKelas();
            $siswas = $this->seedSiswa($kelasList);
            $this->seedKelasWali($kelasList, $allGuru);
            $mapelKelasList = $this->seedMapelKelas($kelasList, $mapels, $allGuru);
            $this->seedEskul($allGuru, $siswas);
            $this->seedP5bkStructure();
            $this->seedProyekTema();
            $this->seedProyekKelas($kelasList);
            $this->seedPrakerin($siswas);
            $this->seedPiketHarian($allGuru);
            $this->seedMutasi($siswas, $kelasList);
            $this->seedLulusan($kelasList);
            $this->seedPrestasi($siswas);
            $this->seedPengingat();
            $this->seedOrganisasi();
            $this->seedCatatanWali($kelasList, $allGuru);
            $this->seedNilaiDetail($mapelKelasList);
            $this->seedPresensi($siswas, $kelasList);
            $this->seedPembagianRaport();
            $this->updateSekolahAktif($sekolah);

            $this->command->info('✅ Demo data selesai!');
            $this->command->info('   - 1 sekolah aktif (tahun 2025/2026 Genap)');
            $this->command->info('   - '.User::where('jabatan', 3)->count().' guru (default guru + guru01-10)');
            $this->command->info('   - '.Siswa::count().' siswa, '.Kelas::count().' kelas');
            $this->command->info('   - '.Mapel::count().' mapel, '.MapelKelas::count().' mapel-kelas assignments');
            $this->command->info('   - '.NilaiFormatif::count().' nilai formatif, '.NilaiSumatifPh::count().' PH, '.NilaiSumatifAs::count().' AS');
            $this->command->info('   - '.DB::table('nilai_mapel')->count().' nilai akhir (auto-aggregated)');
            $this->command->info('   - '.DB::table('presensi')->count().' presensi records');
            $this->command->info('   - '.Lulusan::count().' lulusan, '.Prestasi::count().' prestasi');
            $this->command->info('   - '.Prakerin::count().' prakerin, '.SiswaPrakerin::count().' peserta');
            $this->command->info('Login:');
            $this->command->info('   TU    : admin / password');
            $this->command->info('   Guru  : guru / password');
            $this->command->info('   Guru1 : guru01 / password');
        });
    }

    private function seedSekolah(): Sekolah
    {
        return Sekolah::factory()->create([
            'npsn' => '20501001',
            'nama_sekolah' => 'SMK Negeri 1 Maju Bersama',
            'alamat' => 'Jl. Pendidikan No. 1, Kota Maju Bersama',
            'email' => 'info@smkn1majubersama.sch.id',
            'kontak' => '(021) 1234567',
            'kabupaten' => 'Kabupaten Maju Bersama',
            'provinsi' => 'Jawa Barat',
            'visi' => 'Menjadi SMK unggul yang menghasilkan lulusan berkarakter, kompeten, dan berdaya saing global.',
            'misi' => '1. Menyelenggarakan pendidikan berkualitas.\n2. Mengembangkan karakter peserta didik.\n3. Meningkatkan kompetensi kejuruan sesuai kebutuhan industri.\n4. Menjalin kemitraan dengan dunia usaha dan industri.',
            'tahun_aktif' => $this->tahunAktif->id,
            'semester_aktif' => $this->semesterAktif->id,
        ]);
    }

    private function seedGuruTambahan(): Collection
    {
        $guruBaru = User::factory()->guru()->count(10)->create();

        return $guruBaru->each(function (User $guru, int $i) {
            $guru->update([
                'username' => 'guru'.str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'email' => 'guru'.str_pad($i + 1, 2, '0', STR_PAD_LEFT).'@smkn1majubersama.sch.id',
            ]);
        });
    }

    private function seedMapel(): Collection
    {
        $kelompokA = KelompokMapel::where('nama', 'like', 'Kelompok A%')->first();
        $kelompokB = KelompokMapel::where('nama', 'like', 'Kelompok B%')->first();
        $kelompokC = KelompokMapel::where('nama', 'like', 'Kelompok C%')->first();

        $mapelData = [
            ['kode' => 'MTK', 'nama_mapel' => 'Matematika', 'kelompok_mapel_id' => $kelompokA->id, 'kkm' => 75],
            ['kode' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'kelompok_mapel_id' => $kelompokA->id, 'kkm' => 75],
            ['kode' => 'BIG', 'nama_mapel' => 'Bahasa Inggris', 'kelompok_mapel_id' => $kelompokA->id, 'kkm' => 75],
            ['kode' => 'PKN', 'nama_mapel' => 'Pendidikan Pancasila', 'kelompok_mapel_id' => $kelompokA->id, 'kkm' => 75],
            ['kode' => 'PJK', 'nama_mapel' => 'Pendidikan Jasmani', 'kelompok_mapel_id' => $kelompokB->id, 'kkm' => 75],
            ['kode' => 'SBD', 'nama_mapel' => 'Seni Budaya', 'kelompok_mapel_id' => $kelompokB->id, 'kkm' => 75],
            ['kode' => 'PWP', 'nama_mapel' => 'Pemrograman Web', 'kelompok_mapel_id' => $kelompokC->id, 'kkm' => 80],
            ['kode' => 'BSD', 'nama_mapel' => 'Basis Data', 'kelompok_mapel_id' => $kelompokC->id, 'kkm' => 80],
            ['kode' => 'JRK', 'nama_mapel' => 'Jaringan Komputer', 'kelompok_mapel_id' => $kelompokC->id, 'kkm' => 80],
            ['kode' => 'SO', 'nama_mapel' => 'Sistem Operasi', 'kelompok_mapel_id' => $kelompokC->id, 'kkm' => 80],
            ['kode' => 'PBO', 'nama_mapel' => 'Pemrograman Berorientasi Objek', 'kelompok_mapel_id' => $kelompokC->id, 'kkm' => 80],
            ['kode' => 'PKK', 'nama_mapel' => 'Produk Kreatif dan Kewirausahaan', 'kelompok_mapel_id' => $kelompokC->id, 'kkm' => 75],
        ];

        $mapels = collect();
        foreach ($mapelData as $data) {
            $mapels->push(Mapel::create($data));
        }

        return $mapels;
    }

    private function seedKelas(): Collection
    {
        $tingkats = Tingkat::orderBy('urutan')->get();
        $jurusans = KompetensiKeahlian::all();

        $kelasList = collect();
        foreach ($tingkats as $tingkat) {
            foreach ($jurusans as $jurusan) {
                $kelasList->push(Kelas::factory()->create([
                    'tingkat_id' => $tingkat->id,
                    'kompetensi_keahlian_id' => $jurusan->id,
                    'nama_kelas' => $tingkat->nama.' '.$jurusan->singkatan.' 1',
                    'tahun_pelajaran_id' => $this->tahunAktif->id,
                    'semester_id' => $this->semesterAktif->id,
                ]));
            }
        }

        return $kelasList;
    }

    private function seedSiswa(Collection $kelasList): Collection
    {
        $siswas = Siswa::factory()->count(60)->create(['aktif' => 1]);

        foreach ($siswas as $i => $siswa) {
            $kelas = $kelasList[$i % $kelasList->count()];
            SiswaKelas::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelas->id,
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
                'status' => 'aktif',
            ]);
        }

        return $siswas;
    }

    private function seedKelasWali(Collection $kelasList, Collection $allGuru): void
    {
        $guruWithKelas = User::whereIn('jabatan', [3, 4])->get();

        foreach ($kelasList as $i => $kelas) {
            $wali = $guruWithKelas[$i % $guruWithKelas->count()];
            KelasWali::create([
                'kelas_id' => $kelas->id,
                'user_id' => $wali->id,
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
            ]);
        }
    }

    private function seedMapelKelas(Collection $kelasList, Collection $mapels, Collection $allGuru): Collection
    {
        $mapelNasional = $mapels->take(6);
        $mapelKelasList = collect();

        foreach ($kelasList as $kelas) {
            foreach ($mapelNasional as $mapel) {
                $guru = $allGuru->random();
                $mapelKelasList->push(MapelKelas::create([
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $kelas->id,
                    'user_id' => $guru->id,
                    'tahun_pelajaran_id' => $this->tahunAktif->id,
                    'semester_id' => $this->semesterAktif->id,
                    'kkm' => $mapel->kkm,
                ]));
            }
        }

        return $mapelKelasList;
    }

    private function seedEskul(Collection $allGuru, Collection $siswas): void
    {
        $sekolah = Sekolah::first();
        $eskulNames = [
            'Pramuka' => 'Wajib untuk kelas X',
            'Paskibraka' => 'Ekstrakurikuler paskibraka sekolah',
            'PMR' => 'Palang Merah Remaja',
            'Basket' => 'Ekstrakurikuler bola basket',
        ];

        foreach ($eskulNames as $nama => $keterangan) {
            $eskul = Eskul::create([
                'sekolah_id' => $sekolah->id,
                'nama_eskul' => $nama,
                'keterangan' => $keterangan,
            ]);

            PembinaEskul::create([
                'eskul_id' => $eskul->id,
                'user_id' => $allGuru->random()->id,
                'tahun_pelajaran_id' => $this->tahunAktif->id,
            ]);

            $sampleSiswa = $siswas->random(3);
            foreach ($sampleSiswa as $siswa) {
                SiswaEskul::create([
                    'siswa_id' => $siswa->id,
                    'eskul_id' => $eskul->id,
                    'tahun_pelajaran_id' => $this->tahunAktif->id,
                    'predikat' => fake()->randomElement(['SB', 'B', 'C']),
                    'keterangan' => fake()->sentence(),
                ]);
            }
        }
    }

    private function seedP5bkStructure(): void
    {
        $dimensis = Dimensi::all();
        $elemenData = [
            'Beriman, Bertakwa kepada Tuhan Yang Maha Esa, dan Berakhlak Mulia' => [
                ['nama' => 'Akhlak terhadap diri sendiri', 'urutan' => 1],
                ['nama' => 'Akhlak terhadap manusia', 'urutan' => 2],
                ['nama' => 'Akhlak terhadap alam dan lingkungan', 'urutan' => 3],
            ],
            'Berkebhinekaan Global' => [
                ['nama' => 'Mengenal dan menghargai budaya', 'urutan' => 1],
                ['nama' => 'Komunikasi antar budaya', 'urutan' => 2],
                ['nama' => 'Refleksi dan tanggung jawab', 'urutan' => 3],
            ],
            'Bergotong Royong' => [
                ['nama' => 'Kolaborasi', 'urutan' => 1],
                ['nama' => 'Kepedulian', 'urutan' => 2],
                ['nama' => 'Berbagi', 'urutan' => 3],
            ],
            'Mandiri' => [
                ['nama' => 'Pemahaman diri', 'urutan' => 1],
                ['nama' => 'Regulasi diri', 'urutan' => 2],
                ['nama' => 'Pengembangan diri', 'urutan' => 3],
            ],
            'Bernalar Kritis' => [
                ['nama' => 'Memperoleh dan memproses informasi', 'urutan' => 1],
                ['nama' => 'Menganalisis dan mengevaluasi', 'urutan' => 2],
                ['nama' => 'Merefleksi dan mengambil keputusan', 'urutan' => 3],
            ],
            'Kreatif' => [
                ['nama' => 'Menghasilkan karya orisinal', 'urutan' => 1],
                ['nama' => 'Memiliki keluwesan berpikir', 'urutan' => 2],
                ['nama' => 'Memiliki inisiatif', 'urutan' => 3],
            ],
        ];

        foreach ($dimensis as $dimensi) {
            $items = $elemenData[$dimensi->nama] ?? [];
            foreach ($items as $item) {
                $elemen = Elemen::create([
                    'dimensi_id' => $dimensi->id,
                    'nama' => $item['nama'],
                    'urutan' => $item['urutan'],
                ]);
                SubElemen::create([
                    'elemen_id' => $elemen->id,
                    'nama' => $item['nama'].' (Pengenalan)',
                    'urutan' => 1,
                ]);
                SubElemen::create([
                    'elemen_id' => $elemen->id,
                    'nama' => $item['nama'].' (Penerapan)',
                    'urutan' => 2,
                ]);
            }
        }

        $dimensiKokurikulers = DimensiKokurikuler::all();
        $deskripsiTemplate = [
            'Nilai Karakter' => [
                ['predikat' => 'SB', 'deskripsi' => 'Siswa menunjukkan karakter sangat baik, jujur, disiplin, dan bertanggung jawab.'],
                ['predikat' => 'B', 'deskripsi' => 'Siswa menunjukkan karakter baik dan mulai berkembang dalam aspek kejujuran.'],
            ],
            'Nilai Kedisiplinan' => [
                ['predikat' => 'SB', 'deskripsi' => 'Sangat disiplin dalam kehadiran, waktu, dan penyelesaian tugas.'],
                ['predikat' => 'B', 'deskripsi' => 'Disiplin dalam kehadiran, namun masih perlu peningkatan dalam ketepatan waktu.'],
            ],
            'Nilai Tanggung Jawab' => [
                ['predikat' => 'SB', 'deskripsi' => 'Menunjukkan tanggung jawab tinggi terhadap tugas dan lingkungan.'],
                ['predikat' => 'B', 'deskripsi' => 'Bertanggung jawab terhadap tugas, namun perlu peningkatan pada inisiatif.'],
            ],
        ];

        foreach ($dimensiKokurikulers as $dk) {
            $items = $deskripsiTemplate[$dk->nama] ?? [];
            foreach ($items as $item) {
                DeskripsiKokurikuler::create([
                    'dimensi_kokurikuler_id' => $dk->id,
                    'predikat' => $item['predikat'],
                    'deskripsi' => $item['deskripsi'],
                ]);
            }
        }
    }

    private function seedProyekTema(): void
    {
        $temaData = [
            ['nama_tema' => 'Gaya Hidup Berkelanjutan', 'keterangan' => 'Tema tentang pelestarian lingkungan dan keberlanjutan sumber daya.'],
            ['nama_tema' => 'Bhinneka Tunggal Ika', 'keterangan' => 'Tema tentang keberagaman budaya Indonesia.'],
            ['nama_tema' => 'Bangunlah Jiwa dan Ragimu', 'keterangan' => 'Tema tentang kesehatan jasmani dan rohani.'],
            ['nama_tema' => 'Suara Demokrasi', 'keterangan' => 'Tema tentang kehidupan demokratis di Indonesia.'],
            ['nama_tema' => 'Kearifan Lokal', 'keterangan' => 'Tema tentang kearifan lokal daerah.'],
            ['nama_tema' => 'Rekayasa Teknologi', 'keterangan' => 'Tema tentang inovasi dan rekayasa teknologi.'],
        ];

        foreach ($temaData as $data) {
            ProyekTema::create(array_merge($data, [
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
            ]));
        }
    }

    private function seedProyekKelas(Collection $kelasList): void
    {
        $temas = ProyekTema::all();
        $gurus = User::where('jabatan', 3)->get();

        foreach ($kelasList as $kelas) {
            $tema = $temas->random();
            $guru = $gurus->random();
            ProyekKelas::create([
                'kelas_id' => $kelas->id,
                'proyek_tema_id' => $tema->id,
                'user_id' => $guru->id,
                'judul' => 'Proyek '.$tema->nama_tema.' - '.$kelas->nama_kelas,
                'deskripsi' => fake()->paragraph(),
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
            ]);
        }
    }

    private function seedPrakerin(Collection $siswas): void
    {
        $perusahaan = [
            ['nama_perusahaan' => 'PT Telkom Indonesia', 'PIC' => 'Budi Santoso', 'kontak' => '021-12345678', 'alamat' => 'Jl. Japati No. 1, Bandung'],
            ['nama_perusahaan' => 'PT Bank Mandiri', 'PIC' => 'Siti Aminah', 'kontak' => '021-98765432', 'alamat' => 'Jl. Gatot Subroto Kav. 36-38, Jakarta'],
            ['nama_perusahaan' => 'PT Gojek Indonesia', 'PIC' => 'Andi Wijaya', 'kontak' => '021-55556666', 'alamat' => 'Jl. Pasar Minggu, Jakarta Selatan'],
            ['nama_perusahaan' => 'PT Tokopedia', 'PIC' => 'Rina Kartika', 'kontak' => '021-77778888', 'alamat' => 'Jl. Prof. Dr. Satrio Kav. 11, Jakarta'],
        ];

        foreach ($perusahaan as $p) {
            $prakerin = Prakerin::create(array_merge($p, [
                'tanggal_mulai' => now()->subMonths(3)->format('Y-m-d'),
                'tanggal_selesai' => now()->addWeeks(2)->format('Y-m-d'),
                'keterangan' => 'Prakerin reguler semester genap',
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
            ]));
            $prakerin = $prakerin; // keep var referenced

            $sampleSiswa = $siswas->random(3);
            $kelasSample = $sampleSiswa->map(function ($siswa) {
                return SiswaKelas::where('siswa_id', $siswa->id)
                    ->where('tahun_pelajaran_id', $this->tahunAktif->id)
                    ->where('semester_id', $this->semesterAktif->id)
                    ->first();
            })->filter();

            foreach ($sampleSiswa as $i => $siswa) {
                $sk = $kelasSample->values()[$i] ?? null;
                if (! $sk) {
                    continue;
                }
                $guru = User::where('jabatan', 3)->inRandomOrder()->first();
                SiswaPrakerin::create([
                    'prakerin_id' => $prakerin->id,
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $sk->kelas_id,
                    'user_id' => $guru->id,
                    'status' => 'aktif',
                    'tahun_pelajaran_id' => $this->tahunAktif->id,
                    'semester_id' => $this->semesterAktif->id,
                ]);
            }
        }
    }

    private function seedPiketHarian(Collection $allGuru): void
    {
        $hari = RefHari::orderBy('urutan')->get();
        $guruList = $allGuru->merge(User::where('jabatan', 3)->whereNotIn('id', $allGuru->pluck('id'))->get());

        foreach ($hari as $i => $h) {
            $guru = $guruList[$i % $guruList->count()];
            PiketHarian::create([
                'user_id' => $guru->id,
                'hari_id' => $h->id,
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
            ]);
        }
    }

    private function seedMutasi(Collection $siswas, Collection $kelasList): void
    {
        $mutasiMasuk = $siswas->random(2);
        foreach ($mutasiMasuk as $siswa) {
            MutasiMasuk::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelasList->random()->id,
                'asal_sekolah' => 'SMP Negeri '.fake()->numberBetween(1, 50),
                'tanggal_masuk' => now()->subMonths(2)->format('Y-m-d'),
                'alasan' => 'Pindahan dari sekolah lain',
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
            ]);
        }

        $mutasiKeluar = $siswas->random(2);
        foreach ($mutasiKeluar as $siswa) {
            $sk = SiswaKelas::where('siswa_id', $siswa->id)
                ->where('tahun_pelajaran_id', $this->tahunAktif->id)
                ->where('semester_id', $this->semesterAktif->id)
                ->first();
            MutasiKeluar::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $sk?->kelas_id ?? $kelasList->random()->id,
                'tujuan_sekolah' => 'SMA Negeri '.fake()->numberBetween(1, 50),
                'tanggal_keluar' => now()->subMonth()->format('Y-m-d'),
                'alasan' => 'Pindah domisili',
                'jenis_keluar_id' => 1,
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
            ]);
            $siswa->update(['aktif' => 0]);
        }
    }

    private function seedLulusan(Collection $kelasList): void
    {
        $kelas12 = $kelasList->filter(fn (Kelas $k) => $k->tingkat->angka === 12);

        $siswaKelas12Ids = SiswaKelas::whereIn('kelas_id', $kelas12->pluck('id'))
            ->where('tahun_pelajaran_id', $this->tahunAktif->id)
            ->where('semester_id', $this->semesterAktif->id)
            ->where('status', 'aktif')
            ->get();

        $counter = 1;
        foreach ($siswaKelas12Ids as $sk) {
            Lulusan::create([
                'siswa_id' => $sk->siswa_id,
                'kelas_id' => $sk->kelas_id,
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'tanggal_lulus' => '2026-06-15',
                'no_ijazah' => 'IJZ-2026/'.str_pad($counter, 4, '0', STR_PAD_LEFT),
                'lanjut_ke' => fake()->randomElement(['Kuliah', 'Kerja', 'SMK', 'SMA']),
                'keterangan' => 'Lulusan tahun ajaran 2025/2026',
            ]);
            $counter++;
        }
    }

    private function seedPrestasi(Collection $siswas): void
    {
        $sampleSiswa = $siswas->random(6);
        $templates = [
            'Juara 1 Olimpiade Matematika',
            'Juara 2 Lomba Web Design',
            'Juara 1 Kompetisi Jaringan Komputer',
            'Juara 3 Lomba Debat Bahasa Inggris',
            'Juara 1 Lomba Karya Tulis Ilmiah',
            'Juara 2 Kompetisi Robotik',
        ];

        $levels = ['Sekolah', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional'];

        foreach ($sampleSiswa as $i => $siswa) {
            Prestasi::create([
                'siswa_id' => $siswa->id,
                'nama_prestasi' => $templates[$i % count($templates)],
                'tingkat' => $levels[array_rand($levels)],
                'penyelenggara' => 'Dinas Pendidikan '.fake()->word(),
                'tahun' => 2026,
                'keterangan' => 'Prestasi semester genap',
            ]);
        }
    }

    private function seedPengingat(): void
    {
        $data = [
            ['judul' => 'Rapat Guru Bulanan', 'pesan' => 'Rapat koordinasi guru pada hari Jumat pukul 13.00 WIB di ruang meeting.', 'untuk_role' => 3, 'tanggal' => '2026-06-15'],
            ['judul' => 'Batas Input Nilai Sumatif', 'pesan' => 'Batas akhir input nilai sumatif akhir semester.', 'untuk_role' => 3, 'tanggal' => '2026-06-20'],
            ['judul' => 'Pembagian Rapor Semester', 'pesan' => 'Pembagian rapor semester genap kepada orang tua murid.', 'untuk_role' => 4, 'tanggal' => '2026-06-25'],
            ['judul' => 'Libur Hari Raya Idul Adha', 'pesan' => 'Sekolah libur selama 3 hari dalam rangka Hari Raya Idul Adha.', 'untuk_role' => 2, 'tanggal' => '2026-06-27'],
        ];

        foreach ($data as $d) {
            Pengingat::create($d);
        }
    }

    private function seedOrganisasi(): void
    {
        $data = [
            ['nama_organisasi' => 'OSIS', 'keterangan' => 'Organisasi Siswa Intra Sekolah'],
            ['nama_organisasi' => 'Pramuka (Intra)', 'keterangan' => 'Gerakan Pramuka Gugus Depan'],
            ['nama_organisasi' => 'Rohis', 'keterangan' => 'Rohani Islam sekolah'],
        ];

        foreach ($data as $d) {
            OrganisasiModel::create($d);
        }
    }

    private function seedCatatanWali(Collection $kelasList, Collection $allGuru): void
    {
        $samples = SiswaKelas::where('tahun_pelajaran_id', $this->tahunAktif->id)
            ->where('semester_id', $this->semesterAktif->id)
            ->inRandomOrder()
            ->take(30)
            ->get();

        $templates = [
            'Ananda {nama} menunjukkan perkembangan yang sangat baik dalam akademik dan non-akademik selama semester ini.',
            'Ananda {nama} perlu meningkatkan kedisiplinan dan kehadiran agar lebih optimal dalam belajar.',
            'Ananda {nama} aktif dalam kegiatan kelas dan menunjukkan kepemimpinan yang baik.',
            'Ananda {nama} memiliki potensi akademik yang baik, perlu terus ditingkatkan prestasinya.',
        ];

        $counter = 0;
        foreach ($samples as $sk) {
            $siswa = $sk->siswa;
            $template = $templates[$counter % count($templates)];
            CatatanWali::create([
                'siswa_id' => $sk->siswa_id,
                'kelas_id' => $sk->kelas_id,
                'user_id' => $allGuru->random()->id,
                'tahun_pelajaran_id' => $this->tahunAktif->id,
                'semester_id' => $this->semesterAktif->id,
                'catatan' => str_replace('{nama}', $siswa->nama_siswa, $template),
            ]);
            $counter++;
        }
    }

    private function seedNilaiDetail(Collection $mapelKelasList): void
    {
        foreach ($mapelKelasList as $mk) {
            $tpList = collect();
            for ($i = 1; $i <= 3; $i++) {
                $tpList->push(TujuanPembelajaran::create([
                    'mapel_id' => $mk->mapel_id,
                    'kelas_id' => $mk->kelas_id,
                    'kode_tp' => $mk->mapel->kode.'-TP-'.$mk->kelas_id.'-'.$i,
                    'nama_tp' => 'TP '.($i).': '.fake()->sentence(5),
                    'tahun_pelajaran_id' => $this->tahunAktif->id,
                    'semester_id' => $this->semesterAktif->id,
                ]));
            }

            $siswaIds = SiswaKelas::where('kelas_id', $mk->kelas_id)
                ->where('tahun_pelajaran_id', $this->tahunAktif->id)
                ->where('semester_id', $this->semesterAktif->id)
                ->pluck('siswa_id');

            foreach ($siswaIds as $siswaId) {
                $baseScore = fake()->numberBetween(60, 95);

                foreach ($tpList as $tp) {
                    NilaiFormatif::create([
                        'tahun_pelajaran_id' => $this->tahunAktif->id,
                        'semester_id' => $this->semesterAktif->id,
                        'kelas_id' => $mk->kelas_id,
                        'mapel_id' => $mk->mapel_id,
                        'tujuan_pembelajaran_id' => $tp->id,
                        'siswa_id' => $siswaId,
                        'nilai' => max(50, min(100, $baseScore + fake()->numberBetween(-10, 10))),
                        'middle' => max(50, min(100, $baseScore + fake()->numberBetween(-8, 8))),
                        'nas' => max(50, min(100, $baseScore + fake()->numberBetween(-5, 5))),
                    ]);

                    NilaiSumatifPh::create([
                        'tahun_pelajaran_id' => $this->tahunAktif->id,
                        'semester_id' => $this->semesterAktif->id,
                        'kelas_id' => $mk->kelas_id,
                        'mapel_id' => $mk->mapel_id,
                        'tujuan_pembelajaran_id' => $tp->id,
                        'siswa_id' => $siswaId,
                        'nilai' => max(50, min(100, $baseScore + fake()->numberBetween(-10, 10))),
                        'deskripsi' => fake()->sentence(),
                    ]);
                }

                NilaiSumatifAs::create([
                    'tahun_pelajaran_id' => $this->tahunAktif->id,
                    'semester_id' => $this->semesterAktif->id,
                    'kelas_id' => $mk->kelas_id,
                    'mapel_id' => $mk->mapel_id,
                    'siswa_id' => $siswaId,
                    'nilai' => max(50, min(100, $baseScore + fake()->numberBetween(-5, 5))),
                    'deskripsi' => fake()->sentence(),
                ]);
            }
        }
    }

    private function seedPresensi(Collection $siswas, Collection $kelasList): void
    {
        $sampleSiswa = $siswas->random(10);
        $batch = [];

        foreach ($sampleSiswa as $siswa) {
            $sk = SiswaKelas::where('siswa_id', $siswa->id)
                ->where('tahun_pelajaran_id', $this->tahunAktif->id)
                ->where('semester_id', $this->semesterAktif->id)
                ->first();
            if (! $sk) {
                continue;
            }

            for ($i = 60; $i > 0; $i--) {
                $tanggal = now()->subDays($i);
                if ($tanggal->isSunday()) {
                    continue;
                }

                $rand = fake()->numberBetween(1, 100);
                $jenisAbsen = match (true) {
                    $rand <= 90 => 1,
                    $rand <= 94 => 2,
                    $rand <= 97 => 3,
                    default => 4,
                };

                $batch[] = [
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $sk->kelas_id,
                    'jenis_absen_id' => $jenisAbsen,
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'tahun_pelajaran_id' => $this->tahunAktif->id,
                    'semester_id' => $this->semesterAktif->id,
                    'keterangan' => $jenisAbsen > 1 ? fake()->optional(0.5)->sentence() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($batch, 100) as $chunk) {
            DB::table('presensi')->insert($chunk);
        }
    }

    private function seedPembagianRaport(): void
    {
        PembagianRaport::create([
            'tahun_pelajaran_id' => $this->tahunAktif->id,
            'semester_id' => $this->semesterAktif->id,
            'tanggal_mid' => '2026-03-15',
            'tanggal_semester' => '2026-06-25',
        ]);
    }

    private function updateSekolahAktif(Sekolah $sekolah): void
    {
        // Already set during creation, but ensure consistency
        $sekolah->update([
            'tahun_aktif' => $this->tahunAktif->id,
            'semester_aktif' => $this->semesterAktif->id,
        ]);
    }
}
