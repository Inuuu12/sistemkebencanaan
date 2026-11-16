<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bencana;
use App\Models\DaerahRawan;
use App\Models\LaporanBencana;
use Illuminate\Http\Request;

class BencanaController extends Controller
{
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
