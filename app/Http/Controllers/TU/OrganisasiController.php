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
        OrganisasiModel::create($r->validated());

        return back()->with('status', 'Organisasi ditambahkan.');
    }

    public function update(UpdateOrganisasiRequest $r, OrganisasiModel $organisasi)
    {
        $organisasi->update($r->validated());

        return back()->with('status', 'Diperbarui.');
    }

    public function destroy(OrganisasiModel $organisasi)
    {
        $organisasi->delete();

        return back()->with('status', 'Dihapus.');
    }
}
