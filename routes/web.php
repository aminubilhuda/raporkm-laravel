<?php

use App\Http\Controllers\Guru\AbsensiBkController;
use App\Http\Controllers\Guru\AbsensiGuruController;
use App\Http\Controllers\Guru\AnggotaKelasController as GuruAnggotaKelasController;
use App\Http\Controllers\Guru\CatatanRaporController;
use App\Http\Controllers\Guru\CetakRaporController as GuruCetakRaporController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\EkstraController as GuruEkstraController;
use App\Http\Controllers\Guru\KelasKuController;
use App\Http\Controllers\Guru\KokurikulerController as GuruKokurikulerController;
use App\Http\Controllers\Guru\LagerNilaiKelasController;
use App\Http\Controllers\Guru\NilaiPrakerinController as GuruNilaiPrakerinController;
use App\Http\Controllers\Guru\OrganisasiController as GuruOrganisasiController;
use App\Http\Controllers\Guru\PenilaianController;
use App\Http\Controllers\Guru\PenilaianKokurikulerController;
use App\Http\Controllers\Guru\PenilaianProfilPancasilaController;
use App\Http\Controllers\Guru\PiketHarianController as GuruPiketHarianController;
use App\Http\Controllers\Guru\PrakerinController as GuruPrakerinController;
use App\Http\Controllers\Guru\PresensiController as GuruPresensiController;
use App\Http\Controllers\Guru\ProjectKelasController;
use App\Http\Controllers\Guru\RaporPklController;
use App\Http\Controllers\Guru\TujuanPembelajaranController;
use App\Http\Controllers\TU\AbsensiGuruController as TuAbsensiGuruController;
use App\Http\Controllers\TU\AnggotaKelasController;
use App\Http\Controllers\TU\BukuIndukController;
use App\Http\Controllers\TU\DapodikController;
use App\Http\Controllers\TU\DashboardController;
use App\Http\Controllers\TU\DeskripsiRaporController;
use App\Http\Controllers\TU\EksporController;
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
use App\Http\Controllers\TU\PegawaiController;
use App\Http\Controllers\TU\PengaturanController;
use App\Http\Controllers\TU\PengingatController;
use App\Http\Controllers\TU\PiketHarianController;
use App\Http\Controllers\TU\PrakerinController;
use App\Http\Controllers\TU\PresensiController;
use App\Http\Controllers\TU\PrestasiController;
use App\Http\Controllers\TU\RaporController;
use App\Http\Controllers\TU\RoleController;
use App\Http\Controllers\TU\RombelController;
use App\Http\Controllers\TU\SekolahController;
use App\Http\Controllers\TU\TingkatController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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

    // Role & Permission Management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::post('/pengaturan/push', [PengaturanController::class, 'sendPush'])->name('pengaturan.push');
    Route::post('/pengaturan/backup', [PengaturanController::class, 'backup'])->name('pengaturan.backup');
    Route::post('/pengaturan/restore', [PengaturanController::class, 'restore'])->name('pengaturan.restore');
    Route::post('/set-semester', [PengaturanController::class, 'setSemester'])->name('set-semester');
    Route::resource('deskripsi-rapor', DeskripsiRaporController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/mapel', [MapelController::class, 'index'])->name('mapel.index');
    Route::get('/mapel/create', [MapelController::class, 'create'])->name('mapel.create');
    Route::post('/mapel', [MapelController::class, 'store'])->name('mapel.store');
    Route::get('/mapel/{mapel}/edit', [MapelController::class, 'edit'])->name('mapel.edit');
    Route::put('/mapel/{mapel}', [MapelController::class, 'update'])->name('mapel.update');
    Route::delete('/mapel/{mapel}', [MapelController::class, 'destroy'])->name('mapel.destroy');
    Route::post('/mapel/batch-update', [MapelController::class, 'updateBatch'])->name('mapel.batch-update');
    Route::resource('tingkat', TingkatController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('kompetensi', KompetensiKeahlianController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('kelompok-mapel', KelompokMapelController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/kesiswaan/import', [KesiswaanController::class, 'import'])->name('kesiswaan.import');
    Route::post('/kesiswaan/import', [KesiswaanController::class, 'doImport'])->name('kesiswaan.do-import');
    Route::resource('kesiswaan', KesiswaanController::class);
    Route::patch('rombel/{rombel}/jurusan', [RombelController::class, 'updateJurusan'])->name('rombel.jurusan');
    Route::patch('rombel/{rombel}/wali', [RombelController::class, 'updateWali'])->name('rombel.wali');
    Route::post('rombel/batch-save', [RombelController::class, 'batchSave'])->name('rombel.batch-save');
    Route::resource('rombel', RombelController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/anggota-kelas', [AnggotaKelasController::class, 'index'])->name('anggota-kelas.index');
    Route::post('/anggota-kelas', [AnggotaKelasController::class, 'store'])->name('anggota-kelas.store');
    Route::delete('/anggota-kelas/{id}', [AnggotaKelasController::class, 'destroy'])->name('anggota-kelas.destroy');
    Route::get('/rapor/{siswa}/identitas', [RaporController::class, 'identitas'])->name('rapor.identitas');
    Route::get('/mapel-kelas', [MapelKelasController::class, 'index'])->name('mapel-kelas.index');
    Route::post('/mapel-kelas', [MapelKelasController::class, 'store'])->name('mapel-kelas.store');
    Route::put('/mapel-kelas/{id}', [MapelKelasController::class, 'update'])->name('mapel-kelas.update');
    Route::post('/mapel-kelas/batch-update', [MapelKelasController::class, 'updateBatch'])->name('mapel-kelas.batch-update');
    Route::delete('/mapel-kelas/{id}', [MapelKelasController::class, 'destroy'])->name('mapel-kelas.destroy');
    Route::get('/mapel-siswa', [MapelSiswaController::class, 'index'])->name('mapel-siswa.index');
    Route::post('/mapel-siswa/batch-update', [MapelSiswaController::class, 'batchUpdate'])->name('mapel-siswa.batch-update');
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

    Route::get('/prakerin/import', [PrakerinController::class, 'import'])->name('prakerin.import');
    Route::post('/prakerin/import', [PrakerinController::class, 'doImport'])->name('prakerin.do-import');
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

    Route::get('/buku-induk', [BukuIndukController::class, 'index'])->name('buku-induk.index');
    Route::get('/buku-induk/{bukuInduk}', [BukuIndukController::class, 'show'])->name('buku-induk.show');
    Route::get('/buku-induk-pdf', [BukuIndukController::class, 'pdf'])->name('buku-induk.pdf');

    Route::get('/pengingat', [PengingatController::class, 'index'])->name('pengingat.index');
    Route::post('/pengingat', [PengingatController::class, 'store'])->name('pengingat.store');
    Route::delete('/pengingat/{pengingat}', [PengingatController::class, 'destroy'])->name('pengingat.destroy');

    Route::get('/rekap-presensi', [PresensiController::class, 'rekap'])->name('presensi.rekap');

    Route::get('/absensi-guru', [TuAbsensiGuruController::class, 'index'])->name('absensi-guru.index');
    Route::post('/absensi-guru/check-in', [TuAbsensiGuruController::class, 'checkIn'])->name('absensi-guru.check-in');
    Route::post('/absensi-guru/check-out', [TuAbsensiGuruController::class, 'checkOut'])->name('absensi-guru.check-out');
    Route::get('/absensi-guru/rekap', [TuAbsensiGuruController::class, 'rekap'])->name('absensi-guru.rekap');

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

    Route::get('/dapodik', [DapodikController::class, 'index'])->name('dapodik.index');
    Route::post('/dapodik/config', [DapodikController::class, 'updateConfig'])->name('dapodik.config');
    Route::post('/dapodik/sync/{endpoint}', [DapodikController::class, 'sync'])->name('dapodik.sync');
    Route::get('/dapodik/log', [DapodikController::class, 'log'])->name('dapodik.log');
    Route::get('/dapodik/status', [DapodikController::class, 'status'])->name('dapodik.status');
    Route::post('/dapodik/cancel', [DapodikController::class, 'cancel'])->name('dapodik.cancel');
});

// === Panel Guru ===
Route::middleware(['auth', 'role:3,4'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    Route::post('/set-semester', [GuruDashboardController::class, 'setSemester'])->name('set-semester');

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
    Route::get('/penilaian-profil-pancasila/{kelas?}', [PenilaianProfilPancasilaController::class, 'index'])->name('penilaian-profil-pancasila.index');
    Route::get('/penilaian-profil-pancasila/{proyekKelas}/penilaian', [PenilaianProfilPancasilaController::class, 'penilaian'])->name('penilaian-profil-pancasila.penilaian');
    Route::post('/penilaian-profil-pancasila/{proyekKelas}/penilaian', [PenilaianProfilPancasilaController::class, 'storePenilaian'])->name('penilaian-profil-pancasila.store');
    Route::get('/kokurikuler', [GuruKokurikulerController::class, 'index'])->name('kokurikuler.index');
    Route::get('/penilaian-kokurikuler/{kelas?}', [PenilaianKokurikulerController::class, 'index'])->name('penilaian-kokurikuler.index');
    Route::post('/penilaian-kokurikuler', [PenilaianKokurikulerController::class, 'store'])->name('penilaian-kokurikuler.store');
    Route::get('/ekstra', [GuruEkstraController::class, 'index'])->name('ekstra.index');
    Route::get('/prakerin', [GuruPrakerinController::class, 'index'])->name('prakerin.index');
    Route::get('/nilai-prakerin', [GuruNilaiPrakerinController::class, 'index'])->name('nilai-prakerin.index');
    Route::get('/nilai-prakerin/{siswaPrakerin}/edit', [GuruNilaiPrakerinController::class, 'edit'])->name('nilai-prakerin.edit');
    Route::post('/nilai-prakerin/{siswaPrakerin}', [GuruNilaiPrakerinController::class, 'store'])->name('nilai-prakerin.store');
    Route::get('/piket-harian', [GuruPiketHarianController::class, 'index'])->name('piket-harian.index');
    Route::get('/organisasi', [GuruOrganisasiController::class, 'index'])->name('organisasi.index');
    Route::get('/presensi', [GuruPresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi', [GuruPresensiController::class, 'store'])->name('presensi.store');
    Route::get('/rekap-presensi', [GuruPresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::get('/absensi-bk', [AbsensiBkController::class, 'index'])->name('absensi-bk.index');
    Route::post('/absensi-bk', [AbsensiBkController::class, 'store'])->name('absensi-bk.store');
    Route::get('/absensi-guru', [AbsensiGuruController::class, 'index'])->name('absensi-guru.index');
    Route::post('/absensi-guru/check-in', [AbsensiGuruController::class, 'checkIn'])->name('absensi-guru.check-in');
    Route::post('/absensi-guru/check-out', [AbsensiGuruController::class, 'checkOut'])->name('absensi-guru.check-out');
    Route::get('/rapor-pkl', [RaporPklController::class, 'index'])->name('rapor-pkl.index');
    Route::get('/rapor-pkl/{siswaPrakerin}/pdf', [RaporPklController::class, 'pdf'])->name('rapor-pkl.pdf');
    Route::get('/cetak-rapor/{kelas?}', [GuruCetakRaporController::class, 'index'])->name('cetak-rapor.index');
    Route::post('/cetak-rapor/{kelas}/cetak', [GuruCetakRaporController::class, 'cetak'])->name('cetak-rapor.cetak');
});

require __DIR__.'/auth.php';
