<?php

namespace App\Services\Dapodik;

use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;

class SekolahSyncService
{
    public function __construct(private DapodikClient $client)
    {
    }

    public function sync(): array
    {
        $data = $this->client->get('getSekolah');

        if (empty($data)) {
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

        $this->syncActivePeriod($sekolah);

        return ['success' => 1, 'message' => '1 data sekolah berhasil disinkron.'];
    }

    private function syncActivePeriod(Sekolah $sekolah): void
    {
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
    }
}
