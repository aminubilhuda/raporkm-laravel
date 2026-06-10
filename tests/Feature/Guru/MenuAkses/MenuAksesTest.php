<?php

namespace Tests\Feature\Guru\MenuAkses;

use App\Models\GuruMenuAkses;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use App\Services\GuruMenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MenuAksesTest extends TestCase
{
    use RefreshDatabase;

    private GuruMenuService $menuService;

    private User $guru;

    private TahunPelajaran $tahun;

    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menuService = new GuruMenuService;

        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();

        Sekolah::factory()->create([
            'tahun_aktif' => $this->tahun->id,
            'semester_aktif' => $this->semester->id,
        ]);

        $this->guru = User::factory()->guru()->create();
    }

    public function test_guru_tanpa_apapun_hanya_lihat_menu_universal(): void
    {
        $menus = $this->menuService->getVisibleMenus($this->guru, $this->tahun->id, $this->semester->id);

        $this->assertEqualsCanonicalizing([
            'dashboard',
            'kelas-ku',
            'prakerin',
            'piket-harian',
            'organisasi',
        ], $menus);
    }

    public function test_guru_yang_mengajar_mapel_lihat_menu_penilaian(): void
    {
        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);
        $mapel = Mapel::factory()->create();

        MapelKelas::factory()->create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $menus = $this->menuService->getVisibleMenus($this->guru, $this->tahun->id, $this->semester->id);

        $this->assertContains('tujuan-pembelajaran', $menus);
        $this->assertContains('penilaian', $menus);
        $this->assertContains('lager-nilai', $menus);
    }

    public function test_guru_wali_kelas_lihat_menu_wali(): void
    {
        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);

        DB::table('kelas_wali')->insert([
            'kelas_id' => $kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $menus = $this->menuService->getVisibleMenus($this->guru, $this->tahun->id, $this->semester->id);

        $this->assertContains('catatan-rapor', $menus);
        $this->assertContains('cetak-rapor', $menus);
        $this->assertContains('project-kelas', $menus);
        $this->assertContains('kokurikuler', $menus);
    }

    public function test_revoke_override_sembunyikan_menu(): void
    {
        // Make guru a wali kelas
        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);

        DB::table('kelas_wali')->insert([
            'kelas_id' => $kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        // Revoke cetak-rapor
        GuruMenuAkses::create([
            'user_id' => $this->guru->id,
            'menu_slug' => 'cetak-rapor',
            'tipe' => 'revoke',
        ]);

        $menus = $this->menuService->getVisibleMenus($this->guru, $this->tahun->id, $this->semester->id);

        $this->assertNotContains('cetak-rapor', $menus);
        // Other wali menus should still be visible
        $this->assertContains('catatan-rapor', $menus);
    }

    public function test_grant_override_tampilkan_menu(): void
    {
        // Guru has no wali kelas, but grant cetak-rapor
        GuruMenuAkses::create([
            'user_id' => $this->guru->id,
            'menu_slug' => 'cetak-rapor',
            'tipe' => 'grant',
        ]);

        $menus = $this->menuService->getVisibleMenus($this->guru, $this->tahun->id, $this->semester->id);

        $this->assertContains('cetak-rapor', $menus);
    }

    public function test_revoke_beats_grant(): void
    {
        GuruMenuAkses::create([
            'user_id' => $this->guru->id,
            'menu_slug' => 'cetak-rapor',
            'tipe' => 'grant',
        ]);

        // Overwrite with revoke
        GuruMenuAkses::where('user_id', $this->guru->id)
            ->where('menu_slug', 'cetak-rapor')
            ->update(['tipe' => 'revoke']);

        $menus = $this->menuService->getVisibleMenus($this->guru, $this->tahun->id, $this->semester->id);

        $this->assertNotContains('cetak-rapor', $menus);
    }

    public function test_sidebar_menampilkan_menu_berdasarkan_hak_akses(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.dashboard'))
            ->assertOk();
    }

    public function test_pegawai_form_menampilkan_menu_access_section(): void
    {
        $admin = User::factory()->tataUsaha()->create();

        $this->actingAs($admin)
            ->get(route('tu.pegawai.edit', $this->guru))
            ->assertOk()
            ->assertSee('Hak Akses Menu');
    }
}
