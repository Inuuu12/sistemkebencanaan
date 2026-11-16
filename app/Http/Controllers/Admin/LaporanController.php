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
            Penanganan::create([
                'id_laporan' => $report->id_laporan,
                'updated_by' => Auth::id() ?: 1,
                'status_baru' => $report->status,
                'catatan' => $request->catatan,
            ]);
        }

        return back()->with('success', "Status aduan #{$report->id_laporan} berhasil diperbarui dari {$oldStatus} menjadi {$report->status}.");
    }

    /**
     * Hapus data aduan warga secara permanen beserta data terkait
     */
    public function destroy($id)
    {
        $report = LaporanBencana::findOrFail($id);

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
