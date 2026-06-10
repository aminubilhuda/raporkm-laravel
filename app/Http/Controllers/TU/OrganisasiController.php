<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Http\Requests\TU\Organisasi\StoreOrganisasiRequest;
use App\Http\Requests\TU\Organisasi\UpdateOrganisasiRequest;
use App\Models\Organisasi as OrganisasiModel;

class OrganisasiController extends Controller
{
    public function index()
    {
        $organisasi = OrganisasiModel::latest()->get();

        return view('tu.organisasi.index', compact('organisasi'));
    }

    public function store(StoreOrganisasiRequest $r)
    {
        $org = OrganisasiModel::create($r->validated());

        activity()->performedOn($org)->event('created')
            ->withProperties(['nama' => $org->nama_organisasi])
            ->log('Organisasi ditambahkan');

        return back()->with('status', 'Organisasi ditambahkan.');
    }

    public function update(UpdateOrganisasiRequest $r, OrganisasiModel $organisasi)
    {
        $organisasi->update($r->validated());

        activity()->performedOn($organisasi)->event('updated')
            ->withProperties(['nama' => $organisasi->nama_organisasi])
            ->log('Organisasi diperbarui');

        return back()->with('status', 'Diperbarui.');
    }

    public function destroy(OrganisasiModel $organisasi)
    {
        $nama = $organisasi->nama_organisasi;
        $organisasi->delete();

        activity()->performedOn($organisasi)->event('deleted')
            ->withProperties(['nama' => $nama])
            ->log('Organisasi dihapus');

        return back()->with('status', 'Dihapus.');
    }
}
