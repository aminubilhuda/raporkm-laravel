<?php

namespace App\Services;

use App\Models\Prakerin;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportService
{
    public function importSiswa(UploadedFile $file, int $kelasId, ?int $tahunPelajaranId = null, ?int $semesterId = null): array
    {
        $rows = $this->readFile($file);
        if (empty($rows)) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['File kosong atau tidak terbaca.']];
        }

        $header = array_map('trim', array_map('strtolower', $rows[0]));
        $rows = array_slice($rows, 1);

        $fieldMap = [
            'nisn' => 'nisn',
            'nis' => 'nis',
            'nama_siswa' => 'nama_siswa',
            'nama' => 'nama_siswa',
            'nik_pd' => 'nik_pd',
            'nik' => 'nik_pd',
            'nkk' => 'nkk',
            'tempat_lahir' => 'tempat_lahir',
            'tanggal_lahir' => 'tanggal_lahir',
            'tgl_lahir' => 'tanggal_lahir',
            'kelamin' => 'kelamin',
            'jenis_kelamin' => 'kelamin',
            'agama' => 'agama',
            'alamat' => 'alamat',
            'nama_ayah' => 'nama_ayah',
            'nama_ibu' => 'nama_ibu',
            'kontak' => 'kontak_siswa',
            'kontak_siswa' => 'kontak_siswa',
            'sekolah_asal' => 'sekolah_asal',
            'asal_sekolah' => 'sekolah_asal',
            'pekerjaan_ayah' => 'pekerjaan_ayah',
            'pekerjaan_ibu' => 'pekerjaan_ibu',
            'nik_ayah' => 'nik_ayah',
            'nik_ibu' => 'nik_ibu',
        ];

        $mappedCols = [];
        foreach ($header as $i => $col) {
            if (isset($fieldMap[$col])) {
                $mappedCols[$i] = $fieldMap[$col];
            }
        }

        if (! in_array('nisn', $mappedCols) || ! in_array('nama_siswa', $mappedCols)) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Kolom wajib tidak ditemukan. Header harus mengandung: nisn, nama_siswa.']];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $line => $row) {
            $data = [];
            foreach ($mappedCols as $colIdx => $field) {
                $data[$field] = trim((string) ($row[$colIdx] ?? ''));
            }

            $data['aktif'] = 1;

            if (empty($data['nisn'])) {
                $failed++;
                $errors[] = 'Baris '.($line + 2).': NISN wajib diisi.';

                continue;
            }
            if (empty($data['nama_siswa'])) {
                $failed++;
                $errors[] = 'Baris '.($line + 2)." (NISN: {$data['nisn']}): Nama siswa wajib diisi.";

                continue;
            }

            if (empty($data['nis'])) {
                $data['nis'] = $data['nisn'];
            }

            if (Siswa::where('nisn', $data['nisn'])->exists()) {
                $failed++;
                $errors[] = 'Baris '.($line + 2)." (NISN: {$data['nisn']}): NISN sudah terdaftar.";

                continue;
            }

            try {
                $siswa = Siswa::create($data);

                if ($kelasId && $tahunPelajaranId && $semesterId) {
                    SiswaKelas::firstOrCreate([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $kelasId,
                        'tahun_pelajaran_id' => $tahunPelajaranId,
                        'semester_id' => $semesterId,
                    ], ['status' => 'aktif']);
                }

                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = 'Baris '.($line + 2)." (NISN: {$data['nisn']}): ".$e->getMessage();
            }
        }

        if (count($errors) > 50) {
            $errors = array_slice($errors, 0, 50);
            $errors[] = '... dan '.(count($errors) - 50).' error lainnya.';
        }

        return compact('success', 'failed', 'errors');
    }

    public function importPrakerin(UploadedFile $file, ?int $tahunPelajaranId = null, ?int $semesterId = null): array
    {
        $rows = $this->readFile($file);
        if (empty($rows)) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['File kosong atau tidak terbaca.']];
        }

        $header = array_map('trim', array_map('strtolower', $rows[0]));
        $rows = array_slice($rows, 1);

        $fieldMap = [
            'nama_perusahaan' => 'nama_perusahaan',
            'perusahaan' => 'nama_perusahaan',
            'pic' => 'PIC',
            'kontak' => 'kontak',
            'telepon' => 'kontak',
            'alamat' => 'alamat',
            'tanggal_mulai' => 'tanggal_mulai',
            'tgl_mulai' => 'tanggal_mulai',
            'tanggal_selesai' => 'tanggal_selesai',
            'tgl_selesai' => 'tanggal_selesai',
            'keterangan' => 'keterangan',
            'ket' => 'keterangan',
        ];

        $mappedCols = [];
        foreach ($header as $i => $col) {
            if (isset($fieldMap[$col])) {
                $mappedCols[$i] = $fieldMap[$col];
            }
        }

        if (! in_array('nama_perusahaan', $mappedCols)) {
            return ['success' => 0, 'failed' => 0, 'errors' => ['Kolom "nama_perusahaan" tidak ditemukan di header file.']];
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($rows as $line => $row) {
            $data = [];
            foreach ($mappedCols as $colIdx => $field) {
                $data[$field] = trim((string) ($row[$colIdx] ?? ''));
            }

            if (empty($data['nama_perusahaan'])) {
                $failed++;
                $errors[] = 'Baris '.($line + 2).': Nama perusahaan wajib diisi.';

                continue;
            }

            if ($tahunPelajaranId) {
                $data['tahun_pelajaran_id'] = $tahunPelajaranId;
            }
            if ($semesterId) {
                $data['semester_id'] = $semesterId;
            }

            try {
                Prakerin::create($data);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = 'Baris '.($line + 2)." ({$data['nama_perusahaan']}): ".$e->getMessage();
            }
        }

        if (count($errors) > 50) {
            $errors = array_slice($errors, 0, 50);
            $errors[] = '... dan '.(count($errors) - 50).' error lainnya.';
        }

        return compact('success', 'failed', 'errors');
    }

    private function readFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getPathname();

        if ($extension === 'csv') {
            return $this->readCsv($path);
        }

        return $this->readXlsx($path);
    }

    private function readCsv(string $path): array
    {
        $reader = new CsvReader;
        $reader->open($path);

        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values = $row->toArray();
                if (array_filter($values, fn ($v) => $v !== null && $v !== '')) {
                    $rows[] = $values;
                }
            }
        }

        $reader->close();

        return $rows;
    }

    private function readXlsx(string $path): array
    {
        $reader = new XlsxReader;
        $reader->open($path);

        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values = $row->toArray();
                if (array_filter($values, fn ($v) => $v !== null && $v !== '')) {
                    $rows[] = $values;
                }
            }
        }

        $reader->close();

        return $rows;
    }
}
