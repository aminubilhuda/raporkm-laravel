<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Organisasi as OrganisasiModel;

class OrganisasiController extends Controller
{
    public function index()
    {
        $organisasi = OrganisasiModel::latest()->get();

        return view('guru.organisasi.index', compact('organisasi'));
    }
}
