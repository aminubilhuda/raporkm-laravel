<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\DimensiKokurikuler;

class KokurikulerController extends Controller
{
    public function index()
    {
        $dimensiList = DimensiKokurikuler::orderBy('nama')->get();

        return view('guru.kokurikuler.index', compact('dimensiList'));
    }
}
