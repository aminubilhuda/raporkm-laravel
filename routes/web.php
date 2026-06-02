<?php

use App\Http\Controllers\Guru\AnggotaKelasController as GuruAnggotaKelasController;
use App\Http\Controllers\Guru\CatatanRaporController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\KelasKuController;
use App\Http\Controllers\Guru\LagerNilaiKelasController;
use App\Http\Controllers\Guru\PenilaianController;
use App\Http\Controllers\Guru\PenilaianKokurikulerController;
use App\Http\Controllers\Guru\ProjectKelasController;
use App\Http\Controllers\Guru\TujuanPembelajaranController;
use App\Http\Controllers\TU\AnggotaKelasController;
use App\Http\Controllers\TU\DashboardController;
use App\Http\Controllers\TU\DeskripsiRaporController;
use App\Http\Controllers\TU\EkstraController;
use App\Http\Controllers\TU\KelompokMapelController;
use App\Http\Controllers\TU\KesiswaanController;
use App\Http\Controllers\TU\KokurikulerController;
use App\Http\Controllers\TU\KompetensiKeahlianController;
use App\Http\Controllers\TU\LaporanController;
use App\Http\Controllers\TU\LulusanController;
use App\Http\Controllers\TU\MapelController;
use App\Http\Controllers\TU\MapelKelasController;
use App\Http\Controllers\TU\MapelSiswaController;
use App\Http\Controllers\TU\MutasiController;
use App\Http\Controllers\TU\NaikKelasController;
use App\Http\Controllers\TU\OrganisasiController;
use App\Http\Controllers\TU\P5bkController;
use App\Http\Controllers\TU\RaporController;
use App\Http\Controllers\TU\EksporController;
use App\Http\Controllers\TU\PegawaiController;
use App\Http\Controllers\TU\PengaturanController;
use App\Http\Controllers\TU\PengingatController;
use App\Http\Controllers\TU\PiketHarianController;
use App\Http\Controllers\TU\PrakerinController;
use App\Http\Controllers\TU\PresensiController;
use App\Http\Controllers\TU\PrestasiController;
use App\Http\Controllers\TU\RombelController;
use App\Http\Controllers\TU\SekolahController;
use App\Http\Controllers\TU\TingkatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

$comingSoon = fn (string $title, string $panel = 'TU') => view('coming-soon', ['title' => $title, 'panel' => $panel]);

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::view('/profile', 'profile')->name('profile');

    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect('/login');
    })->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ((int) $user->jabatan) {
            2 => redirect()->route('tu.dashboard'),
            3, 4 => redirect()->route('guru.dashboard'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');
});

// === Panel Tata Usaha ===
Route::middleware(['auth', 'role:2'])->prefix('tu')->name('tu.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
    Route::put('/sekolah', [SekolahController::class, 'update'])->name('sekolah.update');
    Route::resource('pegawai', PegawaiController::class);
    Route::post('pegawai/{pegawai}/restore', [PegawaiController::class, 'restore'])->withTrashed()->name('pegawai.restore');
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::resource('deskripsi-rapor', DeskripsiRaporController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('tingkat', TingkatController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('kompetensi', KompetensiKeahlianController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('kelompok-mapel', KelompokMapelController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('mapel', MapelController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('kesiswaan', KesiswaanController::class);
    Route::resource('rombel', RombelController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/anggota-kelas', [AnggotaKelasController::class, 'index'])->name('anggota-kelas.index');
    Route::post('/anggota-kelas', [AnggotaKelasController::class, 'store'])->name('anggota-kelas.store');
    Route::delete('/anggota-kelas/{id}', [AnggotaKelasController::class, 'destroy'])->name('anggota-kelas.destroy');
    Route::get('/mapel-kelas', [MapelKelasController::class, 'index'])->name('mapel-kelas.index');
    Route::post('/mapel-kelas', [MapelKelasController::class, 'store'])->name('mapel-kelas.store');
    Route::delete('/mapel-kelas/{id}', [MapelKelasController::class, 'destroy'])->name('mapel-kelas.destroy');
    Route::get('/mapel-siswa', [MapelSiswaController::class, 'index'])->name('mapel-siswa.index');
    Route::post('/mapel-siswa', [MapelSiswaController::class, 'store'])->name('mapel-siswa.store');
    Route::delete('/mapel-siswa/{id}', [MapelSiswaController::class, 'destroy'])->name('mapel-siswa.destroy');
    Route::get('/naik-kelas', [NaikKelasController::class, 'index'])->name('naik-kelas.index');
    Route::post('/naik-kelas/process', [NaikKelasController::class, 'process'])->name('naik-kelas.process');

    Route::get('/p5bk', [P5bkController::class, 'dimensi'])->name('p5bk.index');
    Route::post('/p5bk/dimensi', [P5bkController::class, 'dimensiStore'])->name('p5bk.dimensi.store');
    Route::put('/p5bk/dimensi/{dimensi}', [P5bkController::class, 'dimensiUpdate'])->name('p5bk.dimensi.update');
    Route::delete('/p5bk/dimensi/{dimensi}', [P5bkController::class, 'dimensiDestroy'])->name('p5bk.dimensi.destroy');
    Route::post('/p5bk/elemen', [P5bkController::class, 'elemenStore'])->name('p5bk.elemen.store');
    Route::put('/p5bk/elemen/{elemen}', [P5bkController::class, 'elemenUpdate'])->name('p5bk.elemen.update');
    Route::delete('/p5bk/elemen/{elemen}', [P5bkController::class, 'elemenDestroy'])->name('p5bk.elemen.destroy');
    Route::post('/p5bk/sub', [P5bkController::class, 'subStore'])->name('p5bk.sub.store');
    Route::put('/p5bk/sub/{subElemen}', [P5bkController::class, 'subUpdate'])->name('p5bk.sub.update');
    Route::delete('/p5bk/sub/{subElemen}', [P5bkController::class, 'subDestroy'])->name('p5bk.sub.destroy');
    Route::get('/p5bk/tema', [P5bkController::class, 'tema'])->name('p5bk.tema.index');
    Route::post('/p5bk/tema', [P5bkController::class, 'temaStore'])->name('p5bk.tema.store');
    Route::put('/p5bk/tema/{proyekTema}', [P5bkController::class, 'temaUpdate'])->name('p5bk.tema.update');
    Route::delete('/p5bk/tema/{proyekTema}', [P5bkController::class, 'temaDestroy'])->name('p5bk.tema.destroy');
    Route::get('/p5bk/proyek', [P5bkController::class, 'proyek'])->name('p5bk.proyek.index');
    Route::post('/p5bk/proyek', [P5bkController::class, 'proyekStore'])->name('p5bk.proyek.store');
    Route::put('/p5bk/proyek/{proyekKelas}', [P5bkController::class, 'proyekUpdate'])->name('p5bk.proyek.update');
    Route::delete('/p5bk/proyek/{proyekKelas}', [P5bkController::class, 'proyekDestroy'])->name('p5bk.proyek.destroy');

    Route::get('/kokurikuler', [KokurikulerController::class, 'index'])->name('kokurikuler.index');
    Route::post('/kokurikuler/dimensi', [KokurikulerController::class, 'dimensiStore'])->name('kokurikuler.dimensi.store');
    Route::put('/kokurikuler/dimensi/{dimensiKokurikuler}', [KokurikulerController::class, 'dimensiUpdate'])->name('kokurikuler.dimensi.update');
    Route::delete('/kokurikuler/dimensi/{dimensiKokurikuler}', [KokurikulerController::class, 'dimensiDestroy'])->name('kokurikuler.dimensi.destroy');
    Route::post('/kokurikuler/deskripsi', [KokurikulerController::class, 'deskripsiStore'])->name('kokurikuler.deskripsi.store');
    Route::put('/kokurikuler/deskripsi/{deskripsiKokurikuler}', [KokurikulerController::class, 'deskripsiUpdate'])->name('kokurikuler.deskripsi.update');
    Route::delete('/kokurikuler/deskripsi/{deskripsiKokurikuler}', [KokurikulerController::class, 'deskripsiDestroy'])->name('kokurikuler.deskripsi.destroy');

    Route::get('/ekstra', [EkstraController::class, 'index'])->name('ekstra.index');
    Route::post('/ekstra', [EkstraController::class, 'store'])->name('ekstra.store');
    Route::put('/ekstra/{eskul}', [EkstraController::class, 'update'])->name('ekstra.update');
    Route::delete('/ekstra/{eskul}', [EkstraController::class, 'destroy'])->name('ekstra.destroy');
    Route::post('/ekstra/pembina', [EkstraController::class, 'pembinaStore'])->name('ekstra.pembina.store');
    Route::delete('/ekstra/pembina/{pembinaEskul}', [EkstraController::class, 'pembinaDestroy'])->name('ekstra.pembina.destroy');

    Route::get('/prakerin', [PrakerinController::class, 'index'])->name('prakerin.index');
    Route::post('/prakerin', [PrakerinController::class, 'store'])->name('prakerin.store');
    Route::put('/prakerin/{prakerin}', [PrakerinController::class, 'update'])->name('prakerin.update');
    Route::delete('/prakerin/{prakerin}', [PrakerinController::class, 'destroy'])->name('prakerin.destroy');
    Route::get('/prakerin/peserta', [PrakerinController::class, 'peserta'])->name('prakerin.peserta');
    Route::post('/prakerin/peserta', [PrakerinController::class, 'pesertaStore'])->name('prakerin.peserta.store');
    Route::delete('/prakerin/peserta/{siswaPrakerin}', [PrakerinController::class, 'pesertaDestroy'])->name('prakerin.peserta.destroy');

    Route::get('/organisasi', [OrganisasiController::class, 'index'])->name('organisasi.index');
    Route::post('/organisasi', [OrganisasiController::class, 'store'])->name('organisasi.store');
    Route::put('/organisasi/{organisasi}', [OrganisasiController::class, 'update'])->name('organisasi.update');
    Route::delete('/organisasi/{organisasi}', [OrganisasiController::class, 'destroy'])->name('organisasi.destroy');

    Route::get('/piket-harian', [PiketHarianController::class, 'index'])->name('piket-harian.index');
    Route::post('/piket-harian', [PiketHarianController::class, 'store'])->name('piket-harian.store');
    Route::delete('/piket-harian/{piketHarian}', [PiketHarianController::class, 'destroy'])->name('piket-harian.destroy');

    Route::get('/mutasi-masuk', [MutasiController::class, 'masuk'])->name('mutasi-masuk.index');
    Route::post('/mutasi-masuk', [MutasiController::class, 'masukStore'])->name('mutasi-masuk.store');
    Route::delete('/mutasi-masuk/{mutasiMasuk}', [MutasiController::class, 'masukDestroy'])->name('mutasi-masuk.destroy');

    Route::get('/mutasi-keluar', [MutasiController::class, 'keluar'])->name('mutasi-keluar.index');
    Route::post('/mutasi-keluar', [MutasiController::class, 'keluarStore'])->name('mutasi-keluar.store');
    Route::delete('/mutasi-keluar/{mutasiKeluar}', [MutasiController::class, 'keluarDestroy'])->name('mutasi-keluar.destroy');

    Route::get('/lulusan', [LulusanController::class, 'index'])->name('lulusan.index');
    Route::post('/lulusan', [LulusanController::class, 'store'])->name('lulusan.store');
    Route::put('/lulusan/{lulusan}', [LulusanController::class, 'update'])->name('lulusan.update');
    Route::delete('/lulusan/{lulusan}', [LulusanController::class, 'destroy'])->name('lulusan.destroy');

    Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');
    Route::post('/prestasi', [PrestasiController::class, 'store'])->name('prestasi.store');
    Route::put('/prestasi/{prestasi}', [PrestasiController::class, 'update'])->name('prestasi.update');
    Route::delete('/prestasi/{prestasi}', [PrestasiController::class, 'destroy'])->name('prestasi.destroy');

    Route::get('/pengingat', [PengingatController::class, 'index'])->name('pengingat.index');
    Route::post('/pengingat', [PengingatController::class, 'store'])->name('pengingat.store');
    Route::delete('/pengingat/{pengingat}', [PengingatController::class, 'destroy'])->name('pengingat.destroy');

    Route::get('/rekap-presensi', [PresensiController::class, 'rekap'])->name('presensi.rekap');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan-pendidikan');

    Route::get('/rapor', [RaporController::class, 'pilih'])->name('rapor.pilih');
    Route::get('/rapor/semester/{siswa}/{tahun}/{semester}', [RaporController::class, 'semester'])->name('rapor.semester');
    Route::get('/rapor/mid/{siswa}/{tahun}/{semester}', [RaporController::class, 'mid'])->name('rapor.mid');
    Route::get('/rapor/pkl/{siswaPrakerin}', [RaporController::class, 'pkl'])->name('rapor.pkl');

    Route::get('/ekspor', [EksporController::class, 'index'])->name('ekspor.index');
    Route::get('/laporan/pendidikan', [LaporanController::class, 'pendidikan'])->name('laporan.pendidikan');
    Route::get('/ekspor/nilai', [EksporController::class, 'nilai'])->name('ekspor.nilai');
    Route::get('/ekspor/presensi', [EksporController::class, 'presensi'])->name('ekspor.presensi');
    Route::get('/ekspor/siswa', [EksporController::class, 'siswa'])->name('ekspor.siswa');
});

// === Panel Guru ===
Route::middleware(['auth', 'role:3,4'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');

    Route::get('/kelas-ku', [KelasKuController::class, 'index'])->name('kelas-ku.index');
    Route::get('/anggota-kelas/{kelas?}', [GuruAnggotaKelasController::class, 'index'])->name('anggota-kelas.index');
    Route::get('/tujuan-pembelajaran/{kelas?}/{mapel?}', [TujuanPembelajaranController::class, 'index'])->name('tujuan-pembelajaran.index');
    Route::post('/tujuan-pembelajaran', [TujuanPembelajaranController::class, 'store'])->name('tujuan-pembelajaran.store');
    Route::put('/tujuan-pembelajaran/{tujuanPembelajaran}', [TujuanPembelajaranController::class, 'update'])->name('tujuan-pembelajaran.update');
    Route::delete('/tujuan-pembelajaran/{tujuanPembelajaran}', [TujuanPembelajaranController::class, 'destroy'])->name('tujuan-pembelajaran.destroy');
    Route::get('/penilaian/{kelas?}/{mapel?}', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::post('/penilaian/formatif', [PenilaianController::class, 'storeFormatif'])->name('penilaian.formatif');
    Route::post('/penilaian/sumatif-ph', [PenilaianController::class, 'storeSumatifPh'])->name('penilaian.sumatif-ph');
    Route::post('/penilaian/sumatif-ts', [PenilaianController::class, 'storeSumatifTs'])->name('penilaian.sumatif-ts');
    Route::post('/penilaian/sumatif-as', [PenilaianController::class, 'storeSumatifAs'])->name('penilaian.sumatif-as');
    Route::get('/lager-nilai-kelas/{kelas?}', [LagerNilaiKelasController::class, 'index'])->name('lager-nilai-kelas.index');
    Route::get('/lager-nilai-kelas/{kelas}/{mapel}/pdf', [LagerNilaiKelasController::class, 'exportPdf'])->name('lager-nilai-kelas.pdf');
    Route::get('/catatan-rapor/{kelas?}', [CatatanRaporController::class, 'index'])->name('catatan-rapor.index');
    Route::post('/catatan-rapor', [CatatanRaporController::class, 'store'])->name('catatan-rapor.store');
    Route::get('/project-kelas/{kelas?}', [ProjectKelasController::class, 'index'])->name('project-kelas.index');
    Route::post('/project-kelas', [ProjectKelasController::class, 'store'])->name('project-kelas.store');
    Route::delete('/project-kelas/{proyekKelas}', [ProjectKelasController::class, 'destroy'])->name('project-kelas.destroy');
    Route::get('/project-kelas/{proyekKelas}/penilaian', [ProjectKelasController::class, 'penilaian'])->name('project-kelas.penilaian');
    Route::post('/project-kelas/{proyekKelas}/penilaian', [ProjectKelasController::class, 'storePenilaian'])->name('project-kelas.penilaian.store');
    Route::get('/p5bk', fn () => view('guru.p5bk.index'))->name('p5bk.index');
    Route::get('/penilaian-profil-pancasila', fn () => view('guru.penilaian-profil-pancasila.index'))->name('penilaian-profil-pancasila.index');
    Route::get('/kokurikuler', fn () => view('guru.kokurikuler.index'))->name('kokurikuler.index');
    Route::get('/penilaian-kokurikuler/{kelas?}', [PenilaianKokurikulerController::class, 'index'])->name('penilaian-kokurikuler.index');
    Route::post('/penilaian-kokurikuler', [PenilaianKokurikulerController::class, 'store'])->name('penilaian-kokurikuler.store');
    Route::get('/ekstra', fn () => view('guru.datang-soon', ['title' => 'Ekstrakurikuler']))->name('ekstra.index');
    Route::get('/prakerin', fn () => view('guru.datang-soon', ['title' => 'Prakerin']))->name('prakerin.index');
    Route::get('/rapor-pkl/{siswa?}', fn () => view('guru.datang-soon', ['title' => 'Rapor PKL']))->name('rapor-pkl.index');
    Route::get('/rekap-presensi', fn () => view('guru.datang-soon', ['title' => 'Rekap Presensi']))->name('presensi.rekap');
    Route::get('/absensi-bk', fn () => view('guru.datang-soon', ['title' => 'Absensi BK']))->name('absensi-bk.index');
    Route::get('/piket-harian', fn () => view('guru.datang-soon', ['title' => 'Piket Harian']))->name('piket-harian.index');
    Route::get('/organisasi', fn () => view('guru.datang-soon', ['title' => 'Organisasi']))->name('organisasi.index');
});

require __DIR__.'/auth.php';
