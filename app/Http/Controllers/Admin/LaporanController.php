<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Models\Kecamatan;
use App\Models\LaporanBencana;
use App\Models\Penanganan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * Halaman Khusus: Validasi & Verifikasi Aduan Warga (Laporan Darurat)
     */
    public function validasiAduan(Request $request)
    {
        $user = Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;
        $isKecamatanAdmin = $user && in_array($user->role, ['ADMIN_KECAMATAN', 'KECAMATAN']) && $user->id_kecamatan;

        $query = LaporanBencana::with(['bencana', 'kecamatan', 'kelurahan', 'korban', 'penanganan'])
            ->latest('created_at');

        // Pembatasan wilayah berdasarkan Role Admin
        if ($isKelurahanAdmin) {
            $query->where('id_kelurahan', $user->id_kelurahan);
        } elseif ($isKecamatanAdmin) {
            $query->where('id_kecamatan', $user->id_kecamatan);
        } elseif ($request->filled('kecamatan_id')) {
            $query->where('id_kecamatan', $request->kecamatan_id);
        }

        // Filter per kelurahan (khusus admin tingkat BPBD pusat)
        if (!$isKelurahanAdmin && $request->filled('kelurahan_id')) {
            $query->where('id_kelurahan', $request->kelurahan_id);
        }

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

        // Filter status (Pending, Handling, Diteruskan ke Kecamatan, Diteruskan ke BPBD, Selesai, Ditolak)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis bencana
        if ($request->filled('bencana_id')) {
            $query->where('id_bencana', $request->bencana_id);
        }

        $reports = $query->paginate(10)->withQueryString();

        // Base Query untuk Hitungan Tab Filter
        $baseCount = LaporanBencana::query();
        if ($isKelurahanAdmin) {
            $baseCount->where('id_kelurahan', $user->id_kelurahan);
        } elseif ($isKecamatanAdmin) {
            $baseCount->where('id_kecamatan', $user->id_kecamatan);
        }

        $totalAll = (clone $baseCount)->count();
        $totalPending = (clone $baseCount)->where('status', 'Pending')->count();
        $totalHandling = (clone $baseCount)->where('status', 'Handling')->count();
        $totalDiteruskanKec = (clone $baseCount)->where('status', 'Diteruskan ke Kecamatan')->count();
        $totalDiteruskanBPBD = (clone $baseCount)->where('status', 'Diteruskan ke BPBD')->count();
        $totalSelesai = (clone $baseCount)->where('status', 'Selesai')->count();
        $totalDitolak = (clone $baseCount)->where('status', 'Ditolak')->count();

        $disasters = Bencana::orderBy('nama_bencana')->get();
        $districts = Kecamatan::with('kelurahan')->orderBy('nama_kecamatan')->get();

        return view('admin.laporan.validasi', compact(
            'reports',
            'totalAll',
            'totalPending',
            'totalHandling',
            'totalDiteruskanKec',
            'totalDiteruskanBPBD',
            'totalSelesai',
            'totalDitolak',
            'disasters',
            'districts',
            'isKelurahanAdmin',
            'isKecamatanAdmin'
        ));
    }

    /**
     * Update status penanganan laporan secara cepat (dengan catatan verifikasi)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Handling,Diteruskan ke Kecamatan,Diteruskan ke BPBD,Selesai,Ditolak',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $report = LaporanBencana::findOrFail($id);
        $user = Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']);

        // Keamanan: Admin kelurahan hanya bisa memvalidasi laporan di kelurahannya
        if ($isKelurahanAdmin && $report->id_kelurahan != $user->id_kelurahan) {
            return back()->with('error', 'Akses ditolak: Anda hanya dapat memvalidasi laporan pada wilayah kelurahan Anda.');
        }

        $oldStatus = $report->status;
        $report->status = $request->status;

        // Catat arah eskalasi/delegasi
        if ($request->status === 'Diteruskan ke Kecamatan') {
            $report->diteruskan_ke = 'KECAMATAN';
        } elseif ($request->status === 'Diteruskan ke BPBD') {
            $report->diteruskan_ke = 'BPBD';
        }

        if ($request->filled('catatan')) {
            $report->catatan_kelurahan = $request->catatan;
        }

        $report->save();

        // Catat ke riwayat penanganan
        $catatanAksi = $request->catatan ?: "Status diperbarui menjadi '{$request->status}' oleh " . ($user->nama ?? 'Petugas');
        Penanganan::create([
            'id_laporan' => $report->id_laporan,
            'updated_by' => Auth::id() ?: 1,
            'status_baru' => $report->status,
            'catatan' => $catatanAksi,
        ]);

        $pesanNotif = match ($request->status) {
            'Diteruskan ke Kecamatan' => "Aduan #ADU-" . str_pad($report->id_laporan, 5, '0', STR_PAD_LEFT) . " berhasil divalidasi dan DITERUSKAN KE KECAMATAN.",
            'Diteruskan ke BPBD' => "Aduan #ADU-" . str_pad($report->id_laporan, 5, '0', STR_PAD_LEFT) . " berhasil divalidasi dan DITERUSKAN LANGSUNG KE BPBD PUSAT (Kedaruratan).",
            'Handling' => "Aduan #ADU-" . str_pad($report->id_laporan, 5, '0', STR_PAD_LEFT) . " berhasil divalidasi dan DITANGANI MANDIRI KELURAHAN.",
            'Selesai' => "Aduan #ADU-" . str_pad($report->id_laporan, 5, '0', STR_PAD_LEFT) . " ditandai SELESAI.",
            'Ditolak' => "Aduan #ADU-" . str_pad($report->id_laporan, 5, '0', STR_PAD_LEFT) . " DITOLAK.",
            default => "Status aduan #ADU-" . str_pad($report->id_laporan, 5, '0', STR_PAD_LEFT) . " diperbarui.",
        };

        return back()->with('success', $pesanNotif);
    }

    /**
     * Hapus data aduan warga secara permanen beserta data terkait
     */
    public function destroy($id)
    {
        $report = LaporanBencana::findOrFail($id);
        $user = Auth::user();
        $isKelurahanAdmin = $user && in_array($user->role, ['ADMIN_KELURAHAN', 'KELURAHAN']) && $user->id_kelurahan;

        // Keamanan: Admin kelurahan hanya dapat menghapus aduan di kelurahannya
        if ($isKelurahanAdmin && $report->id_kelurahan != $user->id_kelurahan) {
            return back()->with('error', 'Akses ditolak: Anda hanya dapat menghapus aduan pada wilayah kelurahan Anda.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($report) {
            // Hapus relasi terkait jika ada
            $report->penanganan()->delete();
            $report->korban()->delete();
            $report->dampakKerusakan()->delete();
            $report->distribusiBantuan()->delete();
            $report->historisKejadian()->delete();

            // Hapus file foto dari public/uploads/laporan jika ada
            if (!empty($report->foto_laporan)) {
                $photos = is_array($report->foto_laporan) ? $report->foto_laporan : json_decode($report->foto_laporan, true);
                if (is_array($photos)) {
                    foreach ($photos as $photo) {
                        if ($photo) {
                            $fullPath = public_path(ltrim($photo, '/\\'));
                            if (file_exists($fullPath) && is_file($fullPath)) {
                                @unlink($fullPath);
                            }
                        }
                    }
                } elseif (is_string($report->foto_laporan)) {
                    $fullPath = public_path(ltrim($report->foto_laporan, '/\\'));
                    if (file_exists($fullPath) && is_file($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }

            $report->delete();
        });

        return back()->with('success', "Data aduan warga #ADU-" . str_pad($id, 5, '0', STR_PAD_LEFT) . " berhasil dihapus secara permanen.");
    }
}
