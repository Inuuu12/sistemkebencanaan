<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ApiController extends Controller
{
    /**
     * Endpoint pembantu untuk mengambil daftar Kelurahan berdasarkan Kecamatan
     */
    public function getKelurahan(Request $request)
    {
        $kecamatanId = $request->query('kecamatan_id');
        if (!$kecamatanId) {
            return response()->json([]);
        }

        $kelurahan = Kelurahan::where('id_kecamatan', $kecamatanId)
            ->orderBy('nama_kelurahan')
            ->get(['id_kelurahan', 'nama_kelurahan']);

        return response()->json($kelurahan);
    }

    /**
     * Sajikan GeoJSON Batas Wilayah Kabupaten & Kecamatan
     */
    public function getBoundaries(Request $request)
    {
        $level = $request->query('level', 'kecamatan');

        if ($level === 'kabupaten') {
            $path = base_path('database/data/simplified/admin_kab.json');
        } else {
            $path = base_path('database/data/simplified/admin_kec.json');
        }

        if (File::exists($path)) {
            return response()->file($path, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        return response()->json(['type' => 'FeatureCollection', 'features' => []]);
    }
}
