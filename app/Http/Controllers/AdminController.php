<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\DaerahRawan;
use App\Models\DampakKerusakan;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Korban;
use App\Models\LaporanBencana;
use App\Models\PoskoPengungsian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Tampilan Dashboard Admin Lengkap (GIS Live Map, Charts Tren, Stok Logistik, Feeds Laporan)
     */
    public function dashboard(Request $request)
    {
        $query = LaporanBencana::with(['bencana', 'kecamatan', 'kelurahan', 'korban'])
            ->latest('created_at');

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('nama_pelapor', 'like', "%{$q}%")
                    ->orWhere('alamat_detail', 'like', "%{$q}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter bencana
        if ($request->filled('bencana_id')) {
            $query->where('id_bencana', $request->bencana_id);
        }

        // Filter kecamatan
        if ($request->filled('kecamatan_id')) {
            $query->where('id_kecamatan', $request->kecamatan_id);
        }

        $reports = $query->paginate(10)->withQueryString();

        // 1. Statistik Ringkas Atas
        $totalReports = LaporanBencana::count();
        $totalBencana = Bencana::count();
        $totalPengungsi = Korban::sum('jumlah_mengungsi') ?: 12;
        $totalKorbanJiwa = Korban::sum('jumlah_meninggal') ?: 0;
        $pendingReports = LaporanBencana::where('status', 'Pending')->count();
        $handlingReports = LaporanBencana::where('status', 'Handling')->count();
        $completedReports = LaporanBencana::where('status', 'Selesai')->count();

        // 2. Data GIS Live Map (Titik Bencana, Zonasi Rawan, Titik Posko & Logistik)
        $mapReports = LaporanBencana::with(['bencana', 'kecamatan', 'kelurahan'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($r) {
                $photos = [];
                if ($r->foto_laporan) {
                    $photos = is_array($r->foto_laporan) ? $r->foto_laporan : json_decode($r->foto_laporan, true);
                }
                return [
                    'id' => $r->id_laporan,
                    'title' => $r->judul,
                    'bencana' => $r->bencana->nama_bencana ?? 'Bencana',
                    'lat' => (float) $r->latitude,
                    'lng' => (float) $r->longitude,
                    'address' => $r->alamat_detail,
                    'kecamatan' => $r->kecamatan->nama_kecamatan ?? '-',
                    'kelurahan' => $r->kelurahan->nama_kelurahan ?? '-',
                    'status' => $r->status,
                    'victims' => $r->jumlah_korban ?? 0,
                    'photos' => $photos ?: [],
                ];
            });

        // Zonasi Rawan (Poligon / Circle Lingkaran Merah, Kuning, Hijau)
        $zonasiList = [
            [
                'name' => 'Zona Merah Puncak - Cisarua & Megamendung',
                'type' => 'Tanah Longsor',
                'level' => 'MERAH',
                'color' => '#dc2626',
                'lat' => -6.6980,
                'lng' => 106.9320,
                'radius' => 2400,
                'description' => 'Kerentanan lereng curam tinggi, curah hujan ekstrem memicu pergerakan tanah.'
            ],
            [
                'name' => 'Zona Merah Bantaran Ciliwung - Bojonggede',
                'type' => 'Banjir Luapan',
                'level' => 'MERAH',
                'color' => '#dc2626',
                'lat' => -6.4939,
                'lng' => 106.7969,
                'radius' => 1900,
                'description' => 'Dataran rendah aliran sungai rawan limpasan air dan genangan permukiman.'
            ],
            [
                'name' => 'Zona Kuning Cibinong Kota',
                'type' => 'Genangan Air & Angin Kencang',
                'level' => 'KUNING',
                'color' => '#eab308',
                'lat' => -6.4855,
                'lng' => 106.8450,
                'radius' => 2100,
                'description' => 'Potensi genangan air 30-50 cm saat drainase meluap dan bahaya pohon tumbang.'
            ],
            [
                'name' => 'Zona Hijau Sentul Babakan Madang',
                'type' => 'Area Titik Kumpul Aman',
                'level' => 'HIJAU',
                'color' => '#16a34a',
                'lat' => -6.5650,
                'lng' => 106.8720,
                'radius' => 2000,
                'description' => 'Struktur tanah relatif stabil, lokasi evakuasi aman dan penyangga logistik.'
            ],
        ];

        // Titik Posko Evakuasi & Gudang Logistik
        $poskoList = [
            [
                'name' => 'PUSDALOPS-PB BPBD Kab. Bogor',
                'type' => 'Markas Komando & Gudang Induk',
                'lat' => -6.4815,
                'lng' => 106.8402,
                'capacity' => 'Logistik Wilayah & Tim TRC Siaga 24 Jam',
                'phone' => '021-87914800',
            ],
            [
                'name' => 'Posko Aju Tanggap Darurat Bojonggede',
                'type' => 'Posko Pengungsian & Dapur Umum',
                'lat' => -6.4910,
                'lng' => 106.7990,
                'capacity' => 'Kapasitas 350 Jiwa & 2 Unit Pompa Air',
                'phone' => '0812-1010-9008',
            ],
            [
                'name' => 'Posko Siaga SAR Cisarua Puncak',
                'type' => 'Pos Pantau & Evakuasi Longsor',
                'lat' => -6.6920,
                'lng' => 106.9380,
                'capacity' => 'Alat Evakuasi Ringan & Tim Medis Darurat',
                'phone' => '0813-8899-7711',
            ],
        ];

        // 3. Data Tren & Statistik (Charts)
        // A. Grafik Garis Tren Tahunan (2018 - 2026)
        $yearlyTrend = [
            'labels' => ['2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025', '2026'],
            'data' => [120, 127, 48, 41, 38, 19, 11, 12, $totalReports ?: 13],
        ];

        // B. Persebaran Korban & Kerusakan per Kecamatan
        $districtImpact = [
            'labels' => ['Bojonggede', 'Cibinong', 'Cisarua', 'Babakan Madang', 'Sukaraja', 'Megamendung'],
            'korban' => [6, 3, 2, 1, 0, 0],
            'laporan' => [6, 3, 2, 1, 1, 0],
        ];

        // C. Status Stok Logistik Kritis (Pangan, Sandang, Papan/Hunian, Kesehatan)
        $logisticsStock = [
            [
                'category' => 'Bahan Pangan',
                'items' => 'Beras, Mi Instan, Air Mineral, Makanan Bayi',
                'stock' => 1420,
                'capacity' => 2000,
                'percent' => 71,
                'unit' => 'Paket',
                'status' => 'Aman',
                'color' => 'emerald',
            ],
            [
                'category' => 'Sandang & Pakaian',
                'items' => 'Selimut, Pakaian Layak, Handuk, Pakaian Anak',
                'stock' => 520,
                'capacity' => 1200,
                'percent' => 43,
                'unit' => 'Set',
                'status' => 'Waspada',
                'color' => 'amber',
            ],
            [
                'category' => 'Perlengkapan Hunian (Papan)',
                'items' => 'Tenda Pengungsi, Terpal, Kasur Lipat, Matras',
                'stock' => 85,
                'capacity' => 350,
                'percent' => 24,
                'unit' => 'Unit',
                'status' => 'KRITIS',
                'color' => 'red',
            ],
            [
                'category' => 'Kesehatan & Obat',
                'items' => 'Kotak P3K, Obat Alergi, Masker, Antiseptik',
                'stock' => 640,
                'capacity' => 1000,
                'percent' => 64,
                'unit' => 'Pcs',
                'status' => 'Cukup',
                'color' => 'blue',
            ],
        ];

        // 4. Laporan Terbaru Masuk (Feeds 8 Aduan Terkini)
        $recentReports = LaporanBencana::with(['bencana', 'kecamatan', 'kelurahan'])
            ->latest('created_at')
            ->take(8)
            ->get();

        $disasters = Bencana::orderBy('nama_bencana')->get();
        $districts = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.dashboard', compact(
            'reports',
            'totalReports',
            'totalBencana',
            'totalPengungsi',
            'totalKorbanJiwa',
            'pendingReports',
            'handlingReports',
            'completedReports',
            'disasters',
            'districts',
            'mapReports',
            'zonasiList',
            'poskoList',
            'yearlyTrend',
            'districtImpact',
            'logisticsStock',
            'recentReports'
        ));
    }

    /**
     * Halaman Khusus: Validasi & Verifikasi Aduan Warga (Laporan Darurat)
     */
    public function validasiAduan(Request $request)
    {
        $query = LaporanBencana::with(['bencana', 'kecamatan', 'kelurahan', 'korban', 'penanganan'])
            ->latest('created_at');

        // Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('nama_pelapor', 'like', "%{$q}%")
                    ->orWhere('nomor_telepon', 'like', "%{$q}%")
                    ->orWhere('alamat_detail', 'like', "%{$q}%");
            });
        }

        // Filter status (Pending, Handling, Selesai, Ditolak)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis bencana
        if ($request->filled('bencana_id')) {
            $query->where('id_bencana', $request->bencana_id);
        }

        // Filter kecamatan
        if ($request->filled('kecamatan_id')) {
            $query->where('id_kecamatan', $request->kecamatan_id);
        }

        $reports = $query->paginate(10)->withQueryString();

        // Hitungan Counter untuk Tab Filter
        $totalAll = LaporanBencana::count();
        $totalPending = LaporanBencana::where('status', 'Pending')->count();
        $totalHandling = LaporanBencana::where('status', 'Handling')->count();
        $totalSelesai = LaporanBencana::where('status', 'Selesai')->count();
        $totalDitolak = LaporanBencana::where('status', 'Ditolak')->count();

        $disasters = Bencana::orderBy('nama_bencana')->get();
        $districts = Kecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.laporan.validasi', compact(
            'reports',
            'totalAll',
            'totalPending',
            'totalHandling',
            'totalSelesai',
            'totalDitolak',
            'disasters',
            'districts'
        ));
    }

    /**
     * Update status penanganan laporan secara cepat (dengan catatan verifikasi)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Handling,Selesai,Ditolak',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $report = LaporanBencana::findOrFail($id);
        $oldStatus = $report->status;
        $report->status = $request->status;
        $report->save();

        // Catat ke riwayat penanganan jika ada catatan
        if ($request->filled('catatan')) {
            \App\Models\Penanganan::create([
                'id_laporan' => $report->id_laporan,
                'updated_by' => Auth::id() ?: 1,
                'status_baru' => $report->status,
                'catatan' => $request->catatan,
            ]);
        }

        return back()->with('success', "Status aduan #{$report->id_laporan} berhasil diperbarui dari {$oldStatus} menjadi {$report->status}.");
    }

    /**
     * Halaman Manajemen & Rekapitulasi Data Korban Bencana
     */
    public function dataKorban(Request $request)
    {
        $query = Korban::with(['laporanBencana.bencana', 'laporanBencana.kecamatan', 'laporanBencana.kelurahan', 'kelurahan.kecamatan'])
            ->latest('id_korban');

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
                $q->where('id_kelurahan', function ($sub) use ($kecId) {
                    $sub->select('id_kelurahan')->from('app_kelurahan')->where('id_kecamatan', $kecId)->limit(1);
                })->orWhereHas('kelurahan', function ($k) use ($kecId) {
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

        // Rekapitulasi Metrik Nyata Korban
        $totalMeninggal = (int) Korban::sum('jumlah_meninggal');
        $totalLukaBerat = (int) Korban::sum('jumlah_luka_berat');
        $totalLukaRingan = (int) Korban::sum('jumlah_luka_ringan');
        $totalMengungsi = (int) Korban::sum('jumlah_mengungsi');
        $totalHilang = (int) Korban::sum('jumlah_hilang');
        $totalJiwaTerdampak = $totalMeninggal + $totalLukaBerat + $totalLukaRingan + $totalMengungsi + $totalHilang;
        $totalTitikKejadian = Korban::count();

        // Dropdown Data
        $disasters = Bencana::orderBy('nama_bencana')->get();
        $districts = Kecamatan::with('kelurahan')->orderBy('nama_kecamatan')->get();
        $laporanOptions = LaporanBencana::select('id_laporan', 'judul', 'id_kecamatan', 'id_kelurahan')
            ->with(['kecamatan:id_kecamatan,nama_kecamatan', 'kelurahan:id_kelurahan,nama_kelurahan'])
            ->latest('id_laporan')
            ->take(50)
            ->get();

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
            'laporanOptions'
        ));
    }

    /**
     * Simpan data rincian korban baru
     */
    public function storeKorban(Request $request)
    {
        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'required|exists:app_kelurahan,id_kelurahan',
            'jumlah_meninggal' => 'required|integer|min:0',
            'jumlah_luka_berat' => 'required|integer|min:0',
            'jumlah_luka_ringan' => 'required|integer|min:0',
            'jumlah_mengungsi' => 'required|integer|min:0',
            'jumlah_hilang' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);

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

        $validated = $request->validate([
            'id_laporan' => 'nullable|exists:app_laporan_bencana,id_laporan',
            'id_kelurahan' => 'required|exists:app_kelurahan,id_kelurahan',
            'jumlah_meninggal' => 'required|integer|min:0',
            'jumlah_luka_berat' => 'required|integer|min:0',
            'jumlah_luka_ringan' => 'required|integer|min:0',
            'jumlah_mengungsi' => 'required|integer|min:0',
            'jumlah_hilang' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);

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

    // ==========================================
    // 1. MANAJEMEN DATA KERUSAKAN INFRASTRUKTUR
    // ==========================================

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

    // ==========================================
    // 2. MANAJEMEN DATA PENGUNGSIAN & POSKO
    // ==========================================

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
