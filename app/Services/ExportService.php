<?php

namespace App\Services;

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
        $rows = Presensi::with('siswa')
            ->where('kelas_id', $kelasId)
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->get();

        $bySiswa = $rows->groupBy('siswa_id')->map(function ($presensi) {
            $byJenis = $presensi->groupBy('jenis_absen_id')->map->count();

            return [
                'siswa' => $presensi->first()?->siswa,
                'sakit' => $byJenis[2] ?? 0,
                'izin' => $byJenis[3] ?? 0,
                'alpha' => $byJenis[4] ?? 0,
            ];
        })->sortBy(fn ($r) => $r['siswa']?->nama_siswa ?? '');

        $filename = "Presensi-{$kelasId}-{$tahunId}-{$semesterId}.xlsx";

        return $this->stream($filename, function (Writer $writer) use ($bySiswa) {
            $writer->addRow(Row::fromValues(['No', 'NISN', 'NIS', 'Nama Siswa', 'Sakit', 'Izin', 'Tanpa Keterangan']));

            $i = 1;
            foreach ($bySiswa as $r) {
                $writer->addRow(Row::fromValues([
                    $i++,
                    $r['siswa']?->nisn ?? '-',
                    $r['siswa']?->nis ?? '-',
                    $r['siswa']?->nama_siswa ?? '-',
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
        $siswa = $query->orderBy('nama_siswa')->get();

        $filename = $kelasId
            ? "Siswa-Kelas-{$kelasId}.xlsx"
            : 'Siswa-Semua.xlsx';

        return $this->stream($filename, function (Writer $writer) use ($siswa) {
            $writer->addRow(Row::fromValues([
                'No', 'NISN', 'NIS', 'Nama Siswa', 'Jenis Kelamin',
                'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Kontak',
                'Alamat', 'Sekolah Asal', 'Aktif',
            ]));

            $i = 1;
            foreach ($siswa as $s) {
                $writer->addRow(Row::fromValues([
                    $i++,
                    $s->nisn,
                    $s->nis,
                    $s->nama_siswa,
                    $s->kelamin == 1 ? 'L' : 'P',
                    $s->tempat_lahir,
                    $s->tanggal_lahir,
                    $s->agama,
                    $s->kontak_siswa,
                    $s->alamat,
                    $s->sekolah_asal,
                    $s->aktif == 1 ? 'Aktif' : 'Non-aktif',
                ]));
            }
        });
    }

    private function stream(string $filename, \Closure $callback): StreamedResponse
    {
        return new StreamedResponse(function () use ($callback) {
            $writer = new Writer;
            $writer->openToStream('php://output');

            $callback($writer);

            $writer->close();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
