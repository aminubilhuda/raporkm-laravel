<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('tu.dashboard', [
            'totalSiswa' => Siswa::where('aktif', 1)->count(),
            'totalKelas' => Kelas::count(),
            'totalMapel' => Mapel::count(),
            'totalGuru' => User::where('jabatan', 3)->count(),
        ]);
    }
}
