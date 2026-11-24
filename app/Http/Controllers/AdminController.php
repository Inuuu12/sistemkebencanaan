<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\DaerahRawan;
use App\Models\DampakKerusakan;
use App\Models\DistribusiBantuan;
use App\Models\KategoriStok;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Korban;
use App\Models\LaporanBencana;
use App\Models\PoskoPengungsian;
use App\Models\StokBantuan;
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

        // C. Proporsi Kejadian Berdasarkan Jenis Bencana (Doughnut Chart)
        $bencanaDistribution = Bencana::withCount('laporanBencana')
            ->orderByDesc('laporan_bencana_count')
            ->get()
            ->filter(function ($b) {
                return $b->laporan_bencana_count > 0;
            });

        if ($bencanaDistribution->isEmpty()) {
            $disasterShare = [
                'labels' => ['Banjir', 'Tanah Longsor', 'Angin Puting Beliung', 'Kebakaran Lahan', 'Kekeringan'],
                'data' => [42, 28, 16, 9, 5],
                'colors' => ['#0284c7', '#d97706', '#059669', '#dc2626', '#8b5cf6'],
                'total' => 100,
            ];
        } else {
            $palette = ['#0284c7', '#d97706', '#059669', '#dc2626', '#8b5cf6', '#e11d48', '#ea580c', '#0891b2', '#4f46e5'];
            $labels = [];
            $data = [];
            $colors = [];
            $idx = 0;
            $sum = 0;
            foreach ($bencanaDistribution as $item) {
                $labels[] = $item->nama_bencana;
                $data[] = (int) $item->laporan_bencana_count;
                $colors[] = $palette[$idx % count($palette)];
                $sum += (int) $item->laporan_bencana_count;
                $idx++;
            }
            $disasterShare = [
                'labels' => $labels,
                'data' => $data,
                'colors' => $colors,
                'total' => $sum,
            ];
        }

        // D. Status Stok Logistik (Terhubung ke Model Database KategoriStok & StokBantuan)
        if (KategoriStok::count() === 0) {
            $defaultKategori = [
                ['nama_kategori' => 'Bahan Pangan', 'satuan' => 'Paket', 'deskripsi' => 'Beras, mi instan, air mineral, makanan bayi'],
                ['nama_kategori' => 'Sandang & Pakaian', 'satuan' => 'Set', 'deskripsi' => 'Selimut, pakaian layak, handuk, pakaian anak'],
                ['nama_kategori' => 'Perlengkapan Hunian (Papan)', 'satuan' => 'Unit', 'deskripsi' => 'Tenda pengungsi, terpal, kasur lipat, matras'],
                ['nama_kategori' => 'Kesehatan & Obat', 'satuan' => 'Pcs', 'deskripsi' => 'Kotak P3K, obat alergi, masker, antiseptik'],
            ];
            foreach ($defaultKategori as $cat) {
                KategoriStok::create($cat);
            }
        }

        $categories = KategoriStok::with('stokBantuan')->take(4)->get();

        $logisticsStock = $categories->map(function ($cat) {
            $masuk = $cat->stokBantuan->where('tipe', 'MASUK')->sum('jumlah');
            $keluar = $cat->stokBantuan->where('tipe', 'KELUAR')->sum('jumlah');
            $saldo = max(0, $masuk - $keluar);

            $targetKapasitas = match ($cat->nama_kategori) {
                'Bahan Pangan' => 2000,
                'Sandang & Pakaian' => 1200,
                'Perlengkapan Hunian (Papan)' => 350,
                'Kesehatan & Obat' => 1000,
                default => 1000,
            };

            // Jika belum ada pencatatan di database, gunakan baseline awal operasional
            if ($cat->stokBantuan->isEmpty()) {
                $saldo = match ($cat->nama_kategori) {
                    'Bahan Pangan' => 1420,
                    'Sandang & Pakaian' => 520,
                    'Perlengkapan Hunian (Papan)' => 85,
                    'Kesehatan & Obat' => 640,
                    default => 500,
                };
            }

            $percent = min(100, (int) round(($saldo / $targetKapasitas) * 100));

            if ($percent <= 25) {
                $status = 'KRITIS';
                $badgeClass = 'bg-red-100 text-red-700 font-black animate-pulse';
                $barColor = 'bg-red-500';
            } elseif ($percent <= 50) {
                $status = 'Waspada';
                $badgeClass = 'bg-amber-100 text-amber-800 font-bold';
                $barColor = 'bg-amber-500';
            } elseif ($percent <= 70) {
                $status = 'Cukup';
                $badgeClass = 'bg-blue-100 text-blue-800 font-bold';
                $barColor = 'bg-blue-500';
            } else {
                $status = 'Aman';
                $badgeClass = 'bg-emerald-100 text-emerald-800 font-bold';
                $barColor = 'bg-emerald-500';
            }

            $itemNames = $cat->stokBantuan->pluck('nama_item')->unique()->take(4)->implode(', ');
            if (empty($itemNames)) {
                $itemNames = $cat->deskripsi ?: 'Logistik darurat bencana';
            }

            return [
                'category' => $cat->nama_kategori,
                'items' => $itemNames,
                'stock' => $saldo,
                'capacity' => $targetKapasitas,
                'percent' => $percent,
                'unit' => $cat->satuan ?: 'Unit',
                'status' => $status,
                'badge_class' => $badgeClass,
                'bar_color' => $barColor,
            ];
        })->toArray();

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
            'disasterShare',
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
                    $sub->select('id_kelurahan')->from('app_md_kelurahan')->where('id_kecamatan', $kecId)->limit(1);
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
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
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
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
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
                    $sub->select('id_kelurahan')->from('app_md_kelurahan')->where('id_kecamatan', $kecId)->limit(1);
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
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
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
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
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
            'id_kecamatan' => 'required|exists:app_md_kecamatan,id_kecamatan',
            'id_kelurahan' => 'nullable|exists:app_md_kelurahan,id_kelurahan',
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
            'id_kecamatan' => 'required|exists:app_md_kecamatan,id_kecamatan',
            'id_kelurahan' => 'nullable|exists:app_md_kelurahan,id_kelurahan',
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
            'id_kelurahan' => 'nullable|exists:app_md_kelurahan,id_kelurahan',
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
            'id_kelurahan' => 'nullable|exists:app_md_kelurahan,id_kelurahan',
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

    /**
     * Manajemen Master Data Jenis Bencana (CRUD, Metrik Kejadian, Zonasi Rawan)
     */
    public function dataBencana(Request $request)
    {
        $query = Bencana::withCount(['laporanBencana', 'daerahRawan', 'prediksiBencana']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_bencana', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        $sort = $request->get('sort', 'nama');
        switch ($sort) {
            case 'terbanyak':
                $query->orderByDesc('laporan_bencana_count');
                break;
            case 'rawan':
                $query->orderByDesc('daerah_rawan_count');
                break;
            case 'terbaru':
                $query->orderByDesc('id_bencana');
                break;
            default:
                $query->orderBy('nama_bencana', 'asc');
                break;
        }

        $bencanaList = $query->paginate(10)->withQueryString();

        $totalBencana = Bencana::count();
        $totalLaporanTerkait = LaporanBencana::count();
        $totalTitikRawan = DaerahRawan::count();

        $topBencana = Bencana::withCount('laporanBencana')
            ->orderByDesc('laporan_bencana_count')
            ->first();

        $availableIcons = [
            ['filename' => '/icon/banjir.png', 'label' => 'Banjir / Genangan'],
            ['filename' => '/icon/tanah_longsor.png', 'label' => 'Tanah Longsor'],
            ['filename' => '/icon/angin_puting_beliung.png', 'label' => 'Angin Puting Beliung'],
            ['filename' => '/icon/angin_kencang.png', 'label' => 'Angin Kencang / Badai'],
            ['filename' => '/icon/kebakaran_hutan.png', 'label' => 'Kebakaran Hutan / Lahan'],
            ['filename' => '/icon/kebakaran_rumah.png', 'label' => 'Kebakaran Pemukiman'],
            ['filename' => '/icon/gempa_bumi.png', 'label' => 'Gempa Bumi'],
            ['filename' => '/icon/gunung_meletus.png', 'label' => 'Gunung Meletus / Erupsi'],
            ['filename' => '/icon/kekeringan.png', 'label' => 'Kekeringan / Krisis Air'],
        ];

        return view('admin.bencana.index', compact(
            'bencanaList',
            'totalBencana',
            'totalLaporanTerkait',
            'totalTitikRawan',
            'topBencana',
            'availableIcons'
        ));
    }

    public function storeBencana(Request $request)
    {
        $validated = $request->validate([
            'nama_bencana' => 'required|string|max:100|unique:app_md_bencana,nama_bencana',
            'deskripsi' => 'nullable|string|max:1000',
            'icon_url' => 'nullable|string|max:255',
        ], [
            'nama_bencana.required' => 'Nama jenis bencana wajib diisi.',
            'nama_bencana.unique' => 'Nama jenis bencana sudah terdaftar dalam sistem.',
            'nama_bencana.max' => 'Nama jenis bencana maksimal 100 karakter.',
        ]);

        $bencana = Bencana::create($validated);

        return redirect()->route('admin.bencana.index')
            ->with('success', 'Jenis bencana "' . $bencana->nama_bencana . '" berhasil ditambahkan.');
    }

    public function updateBencana(Request $request, $id)
    {
        $bencana = Bencana::findOrFail($id);

        $validated = $request->validate([
            'nama_bencana' => 'required|string|max:100|unique:app_md_bencana,nama_bencana,' . $id . ',id_bencana',
            'deskripsi' => 'nullable|string|max:1000',
            'icon_url' => 'nullable|string|max:255',
        ], [
            'nama_bencana.required' => 'Nama jenis bencana wajib diisi.',
            'nama_bencana.unique' => 'Nama jenis bencana tersebut sudah ada dalam sistem.',
            'nama_bencana.max' => 'Nama jenis bencana maksimal 100 karakter.',
        ]);

        $bencana->update($validated);

        return redirect()->route('admin.bencana.index')
            ->with('success', 'Data jenis bencana "' . $bencana->nama_bencana . '" berhasil diperbarui.');
    }

    public function destroyBencana($id)
    {
        $bencana = Bencana::withCount(['laporanBencana', 'daerahRawan'])->findOrFail($id);

        if ($bencana->laporan_bencana_count > 0 || $bencana->daerah_rawan_count > 0) {
            $alasan = [];
            if ($bencana->laporan_bencana_count > 0) {
                $alasan[] = $bencana->laporan_bencana_count . ' laporan kejadian';
            }
            if ($bencana->daerah_rawan_count > 0) {
                $alasan[] = $bencana->daerah_rawan_count . ' titik zonasi rawan';
            }
            return redirect()->route('admin.bencana.index')
                ->with('error', 'Tidak dapat menghapus jenis bencana "' . $bencana->nama_bencana . '" karena masih terhubung dengan ' . implode(' dan ', $alasan) . '.');
        }

        $nama = $bencana->nama_bencana;
        $bencana->delete();

        return redirect()->route('admin.bencana.index')
            ->with('success', 'Jenis bencana "' . $nama . '" berhasil dihapus dari sistem.');
    }
}
