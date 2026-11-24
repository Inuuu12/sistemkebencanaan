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
        $user = Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        $query = DampakKerusakan::with(['laporanBencana.bencana', 'laporanBencana.kecamatan', 'laporanBencana.kelurahan', 'kelurahan.kecamatan', 'recorder'])
            ->latest('id_dampak');

        // Scoping wilayah: Admin Kelurahan hanya melihat kerusakan di kelurahannya
        if ($isKelurahanAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('id_kelurahan', $user->id_kelurahan)
                  ->orWhereHas('laporanBencana', function ($l) use ($user) {
                      $l->where('id_kelurahan', $user->id_kelurahan);
                  });
            });
        }

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
                $q->whereHas('kelurahan', function ($k) use ($kecId) {
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

        // Metrik Ringkasan Nyata Kerusakan (Ter-scope)
        $baseKerusakan = DampakKerusakan::query();
        if ($isKelurahanAdmin) {
            $baseKerusakan->where(function ($k) use ($user) {
                $k->where('id_kelurahan', $user->id_kelurahan)
                  ->orWhereHas('laporanBencana', function ($l) use ($user) {
                      $l->where('id_kelurahan', $user->id_kelurahan);
                  });
            });
        }

        $totalUnit = (int) (clone $baseKerusakan)->sum('jumlah_unit');
        $totalRusakBerat = (int) (clone $baseKerusakan)->where('tingkat_kerusakan', 'Berat')->sum('jumlah_unit');
        $totalRusakSedang = (int) (clone $baseKerusakan)->where('tingkat_kerusakan', 'Sedang')->sum('jumlah_unit');
        $totalRusakRingan = (int) (clone $baseKerusakan)->where('tingkat_kerusakan', 'Ringan')->sum('jumlah_unit');
        $totalEstimasiKerugian = (float) (clone $baseKerusakan)->sum('estimasi_kerugian');
        $totalInsidenKerusakan = (clone $baseKerusakan)->count();

        // Dropdown Data
        $disasters = Bencana::orderBy('nama_bencana')->get();
        $districts = Kecamatan::with('kelurahan')->orderBy('nama_kecamatan')->get();
        $laporanQuery = LaporanBencana::select('id_laporan', 'judul', 'id_kecamatan', 'id_kelurahan')
            ->with(['kecamatan:id_kecamatan,nama_kecamatan', 'kelurahan:id_kelurahan,nama_kelurahan'])
            ->latest('id_laporan');
        if ($isKelurahanAdmin) {
            $laporanQuery->where('id_kelurahan', $user->id_kelurahan);
        }
        $laporanOptions = $laporanQuery->take(50)->get();

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
            'laporanOptions',
            'isKelurahanAdmin'
        ));
    }

    public function storeKerusakan(Request $request)
    {
        $user = Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
            'jenis_kerusakan' => 'required|string|max:100',
            'tingkat_kerusakan' => 'required|in:Berat,Sedang,Ringan',
            'jumlah_unit' => 'required|integer|min:1',
            'estimasi_kerugian' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        if ($isKelurahanAdmin) {
            $validated['id_kelurahan'] = $user->id_kelurahan;
        }

        $validated['dicatat_oleh'] = Auth::id() ?: 1;
        $validated['estimasi_kerugian'] = $validated['estimasi_kerugian'] ?? 0;

        DampakKerusakan::create($validated);

        return redirect()->route('admin.kerusakan.index')
            ->with('success', 'Data dampak kerusakan infrastruktur berhasil dicatat.');
    }

    public function updateKerusakan(Request $request, $id)
    {
        $dampak = DampakKerusakan::findOrFail($id);
        $user = Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        if ($isKelurahanAdmin && $dampak->id_kelurahan != $user->id_kelurahan) {
            return back()->with('error', 'Akses ditolak: Anda hanya dapat memperbarui data kerusakan pada kelurahan Anda.');
        }

        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
            'jenis_kerusakan' => 'required|string|max:100',
            'tingkat_kerusakan' => 'required|in:Berat,Sedang,Ringan',
            'jumlah_unit' => 'required|integer|min:1',
            'estimasi_kerugian' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        if ($isKelurahanAdmin) {
            $validated['id_kelurahan'] = $user->id_kelurahan;
        }

        $validated['estimasi_kerugian'] = $validated['estimasi_kerugian'] ?? 0;
        $dampak->update($validated);

        return redirect()->route('admin.kerusakan.index')
            ->with('success', 'Data kerusakan #' . $dampak->id_dampak . ' berhasil diperbarui.');
    }

    public function destroyKerusakan($id)
    {
        $dampak = DampakKerusakan::findOrFail($id);
        $user = Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        if ($isKelurahanAdmin && $dampak->id_kelurahan != $user->id_kelurahan) {
            return back()->with('error', 'Akses ditolak: Anda hanya dapat menghapus data kerusakan pada kelurahan Anda.');
        }

        $dampak->delete();

        return redirect()->route('admin.kerusakan.index')
            ->with('success', 'Data kerusakan berhasil dihapus.');
    }
}
