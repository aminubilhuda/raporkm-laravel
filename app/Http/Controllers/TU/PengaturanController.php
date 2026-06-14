<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\PembagianRaport;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use App\Models\User;
use App\Services\PushService;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::first();
        $tahunPelajarans = TahunPelajaran::orderBy('tahun', 'desc')->get();
        $semesters = Semester::orderBy('urutan')->get();
        $pembagian = PembagianRaport::first();
        $guruUsers = User::whereIn('jabatan', [3, 4])->orderBy('nama')->get();

        if (! $pembagian) {
            $pembagian = new PembagianRaport;
        }

        return view('tu.pengaturan.index', compact('sekolah', 'tahunPelajarans', 'semesters', 'pembagian', 'guruUsers'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'tahun_pelajaran_id' => ['required', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'format_rapor' => ['required', 'in:a4,f4'],
            'tanggal_mid' => ['nullable', 'date'],
            'tanggal_semester' => ['nullable', 'date'],
        ]);

        $sekolah = Sekolah::first();
        if ($sekolah) {
            $sekolah->update([
                'tahun_aktif' => $validated['tahun_pelajaran_id'],
                'semester_aktif' => $validated['semester_id'],
                'format_rapor' => $validated['format_rapor'],
            ]);
        }

        PembagianRaport::updateOrCreate(
            ['tahun_pelajaran_id' => $validated['tahun_pelajaran_id'], 'semester_id' => $validated['semester_id']],
            [
                'tanggal_mid' => $validated['tanggal_mid'] ?? null,
                'tanggal_semester' => $validated['tanggal_semester'] ?? null,
            ]
        );

        return back()->with('status', 'Pengaturan berhasil disimpan.');
    }

    public function setSemester(Request $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => ['required', 'exists:tahun_pelajaran,id'],
            'semester_id' => ['required', 'exists:semester,id'],
        ]);

        session([
            'selected_tahun' => $request->integer('tahun_pelajaran_id'),
            'selected_semester' => $request->integer('semester_id'),
        ]);

        return back();
    }

    public function sendPush(Request $request, PushService $pushService)
    {
        $validated = $request->validate([
            'push_title' => ['required', 'string', 'max:255'],
            'push_body' => ['required', 'string', 'max:1000'],
            'push_url' => ['nullable', 'string', 'max:500'],
            'push_target' => ['required', 'in:all,role,user'],
            'push_role' => ['required_if:push_target,role', 'nullable', 'integer', 'in:3,4'],
            'push_user_ids' => ['required_if:push_target,user', 'nullable', 'array'],
            'push_user_ids.*' => ['exists:users,id'],
        ]);

        $results = match ($validated['push_target']) {
            'all' => $pushService->sendToAll(
                $validated['push_title'],
                $validated['push_body'],
                $validated['push_url'] ?? null
            ),
            'role' => $pushService->sendToRole(
                (int) $validated['push_role'],
                $validated['push_title'],
                $validated['push_body'],
                $validated['push_url'] ?? null
            ),
            'user' => $pushService->sendToUsers(
                User::whereIn('id', $validated['push_user_ids'])->get(),
                $validated['push_title'],
                $validated['push_body'],
                $validated['push_url'] ?? null
            ),
        };

        $successCount = count(array_filter($results, fn ($r) => $r['success']));
        $failCount = count($results) - $successCount;

        return back()->with('status', "Push notification terkirim: {$successCount} berhasil, {$failCount} gagal.");
    }

    // ─── Database Backup & Restore ─────────────────────────────────

    private function findMysqlBinary(string $name): ?string
    {
        // 1. Cek .env config (contoh: MYSQLDUMP_PATH="C:\...\mysqldump.exe")
        $envKey = strtoupper($name) . '_PATH';
        $pathFromEnv = env($envKey);
        if ($pathFromEnv && file_exists($pathFromEnv)) {
            return $pathFromEnv;
        }

        // 2. Scan FlyEnv — cari semua versi MySQL yang terinstall
        $flyEnvBase = 'C:\\Program Files\\FlyEnv-Data\\app';
        if (is_dir($flyEnvBase)) {
            $found = glob($flyEnvBase . '\\mysql-*\\*-winx64\\bin\\' . $name . '.exe');
            if (is_array($found)) {
                foreach ($found as $path) {
                    if (file_exists($path)) {
                        return $path;
                    }
                }
            }
        }

        // 3. Coba `where` (system PATH)
        $whereResult = trim((string) shell_exec("where {$name} 2>nul"));
        if ($whereResult !== '' && file_exists($whereResult)) {
            return $whereResult;
        }

        // 4. Tidak ditemukan
        return null;
    }

    private function getMysqlConfig(): array
    {
        return config('database.connections.mysql');
    }

    private function buildMysqldumpCommand(string $mysqldumpPath, array $db): string
    {
        return sprintf(
            '"%s" --host=%s --port=%s --user=%s --password=%s --routines --events --single-transaction --default-character-set=utf8mb4 %s 2>&1',
            $mysqldumpPath,
            $db['host'],
            $db['port'],
            $db['username'],
            $db['password'],
            $db['database']
        );
    }

    private function isMysqldumpError(string $output): bool
    {
        $lower = strtolower($output);
        return str_starts_with($lower, 'mysqldump')
            || str_starts_with($lower, "'mysqldump")
            || str_contains($lower, 'not recognized')
            || str_contains($lower, 'no such file')
            || str_contains($lower, 'access denied');
    }

    public function backup()
    {
        $db = $this->getMysqlConfig();
        $filename = 'backup-' . str_replace('-', '', $db['database']) . '-' . now()->format('Y-m-d_His') . '.sql';

        // Coba mysqldump dulu
        $mysqldump = $this->findMysqlBinary('mysqldump');

        if ($mysqldump) {
            $command = $this->buildMysqldumpCommand($mysqldump, $db);
            $output = shell_exec($command);

            if ($output && ! $this->isMysqldumpError($output) && strlen($output) > 50) {
                return response()->streamDownload(function () use ($output) {
                    echo $output;
                }, $filename, [
                    'Content-Type' => 'application/sql',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            }
        }

        // Fallback: backup pakai PDO
        try {
            $output = $this->backupViaPdo($db);

            if ($output !== '') {
                return response()->streamDownload(function () use ($output) {
                    echo $output;
                }, $filename, [
                    'Content-Type' => 'application/sql',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $hint = $mysqldump
            ? 'mysqldump ditemukan di: ' . $mysqldump . ', tapi gagal dijalankan.'
            : 'mysqldump tidak ditemukan. Atur variabel MYSQLDUMP_PATH di file .env';

        return back()->with('error', 'Gagal membuat backup database. ' . $hint);
    }

    private function backupViaPdo(array $db): string
    {
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset=utf8mb4";
        $pdo = new \PDO($dsn, $db['username'], $db['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
        $sql = "-- E-Rapor KM Backup\n";
        $sql .= "-- Database: {$db['database']}\n";
        $sql .= "-- Date: " . now()->toDateTimeString() . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createTable['Create Table'] . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (count($rows) > 0) {
                $columns = array_keys($rows[0]);
                $cols = '`' . implode('`, `', $columns) . '`';

                foreach ($rows as $row) {
                    $values = array_map(function ($v) use ($pdo) {
                        return $v === null ? 'NULL' : $pdo->quote((string) $v);
                    }, array_values($row));
                    $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $pdo = null;

        return $sql;
    }

    public function restore(Request $request)
    {
        $validated = $request->validate([
            'backup_file' => ['required', 'file', 'mimes:sql,txt', 'max:51200'],
            'confirm_restore' => ['required', 'accepted'],
        ]);

        $db = $this->getMysqlConfig();
        $filePath = $request->file('backup_file')->getRealPath();
        $fileContent = file_get_contents($filePath);

        // Coba mysql CLI dulu
        $mysql = $this->findMysqlBinary('mysql');

        if ($mysql) {
            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s %s < "%s" 2>&1',
                $mysql,
                $db['host'],
                $db['port'],
                $db['username'],
                $db['password'],
                $db['database'],
                $filePath
            );

            $output = shell_exec($command);

            if ($output !== null && $output !== '' && ! str_contains(strtolower($output), 'not recognized')) {
                return back()->with('error', 'Gagal merestore database: ' . $output);
            }

            if ($output === null || $output === '') {
                return back()->with('status', 'Database berhasil direstore dari file backup.');
            }
        }

        // Fallback: restore pakai PDO
        try {
            $this->restoreViaPdo($db, $fileContent);

            return back()->with('status', 'Database berhasil direstore dari file backup.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal merestore database: ' . $e->getMessage());
        }
    }

    private function restoreViaPdo(array $db, string $sql): void
    {
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset=utf8mb4";
        $pdo = new \PDO($dsn, $db['username'], $db['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        $statements = array_filter(array_map('trim', explode(";\n", $sql)));
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '' || str_starts_with($statement, '--')) {
                continue;
            }
            $pdo->exec($statement);
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        $pdo = null;
    }
}
