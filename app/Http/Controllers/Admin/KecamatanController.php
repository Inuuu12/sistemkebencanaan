<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\LaporanBencana;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KecamatanController extends Controller
{
    /**
     * Tampilkan data master kecamatan se-Kabupaten Bogor.
     */
    public function index(Request $request): View
    {
        $query = Kecamatan::withCount(['kelurahan', 'laporanBencana']);

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where('nama_kecamatan', 'like', "%{$q}%");
        }

        $sort = $request->get('sort', 'nama');
        switch ($sort) {
            case 'terbanyak_kelurahan':
                $query->orderByDesc('kelurahan_count');
                break;
            case 'terbanyak_laporan':
                $query->orderByDesc('laporan_bencana_count');
                break;
            default:
                $query->orderBy('nama_kecamatan', 'asc');
                break;
        }

        $kecamatanList = $query->paginate(15)->withQueryString();

        $totalKecamatan = Kecamatan::count();
        $totalKelurahan = Kelurahan::count();
        $kecamatanTerdampak = Kecamatan::has('laporanBencana')->count();
        $totalLaporan = LaporanBencana::count();

        return view('admin.kecamatan.index', compact(
            'kecamatanList',
            'totalKecamatan',
            'totalKelurahan',
            'kecamatanTerdampak',
            'totalLaporan'
        ));
    }

    /**
     * Simpan data kecamatan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kecamatan' => 'required|string|max:100|unique:app_md_kecamatan,nama_kecamatan',
        ], [
            'nama_kecamatan.required' => 'Nama kecamatan wajib diisi.',
            'nama_kecamatan.unique' => 'Kecamatan dengan nama tersebut sudah ada di sistem.',
        ]);

        Kecamatan::create([
            'nama_kecamatan' => trim($validated['nama_kecamatan']),
            'id_kabupaten' => 1,
        ]);

        return redirect()->route('admin.kecamatan.index')
            ->with('success', 'Kecamatan "' . $validated['nama_kecamatan'] . '" berhasil ditambahkan.');
    }

    /**
     * Perbarui data kecamatan.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $kecamatan = Kecamatan::findOrFail($id);

        $validated = $request->validate([
            'nama_kecamatan' => 'required|string|max:100|unique:app_md_kecamatan,nama_kecamatan,' . $id . ',id_kecamatan',
        ], [
            'nama_kecamatan.required' => 'Nama kecamatan wajib diisi.',
            'nama_kecamatan.unique' => 'Kecamatan dengan nama tersebut sudah ada di sistem.',
        ]);

        $kecamatan->update([
            'nama_kecamatan' => trim($validated['nama_kecamatan']),
        ]);

        return redirect()->route('admin.kecamatan.index')
            ->with('success', 'Data kecamatan "' . $kecamatan->nama_kecamatan . '" berhasil diperbarui.');
    }

    /**
     * Hapus data kecamatan.
     */
    public function destroy(int $id): RedirectResponse
    {
        $kecamatan = Kecamatan::withCount(['kelurahan', 'laporanBencana'])->findOrFail($id);

        if ($kecamatan->kelurahan_count > 0 || $kecamatan->laporan_bencana_count > 0) {
            $alasan = [];
            if ($kecamatan->kelurahan_count > 0) {
                $alasan[] = $kecamatan->kelurahan_count . ' desa/kelurahan terdaftar di dalamnya';
            }
            if ($kecamatan->laporan_bencana_count > 0) {
                $alasan[] = $kecamatan->laporan_bencana_count . ' laporan bencana';
            }
            return redirect()->route('admin.kecamatan.index')
                ->withErrors(['error' => 'Kecamatan "' . $kecamatan->nama_kecamatan . '" tidak dapat dihapus karena masih menaungi ' . implode(' dan ', $alasan) . '.']);
        }

        $nama = $kecamatan->nama_kecamatan;
        $kecamatan->delete();

        return redirect()->route('admin.kecamatan.index')
            ->with('success', 'Kecamatan "' . $nama . '" berhasil dihapus.');
    }
}
