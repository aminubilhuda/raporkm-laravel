<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Prakerin\ImportPrakerinRequest;
use App\Http\Requests\TU\Prakerin\StorePrakerinRequest;
use App\Http\Requests\TU\Prakerin\StoreSiswaPrakerinRequest;
use App\Http\Requests\TU\Prakerin\UpdatePrakerinRequest;
use App\Models\Kelas;
use App\Models\Prakerin;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\SiswaPrakerin;
use App\Models\User;
use App\Services\ImportService;
use Illuminate\Http\Request;

class PrakerinController extends Controller
{
    public function __construct(private ImportService $import) {}

    public function index()
    {
        $prakerins = Prakerin::latest()->paginate(15);
        $kelass = Kelas::orderBy('nama_kelas')->get();

        return view('tu.prakerin.index', compact('prakerins', 'kelass'));
    }

    public function import()
    {
        return view('tu.prakerin.import');
    }

    public function doImport(ImportPrakerinRequest $r)
    {
        $sekolah = Sekolah::first();
        $result = $this->import->importPrakerin(
            $r->file('file'),
            session('selected_tahun', $sekolah?->tahun_aktif),
            session('selected_semester', $sekolah?->semester_aktif),
        );

        return redirect()->route('tu.prakerin.import')
            ->with('import_result', $result);
    }

    public function store(StorePrakerinRequest $r)
    {
        $prakerin = Prakerin::create($r->validated());

        activity()->performedOn($prakerin)->event('created')
            ->withProperties(['nama' => $prakerin->nama_perusahaan])
            ->log('Data prakerin ditambahkan');

        return back()->with('status', 'Prakerin ditambahkan.');
    }

    public function update(UpdatePrakerinRequest $r, Prakerin $prakerin)
    {
        $prakerin->update($r->validated());

        activity()->performedOn($prakerin)->event('updated')
            ->withProperties(['nama' => $prakerin->nama_perusahaan])
            ->log('Data prakerin diperbarui');

        return back()->with('status', 'Prakerin diperbarui.');
    }

    public function destroy(Prakerin $prakerin)
    {
        $nama = $prakerin->nama_perusahaan;
        $prakerin->delete();

        activity()->performedOn($prakerin)->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Data prakerin dihapus');

        return back()->with('status', 'Prakerin dihapus.');
    }

    public function peserta(Request $r)
    {
        $prakerinId = $r->get('prakerin_id');
        $peserta = collect();
        $siswas = Siswa::where('aktif', 1)->orderBy('nama_siswa')->get();
        $gurus = User::where('jabatan', 3)->orderBy('nama')->get();
        if ($prakerinId) {
            $peserta = SiswaPrakerin::with(['siswa', 'kelas', 'user'])->where('prakerin_id', $prakerinId)->get();
        }

        return view('tu.prakerin.peserta', compact('prakerinId', 'peserta', 'siswas', 'gurus'));
    }

    public function pesertaStore(StoreSiswaPrakerinRequest $r)
    {
        $sp = SiswaPrakerin::create($r->validated());

        activity()->performedOn($sp)->event('created')
            ->withProperties([
                'siswa' => $sp->siswa->nama_siswa ?? '',
                'perusahaan' => $sp->prakerin->nama_perusahaan ?? '',
            ])
            ->log('Peserta prakerin ditambahkan');

        return back()->with('status', 'Peserta ditambahkan.');
    }

    public function pesertaDestroy(SiswaPrakerin $siswaPrakerin)
    {
        $siswaName = $siswaPrakerin->siswa->nama_siswa ?? '';
        $perusahaan = $siswaPrakerin->prakerin->nama_perusahaan ?? '';
        $siswaPrakerin->delete();

        activity()->performedOn($siswaPrakerin)->event('deleted')
            ->withProperties([
                'siswa' => $siswaName,
                'perusahaan' => $perusahaan,
            ])
            ->log('Peserta prakerin dihapus');

        return back()->with('status', 'Peserta dihapus.');
    }
}
