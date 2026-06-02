<?php

namespace Tests\Feature\Guru;

use App\Models\Kelas;
use App\Models\KelasWali;
use App\Models\KompetensiKeahlian;
use App\Models\Mapel;
use App\Models\MapelKelas;
use App\Models\NilaiFormatif;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private User $guruLain;

    private TahunPelajaran $tahun;

    private Semester $semester;

    private Mapel $mapel;

    private Kelas $kelas;

    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();

        Sekolah::factory()->create([
            'tahun_aktif' => $this->tahun->id,
            'semester_aktif' => $this->semester->id,
        ]);

        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();

        $this->mapel = Mapel::factory()->create();
        $this->kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);

        $this->guru = User::factory()->guru()->create();
        $this->guruLain = User::factory()->guru()->create();

        MapelKelas::factory()->create([
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->siswa = Siswa::factory()->create();
        SiswaKelas::factory()->create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_guru_tidak_bisa_input_nilai_mapel_yang_tidak_diajar(): void
    {
        $mapelLain = Mapel::factory()->create();

        $response = $this->actingAs($this->guruLain)->post(route('guru.penilaian.sumatif-as'), [
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $mapelLain->id,
            'siswa_id' => [$this->siswa->id],
            'nilai' => [
                $this->siswa->id => 85,
            ],
        ]);

        $response->assertForbidden();
    }

    public function test_guru_tidak_bisa_lihat_lager_nilai_kelas_yang_tidak_diajar(): void
    {
        $response = $this->actingAs($this->guruLain)
            ->get(route('guru.lager-nilai-kelas.index', $this->kelas));

        $response->assertOk();
        $response->assertViewIs('guru.lager-nilai-kelas.index');
        $response->assertViewHas('authorized', false);
    }

    public function test_wali_kelas_bisa_lihat_catatan_rapor_kelasnya(): void
    {
        $this->actingAs($this->guru);
        \DB::table('kelas_wali')->insert([
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $response = $this->actingAs($this->guru)
            ->get(route('guru.catatan-rapor.index', $this->kelas));

        $response->assertOk();
        $response->assertViewHas('authorized', true);
    }

    public function test_bukan_wali_tidak_bisa_akses_catatan_rapor_kelas_lain(): void
    {
        \DB::table('kelas_wali')->insert([
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $response = $this->actingAs($this->guruLain)
            ->get(route('guru.catatan-rapor.index', $this->kelas));

        $response->assertOk();
        $response->assertViewHas('authorized', false);
    }

    public function test_penilaian_index_menampilkan_hanya_kelas_dan_mapel_yang_diajar(): void
    {
        $mapelLain = Mapel::factory()->create();
        MapelKelas::factory()->create([
            'mapel_id' => $mapelLain->id,
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guruLain->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $response = $this->actingAs($this->guru)
            ->get(route('guru.penilaian.index', ['kelas' => $this->kelas, 'mapel' => $this->mapel]));

        $response->assertOk();
        $response->assertViewHas('authorized', true);
    }

    public function test_penilaian_index_guru_lain_terkunci(): void
    {
        $response = $this->actingAs($this->guruLain)
            ->get(route('guru.penilaian.index', ['kelas' => $this->kelas, 'mapel' => $this->mapel]));

        $response->assertOk();
        $response->assertViewHas('authorized', false);
    }

    public function test_guru_tidak_bisa_tambah_tp_untuk_mapel_yang_tidak_diajar(): void
    {
        $mapelLain = Mapel::factory()->create();

        $response = $this->actingAs($this->guruLain)->post(route('guru.tujuan-pembelajaran.store'), [
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $mapelLain->id,
            'kode_tp' => 'TP-TEST',
            'nama_tp' => 'TP Test Unauthorized',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('tujuan_pembelajaran', ['kode_tp' => 'TP-TEST']);
    }

    public function test_guru_tidak_bisa_tambah_catatan_kelas_bukan_walianya(): void
    {
        \DB::table('kelas_wali')->insert([
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $this->tahun->id,
            'semester_id' => $this->semester->id,
        ]);

        $response = $this->actingAs($this->guruLain)->post(route('guru.catatan-rapor.store'), [
            'kelas_id' => $this->kelas->id,
            'siswa_id' => $this->siswa->id,
            'catatan' => 'Unauthorized',
        ]);

        $response->assertForbidden();
    }
}
