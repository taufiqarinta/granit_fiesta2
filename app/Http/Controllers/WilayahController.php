<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function getKabupaten(Request $request)
    {
        $kodeProvinsi = $request->kode_provinsi;

        $kabupaten = DB::table('wilayah')
            ->select('kode', 'nama')
            ->whereRaw('CHAR_LENGTH(kode) = 5')
            ->whereRaw('LEFT(kode, 2) = ?', [$kodeProvinsi])
            ->get();

        return response()->json($kabupaten);
    }
}
