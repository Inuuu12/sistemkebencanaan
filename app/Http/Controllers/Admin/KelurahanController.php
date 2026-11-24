<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelurahanController extends Controller
{
    /**
     * Tampilkan data master kelurahan & desa.
     */
    public function index(Request $request): View
    {
        $query = Kelurahan::with('kecamatan')->withCount('laporanBencana');

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where('nama_kelurahan', 'like', "%{$q}%");
        }

        if ($request->filled('kecamatan_id')) {
            $query->where('id_kecamatan', $request->kecamatan_id);
        }

        $kelurahanList = $query->orderBy('id_kecamatan')
            ->orderBy('nama_kelurahan')
            ->paginate(15)
            ->withQueryString();

        $totalKelurahan = Kelurahan::count();
        $totalKecamatan = Kecamatan::count();
        $kelurahanTerdampak = Kelurahan::has('laporanBencana')->count();

        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.kelurahan.index', compact(
            'kelurahanList',
            'totalKelurahan',
            'totalKecamatan',
            'kelurahanTerdampak',
            'kecamatanList'
        ));
    }

    /**
     * Simpan data kelurahan/desa baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelurahan' => 'required|string|max:100',
            'id_kecamatan' => 'required|exists:app_md_kecamatan,id_kecamatan',
        ], [
            'nama_kelurahan.required' => 'Nama kelurahan/desa wajib diisi.',
            'id_kecamatan.required' => 'Pilih kecamatan tempat kelurahan berada.',
            'id_kecamatan.exists' => 'Kecamatan yang dipilih tidak valid.',
        ]);

        Kelurahan::create([
            'nama_kelurahan' => trim($validated['nama_kelurahan']),
            'id_kecamatan' => $validated['id_kecamatan'],
        ]);

        return redirect()->route('admin.kelurahan.index')
            ->with('success', 'Data kelurahan/desa "' . $validated['nama_kelurahan'] . '" berhasil ditambahkan.');
    }

    /**
     * Perbarui data kelurahan/desa.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $kelurahan = Kelurahan::findOrFail($id);

        $validated = $request->validate([
            'nama_kelurahan' => 'required|string|max:100',
            'id_kecamatan' => 'required|exists:app_md_kecamatan,id_kecamatan',
        ], [
            'nama_kelurahan.required' => 'Nama kelurahan/desa wajib diisi.',
            'id_kecamatan.required' => 'Pilih kecamatan tempat kelurahan berada.',
            'id_kecamatan.exists' => 'Kecamatan yang dipilih tidak valid.',
        ]);

        $kelurahan->update([
            'nama_kelurahan' => trim($validated['nama_kelurahan']),
            'id_kecamatan' => $validated['id_kecamatan'],
        ]);

        return redirect()->route('admin.kelurahan.index')
            ->with('success', 'Data kelurahan/desa "' . $kelurahan->nama_kelurahan . '" berhasil diperbarui.');
    }

    /**
     * Hapus data kelurahan/desa.
     */
    public function destroy(int $id): RedirectResponse
    {
        $kelurahan = Kelurahan::withCount('laporanBencana')->findOrFail($id);

        if ($kelurahan->laporan_bencana_count > 0) {
            return redirect()->route('admin.kelurahan.index')
                ->withErrors(['error' => 'Kelurahan "' . $kelurahan->nama_kelurahan . '" tidak dapat dihapus karena memiliki ' . $kelurahan->laporan_bencana_count . ' data laporan bencana terkait.']);
        }

        $nama = $kelurahan->nama_kelurahan;
        $kelurahan->delete();

        return redirect()->route('admin.kelurahan.index')
            ->with('success', 'Data kelurahan/desa "' . $nama . '" berhasil dihapus.');
    }
}
