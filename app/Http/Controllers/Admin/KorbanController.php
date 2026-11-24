<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Models\Kecamatan;
use App\Models\Korban;
use App\Models\LaporanBencana;
use Illuminate\Http\Request;

class KorbanController extends Controller
{
    /**
     * Halaman Manajemen & Rekapitulasi Data Korban Bencana
     */
    public function dataKorban(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        $query = Korban::with(['laporanBencana.bencana', 'laporanBencana.kecamatan', 'laporanBencana.kelurahan', 'kelurahan.kecamatan'])
            ->latest('id_korban');

        // Pembatasan wilayah: Admin Kelurahan hanya melihat data korban di kelurahannya
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
                $sub->where('keterangan', 'like', "%{$q}%")
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

        // Filter Jenis Bencana
        if ($request->filled('bencana_id')) {
            $bencanaId = $request->bencana_id;
            $query->whereHas('laporanBencana', function ($l) use ($bencanaId) {
                $l->where('id_bencana', $bencanaId);
            });
        }

        // Filter Kondisi Dampak (meninggal, luka, mengungsi, hilang)
        if ($request->filled('kondisi')) {
            switch ($request->kondisi) {
                case 'meninggal':
                    $query->where('jumlah_meninggal', '>', 0);
                    break;
                case 'luka':
                    $query->where(function ($q) {
                        $q->where('jumlah_luka_berat', '>', 0)
                          ->orWhere('jumlah_luka_ringan', '>', 0);
                    });
                    break;
                case 'mengungsi':
                    $query->where('jumlah_mengungsi', '>', 0);
                    break;
                case 'hilang':
                    $query->where('jumlah_hilang', '>', 0);
                    break;
            }
        }

        $korbanList = $query->paginate(15)->withQueryString();

        // Rekapitulasi Metrik Nyata Korban (Ter-scope)
        $baseKorban = Korban::query();
        if ($isKelurahanAdmin) {
            $baseKorban->where(function ($k) use ($user) {
                $k->where('id_kelurahan', $user->id_kelurahan)
                  ->orWhereHas('laporanBencana', function ($l) use ($user) {
                      $l->where('id_kelurahan', $user->id_kelurahan);
                  });
            });
        }

        $totalMeninggal = (int) (clone $baseKorban)->sum('jumlah_meninggal');
        $totalLukaBerat = (int) (clone $baseKorban)->sum('jumlah_luka_berat');
        $totalLukaRingan = (int) (clone $baseKorban)->sum('jumlah_luka_ringan');
        $totalMengungsi = (int) (clone $baseKorban)->sum('jumlah_mengungsi');
        $totalHilang = (int) (clone $baseKorban)->sum('jumlah_hilang');
        $totalJiwaTerdampak = $totalMeninggal + $totalLukaBerat + $totalLukaRingan + $totalMengungsi + $totalHilang;
        $totalTitikKejadian = (clone $baseKorban)->count();

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

        return view('admin.korban.index', compact(
            'korbanList',
            'totalMeninggal',
            'totalLukaBerat',
            'totalLukaRingan',
            'totalMengungsi',
            'totalHilang',
            'totalJiwaTerdampak',
            'totalTitikKejadian',
            'disasters',
            'districts',
            'laporanOptions',
            'isKelurahanAdmin'
        ));
    }

    /**
     * Simpan data rincian korban baru
     */
    public function storeKorban(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
            'jumlah_meninggal' => 'required|integer|min:0',
            'jumlah_luka_berat' => 'required|integer|min:0',
            'jumlah_luka_ringan' => 'required|integer|min:0',
            'jumlah_mengungsi' => 'required|integer|min:0',
            'jumlah_hilang' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        if ($isKelurahanAdmin) {
            $validated['id_kelurahan'] = $user->id_kelurahan;
        }

        $korban = Korban::create($validated);

        if ($korban->id_laporan) {
            $this->syncLaporanKorbanTotal($korban->id_laporan);
        }

        return redirect()->route('admin.korban.index')
            ->with('success', 'Data rincian korban berhasil dicatat ke sistem.');
    }

    /**
     * Perbarui data rincian korban
     */
    public function updateKorban(Request $request, $id)
    {
        $korban = Korban::findOrFail($id);
        $user = \Illuminate\Support\Facades\Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        if ($isKelurahanAdmin && $korban->id_kelurahan != $user->id_kelurahan) {
            return back()->with('error', 'Akses ditolak: Anda hanya dapat memperbarui data korban pada kelurahan Anda.');
        }

        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
            'jumlah_meninggal' => 'required|integer|min:0',
            'jumlah_luka_berat' => 'required|integer|min:0',
            'jumlah_luka_ringan' => 'required|integer|min:0',
            'jumlah_mengungsi' => 'required|integer|min:0',
            'jumlah_hilang' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        if ($isKelurahanAdmin) {
            $validated['id_kelurahan'] = $user->id_kelurahan;
        }

        $oldLaporanId = $korban->id_laporan;
        $korban->update($validated);

        if ($korban->id_laporan) {
            $this->syncLaporanKorbanTotal($korban->id_laporan);
        }
        if ($oldLaporanId && $oldLaporanId != $korban->id_laporan) {
            $this->syncLaporanKorbanTotal($oldLaporanId);
        }

        return redirect()->route('admin.korban.index')
            ->with('success', 'Data korban #' . $korban->id_korban . ' berhasil diperbarui.');
    }

    /**
     * Hapus data korban
     */
    public function destroyKorban($id)
    {
        $korban = Korban::findOrFail($id);
        $user = \Illuminate\Support\Facades\Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        if ($isKelurahanAdmin && $korban->id_kelurahan != $user->id_kelurahan) {
            return back()->with('error', 'Akses ditolak: Anda hanya dapat menghapus data korban pada kelurahan Anda.');
        }
        $laporanId = $korban->id_laporan;
        $korban->delete();

        if ($laporanId) {
            $this->syncLaporanKorbanTotal($laporanId);
        }

        return redirect()->route('admin.korban.index')
            ->with('success', 'Data korban berhasil dihapus dari sistem.');
    }

    /**
     * Sinkronisasi total jumlah korban pada laporan bencana terkait
     */
    private function syncLaporanKorbanTotal($idLaporan)
    {
        $laporan = LaporanBencana::find($idLaporan);
        if ($laporan) {
            $total = Korban::where('id_laporan', $idLaporan)->get()->sum(function ($k) {
                return $k->jumlah_meninggal + $k->jumlah_luka_berat + $k->jumlah_luka_ringan + $k->jumlah_mengungsi + $k->jumlah_hilang;
            });
            $laporan->jumlah_korban = $total;
            $laporan->save();
        }
    }
}
