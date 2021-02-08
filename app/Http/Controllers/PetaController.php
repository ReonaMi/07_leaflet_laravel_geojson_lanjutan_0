<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kecamatan;
use App\Models\Desa;

class PetaController extends Controller
{
    public function peta()
    {
        $kecamatan = Kecamatan::all();
        $desa = Desa::all();
        return view('peta', [
            'kecamatan' => $kecamatan,
            'desa' => $desa
        ]);
    }
}
