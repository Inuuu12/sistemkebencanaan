<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\InteractsWithWeatherAndIcons;
use App\Models\Bencana;
use App\Models\Berita;
use App\Models\Kecamatan;
use App\Models\LaporanBencana;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use InteractsWithWeatherAndIcons;

    /**
     * Tampilan Utama / Peta Kebencanaan Publik (Landing Page)
     */
    public function index(Request $request)
    {
        // 1. Ambil data laporan bencana terkini beserta relasinya
        $query = LaporanBencana::with(['bencana', 'kecamatan', 'kelurahan', 'korban'])
            ->orderBy('created_at', 'desc');

        // Filter opsional jika user memfilter dari form
        if ($request->filled('kecamatan_id')) {
            $query->where('id_kecamatan', $request->kecamatan_id);
        }
        if ($request->filled('kelurahan_id')) {
            $query->where('id_kelurahan', $request->kelurahan_id);
        }
        if ($request->filled('bencana_id')) {
            $query->where('id_bencana', $request->bencana_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('alamat_detail', 'like', "%{$search}%");
            });
        }

        $reports = $query->get();

        // 2. Format laporan untuk peta (GeoJSON / Map Markers)
        $mappedReports = $reports->map(function ($r) {
            $disasterName = $r->bencana->nama_bencana ?? 'Bencana';
            $iconUrl = $this->getDisasterIcon($disasterName);
            $photos = [];
            if ($r->foto_laporan) {
                if (is_array($r->foto_laporan)) {
                    $photos = $r->foto_laporan;
                } else {
                    $decoded = json_decode($r->foto_laporan, true);
                    $photos = is_array($decoded) ? $decoded : [$r->foto_laporan];
                }
            }

            $totalKorban = $r->jumlah_korban ?? 0;
            $korbanDetail = $r->korban->first();
            $meninggal = $korbanDetail->jumlah_meninggal ?? 0;
            $luka = ($korbanDetail->jumlah_luka_berat ?? 0) + ($korbanDetail->jumlah_luka_ringan ?? 0);
            $mengungsi = $korbanDetail->jumlah_mengungsi ?? 0;

            return [
                'id' => $r->id_laporan,
                'title' => $r->judul,
                'description' => $r->deskripsi,
                'bencana_nama' => $disasterName,
                'bencana_id' => $r->id_bencana,
                'icon' => $iconUrl,
                'lat' => (float) $r->latitude,
                'lng' => (float) $r->longitude,
                'address' => $r->alamat_detail,
                'district' => $r->kecamatan->nama_kecamatan ?? '-',
                'village' => $r->kelurahan->nama_kelurahan ?? '-',
                'rt' => $r->rt,
                'rw' => $r->rw,
                'pelapor' => $r->nama_pelapor ?: 'Warga / Petugas',
                'status' => $r->status,
                'logistics' => $r->kebutuhan_logistik,
                'photos' => $photos,
                'total_victims' => $totalKorban,
                'meninggal' => $meninggal,
                'luka' => $luka,
                'mengungsi' => $mengungsi,
                'created_at_formatted' => $r->created_at ? $r->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                'time_ago' => $r->created_at ? $r->created_at->diffForHumans() : '-',
            ];
        });

        // 3. Master Data Jenis Bencana untuk Kategori Marquee Rail
        $bencanaTypes = Bencana::orderBy('nama_bencana')->get()->map(function ($b) {
            return [
                'id' => $b->id_bencana,
                'nama' => $b->nama_bencana,
                'deskripsi' => $b->deskripsi,
                'icon' => $this->getDisasterIcon($b->nama_bencana),
            ];
        });

        // 4. Master Wilayah Kecamatan untuk Filter & Form
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();

        // 5. Berita Terkini (untuk widget & floating quick tools)
        $latestNews = Berita::orderBy('created_at', 'desc')->take(6)->get();

        // 6. Data Cuaca Real-time Kabupaten Bogor
        $weather = $this->getWeatherData();

        // 7. Kontak Darurat Resmi
        $emergencyContacts = $this->getEmergencyContactsData();

        // 8. Statistik Ringkasan
        $stats = [
            'total_laporan' => $reports->count(),
            'laporan_diproses' => $reports->whereIn('status', ['Pending', 'Handling', 'Processed'])->count(),
            'laporan_selesai' => $reports->where('status', 'Selesai')->count(),
            'total_korban' => $reports->sum('jumlah_korban'),
        ];

        return view('public.landing', [
            'reports' => $mappedReports,
            'bencanaTypes' => $bencanaTypes,
            'kecamatanList' => $kecamatanList,
            'latestNews' => $latestNews,
            'weather' => $weather,
            'emergencyContacts' => $emergencyContacts,
            'stats' => $stats,
        ]);
    }
}
