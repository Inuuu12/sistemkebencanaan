<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Models\DampakKerusakan;
use App\Models\Kecamatan;
use App\Models\LaporanBencana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KerusakanController extends Controller
{
    /**
     * Manajemen Data Kerusakan Infrastruktur & Bangunan
     */
    public function dataKerusakan(Request $request)
    {
        $query = DampakKerusakan::with(['laporanBencana.bencana', 'laporanBencana.kecamatan', 'laporanBencana.kelurahan', 'kelurahan.kecamatan', 'recorder'])
            ->latest('id_dampak');

        // Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('jenis_kerusakan', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhereHas('laporanBencana', function ($l) use ($q) {
                        $l->where('judul', 'like', "%{$q}%")
                          ->orWhere('alamat_detail', 'like', "%{$q}%");
                    })
                    ->orWhereHas('kelurahan', function ($k) use ($q) {
                        $k->where('nama_kelurahan', 'like', "%{$q}%")
                          ->orWhereHas('kecamatan', function ($kc) use ($q) {
                              $kc->where('nama_kecamatan', 'like', "%{$q}%");
                          });
                    });
            });
        }

        // Filter Kecamatan
        if ($request->filled('kecamatan_id')) {
            $kecId = $request->kecamatan_id;
            $query->where(function ($q) use ($kecId) {
                $q->where('id_kelurahan', function ($sub) use ($kecId) {
                    $sub->select('id_kelurahan')->from('app_kelurahan')->where('id_kecamatan', $kecId)->limit(1);
                })->orWhereHas('kelurahan', function ($k) use ($kecId) {
                    $k->where('id_kecamatan', $kecId);
                })->orWhereHas('laporanBencana', function ($l) use ($kecId) {
                    $l->where('id_kecamatan', $kecId);
                });
            });
        }

        // Filter Tingkat Kerusakan
        if ($request->filled('tingkat')) {
            $query->where('tingkat_kerusakan', $request->tingkat);
        }

        // Filter Jenis Bencana
        if ($request->filled('bencana_id')) {
            $bencanaId = $request->bencana_id;
            $query->whereHas('laporanBencana', function ($l) use ($bencanaId) {
                $l->where('id_bencana', $bencanaId);
            });
        }

        $kerusakanList = $query->paginate(15)->withQueryString();

        // Metrik Ringkasan Nyata Kerusakan
        $totalUnit = (int) DampakKerusakan::sum('jumlah_unit');
        $totalRusakBerat = (int) DampakKerusakan::where('tingkat_kerusakan', 'Berat')->sum('jumlah_unit');
        $totalRusakSedang = (int) DampakKerusakan::where('tingkat_kerusakan', 'Sedang')->sum('jumlah_unit');
        $totalRusakRingan = (int) DampakKerusakan::where('tingkat_kerusakan', 'Ringan')->sum('jumlah_unit');
        $totalEstimasiKerugian = (float) DampakKerusakan::sum('estimasi_kerugian');
        $totalInsidenKerusakan = DampakKerusakan::count();

        // Dropdown Data
        $disasters = Bencana::orderBy('nama_bencana')->get();
        $districts = Kecamatan::with('kelurahan')->orderBy('nama_kecamatan')->get();
        $laporanOptions = LaporanBencana::select('id_laporan', 'judul', 'id_kecamatan', 'id_kelurahan')
            ->with(['kecamatan:id_kecamatan,nama_kecamatan', 'kelurahan:id_kelurahan,nama_kelurahan'])
            ->latest('id_laporan')
            ->take(50)
            ->get();

        return view('admin.kerusakan.index', compact(
            'kerusakanList',
            'totalUnit',
            'totalRusakBerat',
            'totalRusakSedang',
            'totalRusakRingan',
            'totalEstimasiKerugian',
            'totalInsidenKerusakan',
            'disasters',
            'districts',
            'laporanOptions'
        ));
    }

    public function storeKerusakan(Request $request)
    {
        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'required|exists:app_kelurahan,id_kelurahan',
            'jenis_kerusakan' => 'required|string|max:100',
            'tingkat_kerusakan' => 'required|in:Berat,Sedang,Ringan',
            'jumlah_unit' => 'required|integer|min:1',
            'estimasi_kerugian' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $validated['dicatat_oleh'] = Auth::id() ?: 1;
        $validated['estimasi_kerugian'] = $validated['estimasi_kerugian'] ?? 0;

        DampakKerusakan::create($validated);

        return redirect()->route('admin.kerusakan.index')
            ->with('success', 'Data dampak kerusakan infrastruktur berhasil dicatat.');
    }

    public function updateKerusakan(Request $request, $id)
    {
        $dampak = DampakKerusakan::findOrFail($id);

        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'required|exists:app_kelurahan,id_kelurahan',
            'jenis_kerusakan' => 'required|string|max:100',
            'tingkat_kerusakan' => 'required|in:Berat,Sedang,Ringan',
            'jumlah_unit' => 'required|integer|min:1',
            'estimasi_kerugian' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $validated['estimasi_kerugian'] = $validated['estimasi_kerugian'] ?? 0;
        $dampak->update($validated);

        return redirect()->route('admin.kerusakan.index')
            ->with('success', 'Data kerusakan #' . $dampak->id_dampak . ' berhasil diperbarui.');
    }

    public function destroyKerusakan($id)
    {
        $dampak = DampakKerusakan::findOrFail($id);
        $dampak->delete();

        return redirect()->route('admin.kerusakan.index')
            ->with('success', 'Data kerusakan berhasil dihapus.');
    }
}
