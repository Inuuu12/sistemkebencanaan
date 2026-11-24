<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\InteractsWithWeatherAndIcons;
use App\Models\Bencana;
use App\Models\Kecamatan;
use App\Models\LaporanBencana;
use Illuminate\Http\Request;

class KejadianController extends Controller
{
    use InteractsWithWeatherAndIcons;

    /**
     * Halaman Publikasi & Daftar Seluruh Kejadian Bencana
     */
    public function index(Request $request)
    {
        $query = LaporanBencana::with(['bencana', 'kecamatan', 'kelurahan', 'korban', 'dampakKerusakan']);

        // 1. Filter Pencarian Teks
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('alamat_detail', 'like', "%{$search}%")
                    ->orWhere('nama_pelapor', 'like', "%{$search}%");
            });
        }

        // 2. Filter Jenis Bencana
        if ($request->filled('bencana_id')) {
            $query->where('id_bencana', $request->bencana_id);
        }

        // 3. Filter Kecamatan
        if ($request->filled('kecamatan_id')) {
            $query->where('id_kecamatan', $request->kecamatan_id);
        }

        // 4. Filter Status Penanganan
        if ($request->filled('status') && in_array($request->status, ['Pending', 'Handling', 'Selesai'])) {
            $query->where('status', $request->status);
        }

        // 5. Urutan Data
        $urutan = $request->get('urutan', 'terbaru');
        if ($urutan === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } elseif ($urutan === 'korban_terbanyak') {
            $query->orderBy('jumlah_korban', 'desc')->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Statistik Keseluruhan untuk Header Card
        $stats = [
            'total' => LaporanBencana::count(),
            'handling' => LaporanBencana::where('status', 'Handling')->count(),
            'selesai' => LaporanBencana::where('status', 'Selesai')->count(),
            'pending' => LaporanBencana::where('status', 'Pending')->count(),
            'total_korban' => (int) LaporanBencana::sum('jumlah_korban'),
        ];

        // Eksekusi Paginasi
        $reports = $query->paginate(9)->withQueryString();

        // Transformasikan Icon & Foto untuk Setiap Item
        $reports->getCollection()->transform(function ($r) {
            $r->icon_url = $this->getDisasterIcon($r->bencana->nama_bencana ?? 'Bencana');
            $photos = [];
            if ($r->foto_laporan) {
                if (is_array($r->foto_laporan)) {
                    $photos = $r->foto_laporan;
                } else {
                    $decoded = json_decode($r->foto_laporan, true);
                    $photos = is_array($decoded) ? $decoded : [$r->foto_laporan];
                }
            }
            $r->photos_list = array_values(array_filter($photos));
            return $r;
        });

        // Dropdown Master Data
        $bencanaList = Bencana::orderBy('nama_bencana')->get();
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();
        $weather = $this->getWeatherData();

        return view('public.kejadian', [
            'reports' => $reports,
            'stats' => $stats,
            'bencanaList' => $bencanaList,
            'kecamatanList' => $kecamatanList,
            'weather' => $weather,
        ]);
    }

    /**
     * Halaman Detail Satu Kejadian Bencana
     */
    public function detail($id)
    {
        $report = LaporanBencana::with([
            'bencana',
            'kecamatan',
            'kelurahan',
            'korban',
            'dampakKerusakan',
            'penanganan',
            'distribusiBantuan.stokBantuan',
        ])->findOrFail($id);

        $iconUrl = $this->getDisasterIcon($report->bencana->nama_bencana ?? 'Bencana');

        // Parse Daftar Foto
        $photos = [];
        if ($report->foto_laporan) {
            if (is_array($report->foto_laporan)) {
                $photos = $report->foto_laporan;
            } else {
                $decoded = json_decode($report->foto_laporan, true);
                $photos = is_array($decoded) ? $decoded : [$report->foto_laporan];
            }
        }
        $photosList = array_values(array_filter($photos));

        // Kejadian Terkait (Satu Kecamatan atau Jenis Bencana yang Sama)
        $relatedReports = LaporanBencana::with(['bencana', 'kecamatan', 'kelurahan'])
            ->where('id_laporan', '!=', $id)
            ->where(function ($q) use ($report) {
                $q->where('id_kecamatan', $report->id_kecamatan)
                    ->orWhere('id_bencana', $report->id_bencana);
            })
            ->latest('created_at')
            ->take(3)
            ->get()
            ->map(function ($r) {
                $r->icon_url = $this->getDisasterIcon($r->bencana->nama_bencana ?? 'Bencana');
                return $r;
            });

        // Kontak Darurat BPBD
        $emergencyContacts = $this->getEmergencyContactsData();
        $weather = $this->getWeatherData();

        return view('public.kejadian-detail', [
            'report' => $report,
            'iconUrl' => $iconUrl,
            'photosList' => $photosList,
            'relatedReports' => $relatedReports,
            'emergencyContacts' => $emergencyContacts,
            'weather' => $weather,
        ]);
    }
}
