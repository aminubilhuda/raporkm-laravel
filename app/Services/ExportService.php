<?php

namespace App\Services;

use App\Models\JenisAbsen;
use App\Models\NilaiMapel;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function exportNilai(int $kelasId, int $mapelId, int $tahunId, int $semesterId): StreamedResponse
    {
        $nilai = NilaiMapel::with('siswa')
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get()
            ->sortBy('siswa.nama_siswa');

        $filename = "Nilai-{$kelasId}-{$mapelId}-{$tahunId}-{$semesterId}.xlsx";

        return $this->stream($filename, function (Writer $writer) use ($nilai) {
            $header = Row::fromValues(['No', 'NISN', 'NIS', 'Nama Siswa', 'Nilai', 'Predikat', 'KKM', 'Deskripsi']);
            $writer->addRow($header);

            if ($nilai->isEmpty()) {
                $writer->addRow(Row::fromValues(['-', '-', '-', 'Tidak ada data', '-', '-', '-', '-']));

                return;
            }

            foreach ($nilai as $i => $n) {
                $writer->addRow(Row::fromValues([
                    $i + 1,
                    $n->siswa?->nisn ?? '-',
                    $n->siswa?->nis ?? '-',
                    $n->siswa?->nama_siswa ?? '-',
                    $n->nilai,
                    $n->predikat ?? '-',
                    $n->kktp ?? 75,
                    $n->deskripsi ?? '-',
                ]));
            }
        });
    }

    public function exportPresensi(int $kelasId, int $tahunId, int $semesterId): StreamedResponse
    {
        $jenisAbsen = JenisAbsen::pluck('id', 'nama')->toArray();
        $idSakit = $jenisAbsen['Sakit'] ?? 2;
        $idIzin = $jenisAbsen['Izin'] ?? 3;
        $idAlpha = $jenisAbsen['Alpha'] ?? 4;

        $rows = Presensi::with('siswa')
            ->where('kelas_id', $kelasId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get();

        $bySiswa = $rows->groupBy('siswa_id')->map(function ($presensi) use ($idSakit, $idIzin, $idAlpha) {
            $byJenis = $presensi->groupBy('jenis_absen_id')->map->count();
            $totalHari = $presensi->count();

            return [
                'siswa' => $presensi->first()?->siswa,
                'hadir' => $totalHari - ($byJenis[$idSakit] ?? 0) - ($byJenis[$idIzin] ?? 0) - ($byJenis[$idAlpha] ?? 0),
                'sakit' => $byJenis[$idSakit] ?? 0,
                'izin' => $byJenis[$idIzin] ?? 0,
                'alpha' => $byJenis[$idAlpha] ?? 0,
            ];
        })->sortBy(fn ($r) => $r['siswa']?->nama_siswa ?? '');

        $filename = "Presensi-{$kelasId}-{$tahunId}-{$semesterId}.xlsx";

        return $this->stream($filename, function (Writer $writer) use ($bySiswa) {
            $writer->addRow(Row::fromValues(['No', 'NISN', 'NIS', 'Nama Siswa', 'Hadir', 'Sakit', 'Izin', 'Alpha']));

            if ($bySiswa->isEmpty()) {
                $writer->addRow(Row::fromValues(['-', '-', '-', 'Tidak ada data', '-', '-', '-', '-']));

                return;
            }

            $i = 1;
            foreach ($bySiswa as $r) {
                $writer->addRow(Row::fromValues([
                    $i++,
                    $r['siswa']?->nisn ?? '-',
                    $r['siswa']?->nis ?? '-',
                    $r['siswa']?->nama_siswa ?? '-',
                    $r['hadir'],
                    $r['sakit'],
                    $r['izin'],
                    $r['alpha'],
                ]));
            }
        });
    }

    public function exportSiswa(?int $kelasId = null): StreamedResponse
    {
        $query = Siswa::query();
        if ($kelasId) {
            $siswaIds = SiswaKelas::where('kelas_id', $kelasId)
                ->where('status', 'aktif')
                ->pluck('siswa_id');
            $query->whereIn('id', $siswaIds);
        }
        $siswa = $query->with(['siswaKelas.kelas.kompetensiKeahlian'])->orderBy('nama_siswa')->get();

        $filename = $kelasId
            ? "Siswa-Kelas-{$kelasId}.xlsx"
            : 'Siswa-Semua.xlsx';

        return $this->stream($filename, function (Writer $writer) use ($siswa) {
            $writer->addRow(Row::fromValues([
                'No', 'NISN', 'NIS', 'Nama Siswa', 'Jenis Kelamin', 'Jurusan',
                'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Kontak',
                'Alamat', 'Nama Ayah', 'Pekerjaan Ayah', 'Nama Ibu', 'Pekerjaan Ibu',
                'Sekolah Asal', 'Status',
            ]));

            if ($siswa->isEmpty()) {
                $writer->addRow(Row::fromValues(['-', '-', '-', 'Tidak ada data', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-']));

                return;
            }

            $i = 1;
            foreach ($siswa as $s) {
                $writer->addRow(Row::fromValues([
                    $i++,
                    $s->nisn,
                    $s->nis,
                    $s->nama_siswa,
                    $s->kelamin == 1 ? 'L' : 'P',
                    $s->siswaKelas->last()?->kelas?->kompetensiKeahlian?->nama ?? '-',
                    $s->tempat_lahir,
                    $s->tanggal_lahir,
                    $s->agama,
                    $s->kontak_siswa,
                    $s->alamat,
                    $s->nama_ayah ?? '-',
                    $s->pekerjaan_ayah ?? '-',
                    $s->nama_ibu ?? '-',
                    $s->pekerjaan_ibu ?? '-',
                    $s->sekolah_asal,
                    $s->aktif == 1 ? 'Aktif' : 'Non-aktif',
                ]));
            }
        });
    }

    private function stream(string $filename, \Closure $callback): StreamedResponse
    {
        $tempFile = storage_path('app/'.uniqid('export_', true).'.xlsx');

        return new StreamedResponse(function () use ($callback, $tempFile) {
            $writer = new Writer;
            $writer->openToFile($tempFile);

            $callback($writer);

            $writer->close();

            readfile($tempFile);

            @unlink($tempFile);
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
