<?php

namespace Tests\Feature\Guru\RaporPkl;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Prakerin;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaPrakerin;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaporPklTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    private SiswaPrakerin $siswaPrakerin;

    protected function setUp(): void
    {
        parent::setUp();
        $tahun = TahunPelajaran::factory()->create();
        $semester = Semester::factory()->create();
        $this->guru = User::factory()->guru()->create();

        Sekolah::factory()->create([
            'tahun_aktif' => $tahun->id,
            'semester_aktif' => $semester->id,
        ]);

        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);

        $siswa = Siswa::factory()->create();
        $prakerin = Prakerin::factory()->create();

        $this->siswaPrakerin = SiswaPrakerin::factory()->create([
            'prakerin_id' => $prakerin->id,
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'user_id' => $this->guru->id,
            'tahun_pelajaran_id' => $tahun->id,
            'semester_id' => $semester->id,
        ]);
    }

    public function test_guru_can_view_rapor_pkl_index(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.rapor-pkl.index'))
            ->assertStatus(200)
            ->assertSee('Rapor PKL');
    }

    public function test_guru_sees_only_their_pkl_students(): void
    {
        $this->actingAs($this->guru)
            ->get(route('guru.rapor-pkl.index'))
            ->assertStatus(200)
            ->assertSee($this->siswaPrakerin->siswa->nama_siswa);
    }

    public function test_other_guru_cannot_access_pkl_pdf_of_another(): void
    {
        $guruLain = User::factory()->guru()->create();

        $response = $this->actingAs($guruLain)
            ->get(route('guru.rapor-pkl.pdf', $this->siswaPrakerin));

        $response->assertForbidden();
    }
}
