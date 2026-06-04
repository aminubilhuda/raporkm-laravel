<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Kesiswaan\ImportSiswaRequest;
use App\Models\Kelas;
use App\Models\KompetensiKeahlian;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KesiswaanController extends Controller
{
    public function __construct(private ImportService $import) {}

    public function index()
    {
        $siswa = Siswa::with(['siswaKelas.kelas.tingkat', 'siswaKelas.kelas.kompetensiKeahlian'])
            ->where('aktif', 1)->latest()->paginate(15);

        return view('tu.kesiswaan.index', compact('siswa'));
    }

    public function import()
    {
        $kelass = Kelas::with('tingkat', 'kompetensiKeahlian')->orderBy('nama_kelas')->get();

        return view('tu.kesiswaan.import', compact('kelass'));
    }

    public function doImport(ImportSiswaRequest $r)
    {
        $sekolah = Sekolah::first();
        $result = $this->import->importSiswa(
            $r->file('file'),
            $r->integer('kelas_id'),
            $sekolah?->tahun_aktif,
            $sekolah?->semester_aktif,
        );

        return redirect()->route('tu.kesiswaan.import')
            ->with('import_result', $result);
    }

    public function create()
    {
        $kompetensi = KompetensiKeahlian::orderBy('nama')->get();

        return view('tu.kesiswaan.form', ['siswa' => new Siswa, 'kompetensi' => $kompetensi]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:100'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')],
            'nis' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nis')],
            'nik_pd' => ['nullable', 'string', 'max:20'],
            'nkk' => ['nullable', 'string', 'max:20'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'kelamin' => ['nullable', 'integer'],
            'agama' => ['nullable', 'integer'],
            'kontak_siswa' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah' => ['nullable', 'string', 'max:100'],
            'nik_ayah' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:30'],
            'kontak_ayah' => ['nullable', 'string', 'max:14'],
            'nama_ibu' => ['nullable', 'string', 'max:100'],
            'nik_ibu' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:30'],
            'kontak_ibu' => ['nullable', 'string', 'max:14'],
            'alamat_orang_tua' => ['nullable', 'string'],
            'nama_wali' => ['nullable', 'string', 'max:100'],
            'pekerjaan_wali' => ['nullable', 'string', 'max:30'],
            'kontak_wali' => ['nullable', 'string', 'max:14'],
            'sekolah_asal' => ['nullable', 'string'],
            'jenis_siswa' => ['nullable', 'integer'],
            'jurusan' => ['nullable', 'integer', 'exists:kompetensi_keahlian,id'],
        ]);

        Siswa::create($validated);

        return redirect()->route('tu.kesiswaan.index')->with('status', 'Siswa berhasil ditambahkan.');
    }

    public function show(Siswa $kesiswaan)
    {
        $kesiswaan->load(['siswaKelas.kelas.tingkat', 'siswaKelas.kelas.kompetensiKeahlian']);

        return response()->json($kesiswaan);
    }

    public function edit(Siswa $kesiswaan)
    {
        $kesiswaan->load(['siswaKelas.kelas']);
        $kompetensi = KompetensiKeahlian::orderBy('nama')->get();

        return response()->json([
            'siswa' => $kesiswaan,
            'kompetensi' => $kompetensi,
        ]);
    }

    public function update(Request $request, Siswa $kesiswaan)
    {
        $validated = $request->validate([
            'nama_siswa' => ['required', 'string', 'max:100'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')->ignore($kesiswaan->id)],
            'nis' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nis')->ignore($kesiswaan->id)],
            'nik_pd' => ['nullable', 'string', 'max:20'],
            'nkk' => ['nullable', 'string', 'max:20'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'kelamin' => ['nullable', 'integer'],
            'agama' => ['nullable', 'integer'],
            'kontak_siswa' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'nama_ayah' => ['nullable', 'string', 'max:100'],
            'nik_ayah' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:30'],
            'kontak_ayah' => ['nullable', 'string', 'max:14'],
            'nama_ibu' => ['nullable', 'string', 'max:100'],
            'nik_ibu' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:30'],
            'kontak_ibu' => ['nullable', 'string', 'max:14'],
            'alamat_orang_tua' => ['nullable', 'string'],
            'nama_wali' => ['nullable', 'string', 'max:100'],
            'pekerjaan_wali' => ['nullable', 'string', 'max:30'],
            'kontak_wali' => ['nullable', 'string', 'max:14'],
            'sekolah_asal' => ['nullable', 'string'],
            'jenis_siswa' => ['nullable', 'integer'],
            'aktif' => ['nullable', 'integer'],
            'jurusan' => ['nullable', 'integer', 'exists:kompetensi_keahlian,id'],
        ]);

        $kesiswaan->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Siswa berhasil diperbarui.']);
        }

        return redirect()->route('tu.kesiswaan.index')->with('status', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $kesiswaan)
    {
        $kesiswaan->update(['aktif' => 0]);
        $kesiswaan->delete();

        return back()->with('status', 'Siswa berhasil dinonaktifkan.');
    }
}
