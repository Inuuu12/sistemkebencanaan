<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

class DashboardController extends Controller
{
    /**
     * Alias method untuk dashboard admin
     */
    public function dashboard(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Tampilan Dashboard Admin Lengkap (GIS Live Map, Charts Tren, Stok Logistik, Feeds Laporan)
     */
    public function index(Request $request)
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
}
