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
            'tanggal_mid' => ['nullable', 'date'],
            'tanggal_semester' => ['nullable', 'date'],
        ]);

        $sekolah = Sekolah::first();
        if ($sekolah) {
            $sekolah->update([
                'tahun_aktif' => $validated['tahun_pelajaran_id'],
                'semester_aktif' => $validated['semester_id'],
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
}
