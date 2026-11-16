<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\LaporanBencana;
use App\Models\PoskoPengungsian;
use Illuminate\Http\Request;

class PengungsianController extends Controller
{
    /**
     * Manajemen Data Pengungsian & Posko Evakuasi
     */
    public function dataPengungsian(Request $request)
    {
        $query = PoskoPengungsian::with(['laporanBencana.bencana', 'kecamatan', 'kelurahan'])
            ->latest('id_posko');

        // Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_posko', 'like', "%{$q}%")
                    ->orWhere('alamat_lengkap', 'like', "%{$q}%")
                    ->orWhere('pj_nama', 'like', "%{$q}%")
                    ->orWhere('fasilitas', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }

        // Filter Kecamatan
        if ($request->filled('kecamatan_id')) {
            $query->where('id_kecamatan', $request->kecamatan_id);
        }

        // Filter Status Posko
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $poskoList = $query->paginate(15)->withQueryString();

        // Metrik Ringkasan Pengungsian
        $totalPosko = PoskoPengungsian::count();
        $poskoAktif = PoskoPengungsian::where('status', 'Aktif')->count();
        $totalPengungsi = (int) PoskoPengungsian::sum('jumlah_pengungsi');
        $totalKapasitas = (int) PoskoPengungsian::sum('kapasitas_maksimal');
        $totalBalita = (int) PoskoPengungsian::sum('jumlah_balita');
        $totalLansia = (int) PoskoPengungsian::sum('jumlah_lansia');

        // Dropdown Data
        $districts = Kecamatan::with('kelurahan')->orderBy('nama_kecamatan')->get();
        $laporanOptions = LaporanBencana::select('id_laporan', 'judul', 'id_kecamatan', 'id_kelurahan')
            ->with(['kecamatan:id_kecamatan,nama_kecamatan', 'kelurahan:id_kelurahan,nama_kelurahan'])
            ->latest('id_laporan')
            ->take(50)
            ->get();

        return view('admin.pengungsian.index', compact(
            'poskoList',
            'totalPosko',
            'poskoAktif',
            'totalPengungsi',
            'totalKapasitas',
            'totalBalita',
            'totalLansia',
            'districts',
            'laporanOptions'
        ));
    }

    public function storePengungsian(Request $request)
    {
        $validated = $request->validate([
            'nama_posko' => 'required|string|max:150',
            'jenis_posko' => 'required|string|max:100',
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kecamatan' => 'required|exists:app_kecamatan,id_kecamatan',
            'id_kelurahan' => 'nullable|exists:app_kelurahan,id_kelurahan',
            'alamat_lengkap' => 'required|string|max:1000',
            'kapasitas_maksimal' => 'required|integer|min:1',
            'jumlah_pengungsi' => 'required|integer|min:0',
            'jumlah_balita' => 'nullable|integer|min:0',
            'jumlah_lansia' => 'nullable|integer|min:0',
            'fasilitas' => 'nullable|string|max:1000',
            'pj_nama' => 'nullable|string|max:100',
            'pj_kontak' => 'nullable|string|max:50',
            'status' => 'required|in:Aktif,Penuh,Standby,Ditutup',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $validated['jumlah_balita'] = $validated['jumlah_balita'] ?? 0;
        $validated['jumlah_lansia'] = $validated['jumlah_lansia'] ?? 0;

        PoskoPengungsian::create($validated);

        return redirect()->route('admin.pengungsian.index')
            ->with('success', 'Data titik posko pengungsian berhasil ditambahkan.');
    }

    public function updatePengungsian(Request $request, $id)
    {
        $posko = PoskoPengungsian::findOrFail($id);

        $validated = $request->validate([
            'nama_posko' => 'required|string|max:150',
            'jenis_posko' => 'required|string|max:100',
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kecamatan' => 'required|exists:app_kecamatan,id_kecamatan',
            'id_kelurahan' => 'nullable|exists:app_kelurahan,id_kelurahan',
            'alamat_lengkap' => 'required|string|max:1000',
            'kapasitas_maksimal' => 'required|integer|min:1',
            'jumlah_pengungsi' => 'required|integer|min:0',
            'jumlah_balita' => 'nullable|integer|min:0',
            'jumlah_lansia' => 'nullable|integer|min:0',
            'fasilitas' => 'nullable|string|max:1000',
            'pj_nama' => 'nullable|string|max:100',
            'pj_kontak' => 'nullable|string|max:50',
            'status' => 'required|in:Aktif,Penuh,Standby,Ditutup',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $validated['jumlah_balita'] = $validated['jumlah_balita'] ?? 0;
        $validated['jumlah_lansia'] = $validated['jumlah_lansia'] ?? 0;

        $posko->update($validated);

        return redirect()->route('admin.pengungsian.index')
            ->with('success', 'Data posko pengungsian #' . $posko->id_posko . ' berhasil diperbarui.');
    }

    public function destroyPengungsian($id)
    {
        $posko = PoskoPengungsian::findOrFail($id);
        $posko->delete();

        return redirect()->route('admin.pengungsian.index')
            ->with('success', 'Data posko pengungsian berhasil dihapus.');
    }
}
