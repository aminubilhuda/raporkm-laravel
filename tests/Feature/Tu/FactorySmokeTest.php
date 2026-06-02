<?php

namespace Tests\Feature\Tu;

use App\Models\Dimensi;
use App\Models\DimensiKokurikuler;
use App\Models\Elemen;
use App\Models\Lulusan;
use App\Models\MutasiKeluar;
use App\Models\MutasiMasuk;
use App\Models\Organisasi;
use App\Models\PembinaEskul;
use App\Models\Pengingat;
use App\Models\PiketHarian;
use App\Models\Prestasi;
use App\Models\ProyekKelas;
use App\Models\ProyekTema;
use App\Models\SubElemen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactorySmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \DB::table('ref_hari')->insert([
            ['nama' => 'Senin', 'urutan' => 1],
            ['nama' => 'Selasa', 'urutan' => 2],
        ]);
        \DB::table('ref_jenis_keluar')->insert([
            ['nama' => 'Pindah'],
            ['nama' => 'Lulus'],
        ]);
    }

    public function test_all_fase_3_factories_can_create(): void
    {
        $this->assertNotNull(PembinaEskul::factory()->create()->id);
        $this->assertNotNull(Dimensi::factory()->create()->id);
        $this->assertNotNull(Elemen::factory()->create()->id);
        $this->assertNotNull(SubElemen::factory()->create()->id);
        $this->assertNotNull(ProyekTema::factory()->create()->id);
        $this->assertNotNull(ProyekKelas::factory()->create()->id);
        $this->assertNotNull(DimensiKokurikuler::factory()->create()->id);
        $this->assertNotNull(\App\Models\DeskripsiKokurikuler::factory()->create()->id);
        $this->assertNotNull(MutasiMasuk::factory()->create()->id);
        $this->assertNotNull(MutasiKeluar::factory()->create()->id);
        $this->assertNotNull(Lulusan::factory()->create()->id);
        $this->assertNotNull(PiketHarian::factory()->create()->id);
        $this->assertNotNull(Pengingat::factory()->create()->id);
        $this->assertNotNull(Prestasi::factory()->create()->id);
        $this->assertNotNull(Organisasi::factory()->create()->id);
    }
}
