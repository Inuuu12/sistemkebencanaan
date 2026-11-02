<?php

namespace App\Http\Controllers;

use App\Mail\AduanOtpMail;
use App\Models\Bencana;
use App\Models\Berita;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\LaporanBencana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PublicPortalController extends Controller
{
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

    /**
     * Halaman Publikasi & Berita Kebencanaan
     */
    public function news(Request $request)
    {
        $query = Berita::orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($b) use ($q) {
                $b->where('judul', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%")
                    ->orWhere('sumber', 'like', "%{$q}%");
            });
        }

        $news = $query->paginate(9);
        $weather = $this->getWeatherData();

        return view('public.news', [
            'news' => $news,
            'weather' => $weather,
        ]);
    }

    /**
     * Detail Berita Kebencanaan
     */
    public function newsDetail($id)
    {
        $article = Berita::findOrFail($id);
        $relatedNews = Berita::where('id_berita', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        $weather = $this->getWeatherData();

        return view('public.news-detail', [
            'article' => $article,
            'relatedNews' => $relatedNews,
            'weather' => $weather,
        ]);
    }

    /**
     * Form Laporan Kejadian Bencana oleh Masyarakat
     */
    public function aduan()
    {
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();
        $bencanaList = Bencana::orderBy('nama_bencana')->get();
        $weather = $this->getWeatherData();

        return view('public.aduan', [
            'kecamatanList' => $kecamatanList,
            'bencanaList' => $bencanaList,
            'weather' => $weather,
        ]);
    }

    /**
     * Kirim Kode OTP Verifikasi Email Pelapor Aduan
     */
    public function sendAduanOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,filter|max:100',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 100 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email'),
            ], 422);
        }

        $email = strtolower(trim($request->email));

        // Rate-limiting / Cooldown 60 detik antar pengiriman
        $cooldownKey = 'otp_cooldown_' . md5($email);
        if (Cache::has($cooldownKey)) {
            $remaining = Cache::get($cooldownKey) - time();
            if ($remaining > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Mohon tunggu {$remaining} detik sebelum meminta kode OTP kembali.",
                    'remaining' => $remaining,
                ], 429);
            }
        }

        // Generate 6 digit numeric OTP
        $otp = (string) random_int(100000, 999999);

        // Simpan OTP ke cache selama 10 menit
        $cacheKey = 'aduan_otp_' . md5($email);
        Cache::put($cacheKey, [
            'code' => $otp,
            'email' => $email,
            'attempts' => 0,
        ], now()->addMinutes(10));

        // Set cooldown 60 detik
        Cache::put($cooldownKey, time() + 60, 60);

        // Kirim email OTP
        $isLocalOrLog = app()->environment('local') || config('mail.default') === 'log';
        try {
            Mail::to($email)->send(new AduanOtpMail($otp, $email));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email OTP aduan: ' . $e->getMessage());
            // Jika di lokal dan mail gagal/belum disetup, tetap izinkan dengan catatan dev
            if (!$isLocalOrLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim email verifikasi ke ' . $email . '. Pastikan email aktif atau hubungi admin.',
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6-digit telah dikirim ke ' . $email . '. Silakan cek kotak masuk atau folder spam Anda.',
            'cooldown' => 60,
            'dev_otp' => $isLocalOrLog ? $otp : null,
        ]);
    }

    /**
     * Verifikasi Kode OTP Email Pelapor
     */
    public function verifyAduanOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,filter|max:100',
            'otp' => 'required|string|size:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus 6 digit angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = strtolower(trim($request->email));
        $cacheKey = 'aduan_otp_' . md5($email);
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak ditemukan atau telah kedaluwarsa. Silakan klik "Kirim Kode OTP" kembali.',
            ], 400);
        }

        if (($cachedData['attempts'] ?? 0) >= 5) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'Batas percobaan OTP telah terlampaui. Silakan minta kode baru.',
            ], 429);
        }

        if (trim($request->otp) !== (string) $cachedData['code']) {
            $cachedData['attempts'] = ($cachedData['attempts'] ?? 0) + 1;
            Cache::put($cacheKey, $cachedData, now()->addMinutes(10));
            $sisa = 5 - $cachedData['attempts'];
            return response()->json([
                'success' => false,
                'message' => "Kode OTP salah. Sisa kesempatan: {$sisa} kali.",
            ], 400);
        }

        // Simpan status verifikasi di session
        session(['verified_aduan_email' => $email]);
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Email ' . $email . ' berhasil diverifikasi!',
            'verified_email' => $email,
        ]);
    }

    /**
     * Simpan Aduan / Laporan Warga
     */
    public function storeAduan(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'id_bencana' => 'required|exists:app_md_bencana,id_bencana',
            'id_kecamatan' => 'required|exists:app_md_kecamatan,id_kecamatan',
            'id_kelurahan' => 'nullable|exists:app_md_kelurahan,id_kelurahan',
            'deskripsi' => 'required|string',
            'alamat_detail' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'nama_pelapor' => 'nullable|string|max:100',
            'nomor_telepon' => 'required|string|max:20',
            'email_pelapor' => 'required|email:rfc,filter|max:100',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'tingkat_korban' => 'nullable|in:Rendah,Sedang,Tinggi',
            'jumlah_korban_jiwa' => 'nullable|integer|min:0',
            'jumlah_korban_luka_ringan' => 'nullable|integer|min:0',
            'jumlah_korban_luka_sedang' => 'nullable|integer|min:0',
            'jumlah_korban_luka_berat' => 'nullable|integer|min:0',
            'jumlah_rumah_rusak_berat' => 'nullable|integer|min:0',
            'jumlah_rumah_rusak_sedang' => 'nullable|integer|min:0',
            'jumlah_rumah_rusak_ringan' => 'nullable|integer|min:0',
        ], [
            'email_pelapor.required' => 'Email pelapor wajib diisi.',
            'email_pelapor.email' => 'Format email pelapor tidak valid.',
            'nomor_telepon.required' => 'Nomor HP/WhatsApp pelapor wajib diisi.',
            'judul.required' => 'Judul laporan bencana wajib diisi.',
            'id_bencana.required' => 'Pilih jenis bencana yang terjadi.',
            'id_kecamatan.required' => 'Pilih kecamatan lokasi kejadian.',
            'deskripsi.required' => 'Kronologi dan keterangan kejadian bencana wajib diisi.',
            'tingkat_korban.in' => 'Tingkat korban tidak valid.',
        ]);

        $email = strtolower(trim($request->email_pelapor));
        $verifiedEmail = session('verified_aduan_email');

        // Pastikan email telah berhasil diverifikasi OTP
        if ($verifiedEmail !== $email) {
            return back()
                ->withInput()
                ->withErrors([
                    'email_pelapor' => 'Email belum diverifikasi. Silakan masukkan kode OTP yang dikirimkan ke email Anda dan klik Verifikasi terlebih dahulu agar email terdaftar secara sah.',
                ]);
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'laporan_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/laporan');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $fotoPath = '/uploads/laporan/' . $filename;
        }

        // Simpan ke database
        $laporan = LaporanBencana::create([
            'id_user' => 1, // Default guest / warga
            'id_bencana' => $request->id_bencana,
            'id_kecamatan' => $request->id_kecamatan,
            'id_kelurahan' => $request->id_kelurahan,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'alamat_detail' => $request->alamat_detail,
            'latitude' => $request->latitude ?: -6.4855,
            'longitude' => $request->longitude ?: 106.8450,
            'nama_pelapor' => $request->nama_pelapor ?: 'Warga Masyarakat',
            'nomor_telepon' => $request->nomor_telepon,
            'email_pelapor' => $email,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'foto_laporan' => $fotoPath ? [$fotoPath] : null,
            'jumlah_korban' => 0,
            'tingkat_korban' => $request->tingkat_korban,
            'jumlah_korban_jiwa' => (int) ($request->jumlah_korban_jiwa ?? 0),
            'jumlah_korban_luka_ringan' => (int) ($request->jumlah_korban_luka_ringan ?? 0),
            'jumlah_korban_luka_sedang' => (int) ($request->jumlah_korban_luka_sedang ?? 0),
            'jumlah_korban_luka_berat' => (int) ($request->jumlah_korban_luka_berat ?? 0),
            'jumlah_rumah_rusak_berat' => (int) ($request->jumlah_rumah_rusak_berat ?? 0),
            'jumlah_rumah_rusak_sedang' => (int) ($request->jumlah_rumah_rusak_sedang ?? 0),
            'jumlah_rumah_rusak_ringan' => (int) ($request->jumlah_rumah_rusak_ringan ?? 0),
            'status' => 'Pending',
        ]);

        // Hapus session verifikasi setelah laporan terkirim
        session()->forget('verified_aduan_email');

        return redirect()->route('public.aduan')
            ->with('success', 'Laporan kejadian bencana Anda berhasil dikirimkan ke PUSDALOPS BPBD Kabupaten Bogor. Tim tanggap darurat akan segera melakukan verifikasi lapangan.');
    }

    /**
     * Endpoint pembantu untuk mengambil daftar Kelurahan berdasarkan Kecamatan
     */
    public function getKelurahan(Request $request)
    {
        $kecamatanId = $request->query('kecamatan_id');
        if (!$kecamatanId) {
            return response()->json([]);
        }

        $kelurahan = Kelurahan::where('id_kecamatan', $kecamatanId)
            ->orderBy('nama_kelurahan')
            ->get(['id_kelurahan', 'nama_kelurahan']);

        return response()->json($kelurahan);
    }

    /**
     * Sajikan GeoJSON Batas Wilayah Kabupaten & Kecamatan
     */
    public function getBoundaries(Request $request)
    {
        $level = $request->query('level', 'kecamatan');

        if ($level === 'kabupaten') {
            $path = base_path('database/data/simplified/admin_kab.json');
        } else {
            $path = base_path('database/data/simplified/admin_kec.json');
        }

        if (File::exists($path)) {
            return response()->file($path, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        return response()->json(['type' => 'FeatureCollection', 'features' => []]);
    }

    /**
     * Ambil Cuaca Wilayah Kabupaten Bogor
     */
    private function getWeatherData(): array
    {
        $regions = [
            ['name' => 'Cibinong, Kab. Bogor', 'lat' => -6.4855, 'lng' => 106.8450],
            ['name' => 'Cisarua (Puncak), Kab. Bogor', 'lat' => -6.6980, 'lng' => 106.9320],
            ['name' => 'Ciawi, Kab. Bogor', 'lat' => -6.6580, 'lng' => 106.8550],
            ['name' => 'Babakan Madang, Kab. Bogor', 'lat' => -6.5650, 'lng' => 106.8720],
            ['name' => 'Bojonggede, Kab. Bogor', 'lat' => -6.4950, 'lng' => 106.7980],
            ['name' => 'Parung, Kab. Bogor', 'lat' => -6.4250, 'lng' => 106.7320],
            ['name' => 'Leuwiliang, Kab. Bogor', 'lat' => -6.5750, 'lng' => 106.6280],
            ['name' => 'Jasinga, Kab. Bogor', 'lat' => -6.4850, 'lng' => 106.4520],
            ['name' => 'Jonggol, Kab. Bogor', 'lat' => -6.4680, 'lng' => 107.0520],
            ['name' => 'Cileungsi, Kab. Bogor', 'lat' => -6.3980, 'lng' => 106.9620],
        ];

        return Cache::remember('weather_kabupaten_bogor_public', 300, function () use ($regions) {
            try {
                $lats = implode(',', array_column($regions, 'lat'));
                $lngs = implode(',', array_column($regions, 'lng'));
                $url = "https://api.open-meteo.com/v1/forecast?latitude={$lats}&longitude={$lngs}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&timezone=Asia%2FJakarta";

                $response = Http::timeout(4)->get($url);
                if ($response->successful()) {
                    $results = $response->json();
                    if (is_array($results)) {
                        $parsed = [];
                        foreach ($regions as $index => $region) {
                            $item = $results[$index] ?? null;
                            $current = $item['current'] ?? null;
                            if (!$current) continue;

                            $code = $current['weather_code'] ?? 0;
                            $condition = 'Cerah';
                            if (in_array($code, [1, 2])) $condition = 'Cerah Berawan';
                            elseif ($code === 3) $condition = 'Berawan';
                            elseif (in_array($code, [45, 48])) $condition = 'Berkabut';
                            elseif (in_array($code, [51, 53, 55, 56, 57])) $condition = 'Gerimis';
                            elseif (in_array($code, [61, 63])) $condition = 'Hujan Ringan';
                            elseif (in_array($code, [65, 80, 81, 82])) $condition = 'Hujan';
                            elseif (in_array($code, [95, 96, 99])) $condition = 'Badai Petir';

                            $parsed[] = [
                                'location' => $region['name'],
                                'temp' => round($current['temperature_2m'], 1),
                                'humidity' => (int) round($current['relative_humidity_2m'] ?? 65),
                                'condition' => $condition,
                                'wind_speed' => (int) round($current['wind_speed_10m'] ?? 0),
                            ];
                        }
                        if (!empty($parsed)) return $parsed;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback graceful
            }

            return [
                ['location' => 'Cibinong, Kab. Bogor', 'temp' => 31.0, 'humidity' => 60, 'condition' => 'Cerah Berawan', 'wind_speed' => 8],
                ['location' => 'Cisarua (Puncak), Kab. Bogor', 'temp' => 24.5, 'humidity' => 75, 'condition' => 'Berawan Sejuk', 'wind_speed' => 9],
                ['location' => 'Ciawi, Kab. Bogor', 'temp' => 28.0, 'humidity' => 65, 'condition' => 'Cerah Berawan', 'wind_speed' => 7],
                ['location' => 'Babakan Madang, Kab. Bogor', 'temp' => 30.0, 'humidity' => 62, 'condition' => 'Cerah', 'wind_speed' => 8],
                ['location' => 'Bojonggede, Kab. Bogor', 'temp' => 31.2, 'humidity' => 60, 'condition' => 'Cerah Berawan', 'wind_speed' => 6],
                ['location' => 'Parung, Kab. Bogor', 'temp' => 31.0, 'humidity' => 58, 'condition' => 'Cerah', 'wind_speed' => 7],
                ['location' => 'Leuwiliang, Kab. Bogor', 'temp' => 30.4, 'humidity' => 64, 'condition' => 'Cerah Berawan', 'wind_speed' => 6],
                ['location' => 'Jasinga, Kab. Bogor', 'temp' => 29.5, 'humidity' => 68, 'condition' => 'Cerah', 'wind_speed' => 5],
                ['location' => 'Jonggol, Kab. Bogor', 'temp' => 31.0, 'humidity' => 59, 'condition' => 'Cerah', 'wind_speed' => 8],
                ['location' => 'Cileungsi, Kab. Bogor', 'temp' => 31.5, 'humidity' => 58, 'condition' => 'Cerah', 'wind_speed' => 8],
            ];
        });
    }

    /**
     * Kontak Darurat Hotline BPBD & Lembaga Terkait
     */
    private function getEmergencyContactsData(): array
    {
        return [
            [
                'name' => 'Call Center BPBD (112)',
                'phone' => '112',
                'description' => 'Layanan Bebas Pulsa Kedaruratan Bencana 24 Jam',
                'icon' => 'phone-call',
                'color' => 'red',
            ],
            [
                'name' => 'Pusdalops BPBD Kab. Bogor',
                'phone' => '0218751070',
                'phone_display' => '(021) 875-1070',
                'description' => 'Pusat Pengendalian Operasi Kebencanaan Kabupaten Bogor',
                'icon' => 'shield-alert',
                'color' => 'orange',
            ],
            [
                'name' => 'Pemadam Kebakaran (113)',
                'phone' => '113',
                'description' => 'Dinas Pemadam Kebakaran & Penyelamatan Kab. Bogor',
                'icon' => 'flame',
                'color' => 'amber',
            ],
            [
                'name' => 'Ambulans Gawat Darurat (119)',
                'phone' => '119',
                'description' => 'Pelayanan Kesehatan Darurat & Rujukan Pasien',
                'icon' => 'heart-pulse',
                'color' => 'emerald',
            ],
            [
                'name' => 'Kepolisian Resor Bogor (110)',
                'phone' => '110',
                'description' => 'Layanan Kepolisian & Ketertiban Masyarakat',
                'icon' => 'badge-sheriff',
                'color' => 'blue',
            ],
            [
                'name' => 'BASARNAS Jakarta (115)',
                'phone' => '115',
                'description' => 'Operasi Pencarian dan Pertolongan Korban Bencana',
                'icon' => 'life-buoy',
                'color' => 'teal',
            ],
        ];
    }

    /**
     * Pemetaan ikon bencana sesuai dengan file gambar di public/icon atau public/icons
     */
    private function getDisasterIcon(?string $name): string
    {
        if (!$name) return '/icon/banjir.png';
        $n = strtolower(trim($name));

        if (str_contains($n, 'banjir')) return '/icon/banjir.png';
        if (str_contains($n, 'longsor')) return '/icon/tanah_longsor.png';
        if (str_contains($n, 'puting beliung')) return '/icon/angin_puting_beliung.png';
        if (str_contains($n, 'angin') || str_contains($n, 'topan') || str_contains($n, 'badai')) return '/icon/angin_kencang.png';
        if (str_contains($n, 'lahan') || str_contains($n, 'hutan')) return '/icon/kebakaran_hutan.png';
        if (str_contains($n, 'kebakaran')) return '/icon/kebakaran_rumah.png';
        if (str_contains($n, 'gempa')) return '/icon/gempa_bumi.png';
        if (str_contains($n, 'gunung') || str_contains($n, 'meletus')) return '/icon/gunung_meletus.png';
        if (str_contains($n, 'tsunami')) return '/icon/tsunami.png';
        if (str_contains($n, 'kering') || str_contains($n, 'kekeringan')) return '/icon/kekeringan.png';

        // Cek alternatif di folder /icons/
        if (File::exists(public_path('icons/' . $n . '.png'))) {
            return '/icons/' . $n . '.png';
        }

        return '/icon/banjir.png';
    }

    /**
     * Tampilan Halaman Login Admin / Petugas
     */
    public function login()
    {
        return view('admin.login');
    }

    /**
     * Proses Autentikasi Login
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.home');
    }
}
