<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\LaporanBencana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Tampilan Dashboard Admin sederhana & elegan (style kunker)
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

        // Statistik Ringkas
        $totalReports = LaporanBencana::count();
        $pendingReports = LaporanBencana::where('status', 'Pending')->count();
        $handlingReports = LaporanBencana::where('status', 'Handling')->count();
        $completedReports = LaporanBencana::where('status', 'Selesai')->count();

        // Data Referensi
        $disasters = Bencana::orderBy('nama_bencana')->get();
        $districts = Kecamatan::orderBy('nama_kecamatan')->get();

        // Statistik per jenis bencana
        $disasterStats = Bencana::withCount('laporanBencana')
            ->having('laporan_bencana_count', '>', 0)
            ->orderByDesc('laporan_bencana_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'reports',
            'totalReports',
            'pendingReports',
            'handlingReports',
            'completedReports',
            'disasters',
            'districts',
            'disasterStats'
        ));
    }

    /**
     * Update status penanganan laporan secara cepat
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Handling,Selesai',
        ]);

        $report = LaporanBencana::findOrFail($id);
        $oldStatus = $report->status;
        $report->status = $request->status;
        $report->save();

        return back()->with('success', "Status laporan #{$report->id_laporan} berhasil diperbarui dari {$oldStatus} menjadi {$report->status}.");
    }
}
