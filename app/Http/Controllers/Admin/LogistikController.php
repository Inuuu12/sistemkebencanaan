<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DistribusiBantuan;
use App\Models\KategoriStok;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\LaporanBencana;
use App\Models\StokBantuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogistikController extends Controller
{
    /**
     * Manajemen Gudang & Stok Bantuan Logistik
     */
    public function stokBantuan(Request $request)
    {
        // Pastikan ada kategori dasar jika tabel kategori masih kosong
        if (KategoriStok::count() === 0) {
            $defaultKategori = [
                ['nama_kategori' => 'Makanan & Sembako', 'satuan' => 'Dus', 'deskripsi' => 'Beras, mie instan, makanan kaleng, biskuit, air mineral'],
                ['nama_kategori' => 'Kesehatan & Obat', 'satuan' => 'Box', 'deskripsi' => 'P3K, masker, perban, minyak angin, obat-obatan darurat'],
                ['nama_kategori' => 'Sandang & Selimut', 'satuan' => 'Lembar', 'deskripsi' => 'Selimut tebal, pakaian layak, sarung, pakaian dalam'],
                ['nama_kategori' => 'Perlengkapan Bayi', 'satuan' => 'Paket', 'deskripsi' => 'Popok bayi, susu formula, bubur bayi, minyak telon'],
                ['nama_kategori' => 'Tenda & Matras', 'satuan' => 'Unit', 'deskripsi' => 'Tenda pleton, tenda keluarga, velbed, matras tidur'],
                ['nama_kategori' => 'Kebersihan & Sanitasi', 'satuan' => 'Paket', 'deskripsi' => 'Sabun, pasta gigi, pembersih lantai, detergen, ember'],
            ];
            foreach ($defaultKategori as $cat) {
                KategoriStok::create($cat);
            }
        }

        $query = StokBantuan::with(['kategoriStok', 'recorder'])
            ->latest('tanggal');

        // Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_item', 'like', "%{$q}%")
                    ->orWhere('sumber', 'like', "%{$q}%")
                    ->orWhere('tujuan', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }

        // Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->where('id_kategori', $request->kategori_id);
        }

        // Filter Tipe (MASUK / KELUAR)
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $stokList = $query->paginate(10)->withQueryString();

        // Metrik Statistik
        $totalItem = StokBantuan::count();
        $totalMasuk = StokBantuan::where('tipe', 'MASUK')->sum('jumlah');
        $totalKeluar = StokBantuan::where('tipe', 'KELUAR')->sum('jumlah');
        $totalKategori = KategoriStok::count();
        $totalTersalur = DistribusiBantuan::sum('jumlah');

        $kategoriList = KategoriStok::orderBy('nama_kategori')->get();

        return view('admin.logistik.stok', compact(
            'stokList',
            'totalItem',
            'totalMasuk',
            'totalKeluar',
            'totalKategori',
            'totalTersalur',
            'kategoriList'
        ));
    }

    public function storeStokBantuan(Request $request)
    {
        $validated = $request->validate([
            'nama_item' => 'required|string|max:150',
            'id_kategori' => 'required|exists:app_md_kategori_stok,id_kategori',
            'tipe' => 'required|in:MASUK,KELUAR',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'tanggal' => 'required|date',
            'sumber' => 'nullable|string|max:150',
            'tujuan' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'nama_item.required' => 'Nama barang logistik wajib diisi.',
            'id_kategori.required' => 'Pilih kategori logistik bantuan.',
            'jumlah.min' => 'Jumlah barang minimal 1.',
        ]);

        $validated['dicatat_oleh'] = Auth::id();

        StokBantuan::create($validated);

        return redirect()->route('admin.logistik.stok')
            ->with('success', 'Catatan stok bantuan "' . $validated['nama_item'] . '" berhasil disimpan.');
    }

    public function updateStokBantuan(Request $request, $id)
    {
        $stok = StokBantuan::findOrFail($id);

        $validated = $request->validate([
            'nama_item' => 'required|string|max:150',
            'id_kategori' => 'required|exists:app_md_kategori_stok,id_kategori',
            'tipe' => 'required|in:MASUK,KELUAR',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'tanggal' => 'required|date',
            'sumber' => 'nullable|string|max:150',
            'tujuan' => 'nullable|string|max:150',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $stok->update($validated);

        return redirect()->route('admin.logistik.stok')
            ->with('success', 'Data stok bantuan #' . $stok->id_stok . ' berhasil diperbarui.');
    }

    public function destroyStokBantuan($id)
    {
        $stok = StokBantuan::withCount('distribusiBantuan')->findOrFail($id);

        if ($stok->distribusi_bantuan_count > 0) {
            return redirect()->route('admin.logistik.stok')
                ->with('error', 'Item stok ini tidak dapat dihapus karena sudah memiliki riwayat distribusi ke posko/warga.');
        }

        $nama = $stok->nama_item;
        $stok->delete();

        return redirect()->route('admin.logistik.stok')
            ->with('success', 'Catatan stok logistik "' . $nama . '" berhasil dihapus.');
    }

    /**
     * Manajemen Distribusi & Penyaluran Bantuan Kedaruratan
     */
    public function distribusiBantuan(Request $request)
    {
        $query = DistribusiBantuan::with(['laporanBencana.bencana', 'laporanBencana.kecamatan', 'kelurahan.kecamatan', 'stokBantuan.kategoriStok', 'sender'])
            ->latest('tanggal_kirim');

        // Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('penerima', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%")
                    ->orWhere('satuan', 'like', "%{$q}%")
                    ->orWhereHas('stokBantuan', function ($s) use ($q) {
                        $s->where('nama_item', 'like', "%{$q}%");
                    })
                    ->orWhereHas('kelurahan', function ($k) use ($q) {
                        $k->where('nama_kelurahan', 'like', "%{$q}%")
                          ->orWhereHas('kecamatan', function ($kc) use ($q) {
                              $kc->where('nama_kecamatan', 'like', "%{$q}%");
                          });
                    })
                    ->orWhereHas('laporanBencana', function ($l) use ($q) {
                        $l->where('judul', 'like', "%{$q}%");
                    });
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $distribusiList = $query->paginate(10)->withQueryString();

        // Metrik Distribusi
        $totalDistribusi = DistribusiBantuan::count();
        $totalTerkirim = DistribusiBantuan::where('status', 'Selesai')->count();
        $totalProses = DistribusiBantuan::where('status', 'Proses Kirim')->count();
        $totalTiba = DistribusiBantuan::where('status', 'Tiba di Lokasi')->count();
        $totalUnitKirim = DistribusiBantuan::sum('jumlah');

        $laporanList = LaporanBencana::with('bencana')->latest('id_laporan')->take(30)->get();
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();
        $kelurahanList = Kelurahan::orderBy('nama_kelurahan')->get();
        $stokList = StokBantuan::orderBy('nama_item')->get();

        return view('admin.logistik.distribusi', compact(
            'distribusiList',
            'totalDistribusi',
            'totalTerkirim',
            'totalProses',
            'totalTiba',
            'totalUnitKirim',
            'laporanList',
            'kecamatanList',
            'kelurahanList',
            'stokList'
        ));
    }

    public function storeDistribusiBantuan(Request $request)
    {
        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'nullable|exists:app_kelurahan,id_kelurahan',
            'id_stok' => 'nullable|exists:app_stok_bantuan,id_stok',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'tanggal_kirim' => 'required|date',
            'tanggal_terima' => 'nullable|date',
            'status' => 'required|in:Proses Kirim,Tiba di Lokasi,Selesai',
            'penerima' => 'required|string|max:150',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'penerima.required' => 'Nama penerima / penanggung jawab wajib diisi.',
            'jumlah.min' => 'Jumlah barang distribusi minimal 1.',
        ]);

        $validated['dikirim_oleh'] = Auth::id();

        DistribusiBantuan::create($validated);

        return redirect()->route('admin.logistik.distribusi')
            ->with('success', 'Data penyaluran logistik ke "' . $validated['penerima'] . '" berhasil dicatat.');
    }

    public function updateDistribusiBantuan(Request $request, $id)
    {
        $distribusi = DistribusiBantuan::findOrFail($id);

        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'nullable|exists:app_kelurahan,id_kelurahan',
            'id_stok' => 'nullable|exists:app_stok_bantuan,id_stok',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'tanggal_kirim' => 'required|date',
            'tanggal_terima' => 'nullable|date',
            'status' => 'required|in:Proses Kirim,Tiba di Lokasi,Selesai',
            'penerima' => 'required|string|max:150',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $distribusi->update($validated);

        return redirect()->route('admin.logistik.distribusi')
            ->with('success', 'Data distribusi bantuan #' . $distribusi->id_distribusi . ' berhasil diperbarui.');
    }

    public function destroyDistribusiBantuan($id)
    {
        $distribusi = DistribusiBantuan::findOrFail($id);
        $distribusi->delete();

        return redirect()->route('admin.logistik.distribusi')
            ->with('success', 'Catatan distribusi bantuan berhasil dihapus.');
    }
}
