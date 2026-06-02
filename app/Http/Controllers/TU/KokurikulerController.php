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

        return view('tu.kokurikuler.index', compact('dimensi'));
    }

    public function dimensiStore(StoreDimensiKokurikulerRequest $r)
    {
        DimensiKokurikuler::create($r->validated());

        return back()->with('status', 'Dimensi kokurikuler ditambahkan.');
    }

    public function dimensiUpdate(UpdateDimensiKokurikulerRequest $r, DimensiKokurikuler $dimensiKokurikuler)
    {
        $dimensiKokurikuler->update($r->validated());

        return back()->with('status', 'Diperbarui.');
    }

    public function dimensiDestroy(DimensiKokurikuler $dimensiKokurikuler)
    {
        $dimensiKokurikuler->delete();

        return back()->with('status', 'Dihapus.');
    }

    public function deskripsiStore(StoreDeskripsiKokurikulerRequest $r)
    {
        DeskripsiKokurikuler::create($r->validated());

        return back()->with('status', 'Deskripsi ditambahkan.');
    }

    public function deskripsiUpdate(UpdateDeskripsiKokurikulerRequest $r, DeskripsiKokurikuler $deskripsiKokurikuler)
    {
        $deskripsiKokurikuler->update($r->validated());

        return back()->with('status', 'Diperbarui.');
    }

    public function deskripsiDestroy(DeskripsiKokurikuler $deskripsiKokurikuler)
    {
        $deskripsiKokurikuler->delete();

        return back()->with('status', 'Dihapus.');
    }
}
