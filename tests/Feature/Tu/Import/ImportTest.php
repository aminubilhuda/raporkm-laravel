<?php

namespace Tests\Feature\Tu\Import;

use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    private User $tu;

    private Kelas $kelas;

    private TahunPelajaran $tahun;

    private Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tahun = TahunPelajaran::factory()->create();
        $this->semester = Semester::factory()->create();
        $this->tu = User::factory()->tataUsaha()->create();

        Sekolah::factory()->create([
            'tahun_aktif' => $this->tahun->id,
            'semester_aktif' => $this->semester->id,
        ]);

        $tingkat = Tingkat::factory()->create();
        $jurusan = KompetensiKeahlian::factory()->create();
        $this->kelas = Kelas::factory()->create([
            'tingkat_id' => $tingkat->id,
            'kompetensi_keahlian_id' => $jurusan->id,
        ]);
    }

    public function test_import_siswa_form_loads(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.kesiswaan.import'))
            ->assertStatus(200)
            ->assertSee('Import Siswa')
            ->assertSee('Pilih File');
    }

    public function test_import_siswa_csv_success(): void
    {
        $csv = "nisn,nis,nama_siswa,tempat_lahir\n".
            "1234567890,2024001,Andi Pratama,Surabaya\n".
            '1234567891,2024002,Siti Aisyah,Surabaya';

        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csv);

        $this->actingAs($this->tu)->post(route('tu.kesiswaan.do-import'), [
            'file' => $file,
            'kelas_id' => $this->kelas->id,
        ])->assertRedirect()->assertSessionHas('import_result');

        $this->assertDatabaseHas('siswa', ['nisn' => '1234567890', 'nama_siswa' => 'Andi Pratama']);
        $this->assertDatabaseHas('siswa', ['nisn' => '1234567891', 'nama_siswa' => 'Siti Aisyah']);
        $this->assertEquals(2, Siswa::count());
    }

    public function test_import_siswa_skips_duplicate_nisn(): void
    {
        Siswa::factory()->create(['nisn' => '1234567890']);

        $csv = "nisn,nis,nama_siswa\n".
            "1234567890,2024001,Andi Pratama\n".
            '1234567892,2024003,Budi Santoso';

        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csv);

        $this->actingAs($this->tu)->post(route('tu.kesiswaan.do-import'), [
            'file' => $file,
            'kelas_id' => $this->kelas->id,
        ])->assertRedirect()->assertSessionHas('import_result');

        $this->assertEquals(2, Siswa::count()); // 1 existing + 1 new = 2
    }

    public function test_import_prakerin_form_loads(): void
    {
        $this->actingAs($this->tu)
            ->get(route('tu.prakerin.import'))
            ->assertStatus(200)
            ->assertSee('Import Prakerin');
    }

    public function test_import_prakerin_csv_success(): void
    {
        $csv = "nama_perusahaan,PIC,kontak\n".
            "PT Maju Bersama,Budi,08123456789\n".
            'CV Karya Mandiri,Siti,08123456788';

        $file = UploadedFile::fake()->createWithContent('prakerin.csv', $csv);

        $this->actingAs($this->tu)->post(route('tu.prakerin.do-import'), [
            'file' => $file,
        ])->assertRedirect()->assertSessionHas('import_result');

        $this->assertDatabaseHas('prakerin', ['nama_perusahaan' => 'PT Maju Bersama']);
        $this->assertDatabaseHas('prakerin', ['nama_perusahaan' => 'CV Karya Mandiri']);
    }

    public function test_import_siswa_validates_file_required(): void
    {
        $this->actingAs($this->tu)
            ->post(route('tu.kesiswaan.do-import'), ['kelas_id' => $this->kelas->id])
            ->assertSessionHasErrors('file');
    }

    public function test_import_siswa_validates_kelas_required(): void
    {
        $file = UploadedFile::fake()->create('siswa.csv', 100);

        $this->actingAs($this->tu)
            ->post(route('tu.kesiswaan.do-import'), ['file' => $file])
            ->assertSessionHasErrors('kelas_id');
    }
}
