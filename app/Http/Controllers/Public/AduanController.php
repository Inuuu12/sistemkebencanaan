<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\InteractsWithWeatherAndIcons;
use App\Mail\AduanOtpMail;
use App\Models\Bencana;
use App\Models\Kecamatan;
use App\Models\LaporanBencana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AduanController extends Controller
{
    use InteractsWithWeatherAndIcons;

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
            'id_kelurahan' => 'required|exists:app_md_kelurahan,id_kelurahan',
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
            'id_kelurahan.required' => 'Pilih desa/kelurahan lokasi kejadian bencana agar laporan masuk ke admin kelurahan terkait.',
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
            'latitude' => $request->filled('latitude') ? (float) $request->latitude : null,
            'longitude' => $request->filled('longitude') ? (float) $request->longitude : null,
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
}
