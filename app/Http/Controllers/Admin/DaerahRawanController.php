<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Models\DaerahRawan;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DaerahRawanController extends Controller
{
    /**
     * Tampilkan data zonasi titik rawan bencana (GIS Layer).
     */
    public function index(Request $request): View
    {
        $query = DaerahRawan::with(['kelurahan.kecamatan', 'bencana']);

        if ($request->filled('bencana_id')) {
            $query->where('id_bencana', $request->bencana_id);
        }

        if ($request->filled('zona')) {
            $query->where('zona', $request->zona);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('keterangan', 'like', "%{$q}%")
                    ->orWhereHas('kelurahan', function ($k) use ($q) {
                        $k->where('nama_kelurahan', 'like', "%{$q}%")
                            ->orWhereHas('kecamatan', function ($kc) use ($q) {
                                $kc->where('nama_kecamatan', 'like', "%{$q}%");
                            });
                    });
            });
        }

        $rawanList = $query->orderByDesc('id_daerah_rawan')->paginate(15)->withQueryString();

        $totalTitik = DaerahRawan::count();
        $zonaTinggi = DaerahRawan::whereIn('zona', ['Tinggi', 'Merah'])->count();
        $zonaSedang = DaerahRawan::whereIn('zona', ['Sedang', 'Kuning'])->count();
        $zonaRendah = DaerahRawan::whereIn('zona', ['Rendah', 'Hijau'])->count();

        $bencanaList = Bencana::orderBy('nama_bencana')->get();
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();
        $kelurahanList = Kelurahan::orderBy('nama_kelurahan')->get();

        return view('admin.rawan.index', compact(
            'rawanList',
            'totalTitik',
            'zonaTinggi',
            'zonaSedang',
            'zonaRendah',
            'bencanaList',
            'kecamatanList',
            'kelurahanList'
        ));
    }

    /**
     * Simpan data titik rawan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_bencana' => 'required|exists:app_md_bencana,id_bencana',
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
            'zona' => 'required|string|max:50',
            'keterangan' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_meter' => 'nullable|integer|min:0',
        ], [
            'id_bencana.required' => 'Pilih jenis potensi bencana.',
            'id_kelurahan.required' => 'Pilih lokasi kelurahan/desa.',
            'zona.required' => 'Pilih tingkat zonasi kerawanan.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        DaerahRawan::create($validated);

        return redirect()->route('admin.rawan.index')
            ->with('success', 'Titik zonasi rawan bencana berhasil ditambahkan.');
    }

    /**
     * Perbarui data titik rawan.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $rawan = DaerahRawan::findOrFail($id);

        $validated = $request->validate([
            'id_bencana' => 'required|exists:app_md_bencana,id_bencana',
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
            'zona' => 'required|string|max:50',
            'keterangan' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_meter' => 'nullable|integer|min:0',
        ], [
            'id_bencana.required' => 'Pilih jenis potensi bencana.',
            'id_kelurahan.required' => 'Pilih lokasi kelurahan/desa.',
            'zona.required' => 'Pilih tingkat zonasi kerawanan.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $rawan->update($validated);

        return redirect()->route('admin.rawan.index')
            ->with('success', 'Data titik zonasi rawan berhasil diperbarui.');
    }

    /**
     * Hapus data titik rawan.
     */
    public function destroy(int $id): RedirectResponse
    {
        $rawan = DaerahRawan::findOrFail($id);
        $rawan->delete();

        return redirect()->route('admin.rawan.index')
            ->with('success', 'Titik zonasi rawan bencana berhasil dihapus.');
    }
}
