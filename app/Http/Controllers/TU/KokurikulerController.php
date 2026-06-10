<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Kokurikuler\StoreDeskripsiKokurikulerRequest;
use App\Http\Requests\TU\Kokurikuler\StoreDimensiKokurikulerRequest;
use App\Http\Requests\TU\Kokurikuler\UpdateDeskripsiKokurikulerRequest;
use App\Http\Requests\TU\Kokurikuler\UpdateDimensiKokurikulerRequest;
use App\Models\DeskripsiKokurikuler;
use App\Models\DimensiKokurikuler;

class KokurikulerController extends Controller
{
    public function index()
    {
        $dimensi = DimensiKokurikuler::with('deskripsiKokurikuler')->latest()->get();
        $totalDeskripsi = DeskripsiKokurikuler::count();

        return view('tu.kokurikuler.index', compact('dimensi', 'totalDeskripsi'));
    }

    public function dimensiStore(StoreDimensiKokurikulerRequest $r)
    {
        $dimensi = DimensiKokurikuler::create($r->validated());

        activity()->performedOn($dimensi)->event('created')
            ->withProperties(['nama' => $dimensi->nama])
            ->log('Dimensi kokurikuler ditambahkan');

        return back()->with('status', 'Dimensi kokurikuler ditambahkan.');
    }

    public function dimensiUpdate(UpdateDimensiKokurikulerRequest $r, DimensiKokurikuler $dimensiKokurikuler)
    {
        $dimensiKokurikuler->update($r->validated());

        activity()->performedOn($dimensiKokurikuler)->event('updated')
            ->withProperties(['nama' => $dimensiKokurikuler->nama])
            ->log('Dimensi kokurikuler diperbarui');

        return back()->with('status', 'Diperbarui.');
    }

    public function dimensiDestroy(DimensiKokurikuler $dimensiKokurikuler)
    {
        $nama = $dimensiKokurikuler->nama;
        $dimensiKokurikuler->delete();

        activity()->performedOn($dimensiKokurikuler)->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Dimensi kokurikuler dihapus');

        return back()->with('status', 'Dihapus.');
    }

    public function deskripsiStore(StoreDeskripsiKokurikulerRequest $r)
    {
        $deskripsi = DeskripsiKokurikuler::create($r->validated());

        activity()->performedOn($deskripsi)->event('created')
            ->withProperties(['predikat' => $deskripsi->predikat])
            ->log('Deskripsi kokurikuler ditambahkan');

        return back()->with('status', 'Deskripsi ditambahkan.');
    }

    public function deskripsiUpdate(UpdateDeskripsiKokurikulerRequest $r, DeskripsiKokurikuler $deskripsiKokurikuler)
    {
        $deskripsiKokurikuler->update($r->validated());

        activity()->performedOn($deskripsiKokurikuler)->event('updated')
            ->withProperties(['predikat' => $deskripsiKokurikuler->predikat])
            ->log('Deskripsi kokurikuler diperbarui');

        return back()->with('status', 'Diperbarui.');
    }

    public function deskripsiDestroy(DeskripsiKokurikuler $deskripsiKokurikuler)
    {
        $predikat = $deskripsiKokurikuler->predikat;
        $deskripsiKokurikuler->delete();

        activity()->performedOn($deskripsiKokurikuler)->event('deleted')
            ->withProperties(['predikat' => $predikat])
            ->log('Deskripsi kokurikuler dihapus');

        return back()->with('status', 'Dihapus.');
    }
}
