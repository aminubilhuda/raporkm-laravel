<?php

namespace Tests\Feature\Tu\MapelSiswa;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\MapelSiswa;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapelSiswaTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    private User $guru;

    private Kelas $kelas;

    private TahunPelajaran $tp;

    private int $semesterId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tu = User::factory()->tataUsaha()->create();
        $this->guru = User::factory()->guru()->create();

        $this->seed('RefDataSeeder');

        $tingkat = \DB::table('tingkat')->where('angka', 10)->first();
        $kk = KompetensiKeahlian::create(['nama' => 'TKJ', 'singkatan' => 'TKJ']);
        $this->kelas = Kelas::create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $kk->id,
            'nama_kelas' => 'X TKJ 1',
        ]);

        $this->tp = TahunPelajaran::create(['tahun' => '2025/2026', 'status' => 1]);
        $this->semesterId = \DB::table('semester')->insertGetId(['nama' => 'Ganjil', 'urutan' => 1, 'status' => 1]);
    }

    public function test_tu_can_view_mapel_siswa_page(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.mapel-siswa.index'))
            ->assertStatus(200)
            ->assertSee('Peta Mapel Siswa');
    }

    public function test_guru_cannot_access_mapel_siswa(): void
    {
        $response = $this->actingAs($this->guru)->get(route('tu.mapel-siswa.index'));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    public function test_mapel_siswa_displays_matrix_with_correct_checkboxes(): void
    {
        $kelompokId = \DB::table('kelompok_mapel')->first()->id;
        $mapel = Mapel::create([
            'kelompok_mapel_id' => $kelompokId,
            'kode' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kkm' => 75,
        ]);

        $mk = MapelKelas::create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
        ]);

        $siswa = Siswa::factory()->create(['nama_siswa' => 'Budi Santoso']);
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
            'status' => 'aktif',
        ]);

        MapelSiswa::create(['siswa_id' => $siswa->id, 'mapel_kelas_id' => $mk->id]);

        $response = $this->actingAs($this->tu)->get(route('tu.mapel-siswa.index', [
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
        ]));

        $response->assertStatus(200)
            ->assertSee('Budi Santoso')
            ->assertSee('MTK')
            ->assertSee("mapel[{$siswa->id}][{$mk->id}]");
    }

    public function test_mapel_siswa_batch_update_saves_correctly(): void
    {
        $kelompokId = \DB::table('kelompok_mapel')->first()->id;
        $mapel = Mapel::create([
            'kelompok_mapel_id' => $kelompokId,
            'kode' => 'BIN',
            'nama_mapel' => 'Bahasa Indonesia',
            'kkm' => 75,
        ]);

        $mk = MapelKelas::create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
        ]);

        $siswa = Siswa::factory()->create();
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
            'status' => 'aktif',
        ]);

        $this->actingAs($this->tu)->post(route('tu.mapel-siswa.batch-update'), [
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
            'mapel' => [
                $siswa->id => [
                    $mk->id => '1',
                ],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('mapel_siswa', [
            'siswa_id' => $siswa->id,
            'mapel_kelas_id' => $mk->id,
        ]);
    }

    public function test_mapel_siswa_batch_update_removes_unchecked(): void
    {
        $kelompokId = \DB::table('kelompok_mapel')->first()->id;
        $mapel = Mapel::create([
            'kelompok_mapel_id' => $kelompokId,
            'kode' => 'BIG',
            'nama_mapel' => 'Bahasa Inggris',
            'kkm' => 75,
        ]);

        $mk = MapelKelas::create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
        ]);

        $siswa = Siswa::factory()->create();
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
            'status' => 'aktif',
        ]);

        MapelSiswa::create(['siswa_id' => $siswa->id, 'mapel_kelas_id' => $mk->id]);
        $this->assertDatabaseCount('mapel_siswa', 1);

        $this->actingAs($this->tu)->post(route('tu.mapel-siswa.batch-update'), [
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
            'mapel' => [
                (string) $siswa->id => [],
            ],
        ])->assertRedirect();

        $this->assertDatabaseCount('mapel_siswa', 0);
    }

    public function test_mapel_siswa_filter_by_kelas_and_semester(): void
    {
        $kelompokId = \DB::table('kelompok_mapel')->first()->id;
        $mapel = Mapel::create([
            'kelompok_mapel_id' => $kelompokId,
            'kode' => 'PP',
            'nama_mapel' => 'Pendidikan Pancasila',
            'kkm' => 75,
        ]);

        $mk = MapelKelas::create([
            'mapel_id' => $mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
        ]);

        $siswa = Siswa::factory()->create(['nama_siswa' => 'Andi Wijaya']);
        SiswaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->tu)->get(route('tu.mapel-siswa.index', [
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $this->semesterId,
        ]));

        $response->assertStatus(200)
            ->assertSee('Andi Wijaya')
            ->assertSee('PP');

        $wrongSemester = \DB::table('semester')->insertGetId(['nama' => 'Genap', 'urutan' => 2, 'status' => 0]);
        $response2 = $this->actingAs($this->tu)->get(route('tu.mapel-siswa.index', [
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tp->id,
            'semester_id' => $wrongSemester,
        ]));

        $response2->assertStatus(200)
            ->assertDontSee('Andi Wijaya');
    }
}
