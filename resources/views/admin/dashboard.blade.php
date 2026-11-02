@extends('layouts.admin')

@section('title', 'Dashboard Administrasi')
@section('header_title', 'Pusat Komando & Pemantauan Kebencanaan')

@push('styles')
<!-- Leaflet CSS untuk GIS Live Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Styling Peta GIS */
    #liveGisMap {
        height: 480px;
        width: 100%;
        border-radius: 1rem;
        z-index: 10;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        padding: 0.25rem;
    }
    .leaflet-popup-content {
        margin: 0.75rem 1rem;
        line-height: 1.4;
    }
</style>
@endpush

@section('content')
<div class="space-y-8 pb-12">

    <!-- HEADER TITLE & LIVE TIME -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Monitoring</h1>
        </div>
    </div>

    <!-- 1. KARTU STATISTIK RINGKAS UTAMA -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Total Bencana -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-slate-300 transition-colors">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Bencana</p>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format($totalBencana ?? 8) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Jenis terdata</p>
            </div>
            
        </div>

        <!-- Total Laporan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-slate-300 transition-colors">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Aduan</p>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format($totalReports ?? 13) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Aduan masyarakat</p>
            </div>
        </div>

        <!-- Total Pengungsi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-slate-300 transition-colors">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pengungsi</p>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format($totalPengungsi ?? 12) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Jiwa di posko</p>
            </div>
        </div>

        <!-- Total Korban Jiwa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between hover:border-slate-300 transition-colors">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Korban Jiwa</p>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format($totalKorbanJiwa ?? 0) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Meninggal dunia</p>
            </div>
        </div>

    </div>



    <!-- 3. PETA SEBARAN BENCANA INTERAKTIF (GIS LIVE MAP) -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#ea5921]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Peta Sebaran Bencana Interaktif (GIS Live Map)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Visualisasi realtime titik kejadian, zonasi risiko rawan, dan lokasi posko logistik BPBD</p>
            </div>

            <!-- Layer Toggle Control (Filter Checklist) -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 cursor-pointer select-none">
                    <input type="checkbox" id="toggleDisasters" checked class="rounded text-[#ea5921] focus:ring-orange-500 cursor-pointer">
                    <span class="font-medium text-slate-700">Titik Bencana ({{ count($mapReports) }})</span>
                </label>
                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 cursor-pointer select-none">
                    <input type="checkbox" id="toggleZones" checked class="rounded text-red-600 focus:ring-red-500 cursor-pointer">
                    <span class="font-medium text-slate-700">Zonasi Rawan ({{ count($zonasiList) }})</span>
                </label>
                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-50 cursor-pointer select-none">
                    <input type="checkbox" id="togglePosko" checked class="rounded text-blue-600 focus:ring-blue-500 cursor-pointer">
                    <span class="font-medium text-slate-700">Posko & Logistik ({{ count($poskoList) }})</span>
                </label>
            </div>
        </div>

        <!-- Wadah Leaflet Map -->
        <div id="liveGisMap" class="shadow-inner border border-slate-200"></div>

        <!-- Keterangan Legend Peta di Bawah -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600">
            <div class="flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded-full bg-[#ea5921] border-2 border-white shadow-xs"></span>
                <span><strong>Titik Kejadian:</strong> Laporan kejadian diverifikasi & dipantau</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded-full bg-red-500/80 border border-red-600"></span>
                <span><strong>Zonasi Risiko:</strong> Merah (Tinggi), Kuning (Sedang), Hijau (Aman)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3.5 h-3.5 rounded-full bg-blue-600 border-2 border-white shadow-xs"></span>
                <span><strong>Posko & Gudang:</strong> Titik evakuasi pengungsi & cadangan logistik</span>
            </div>
        </div>

    </div>

    <!-- 4. GRAFIK & STATISTIK TREN (CHARTS) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Grafik 1: Tren Kejadian Bencana dari Tahun ke Tahun (Persis Seperti Gambar User) -->
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <!-- Header Grafik Style User -->
                <div class="text-center mb-4">
                    <h3 class="font-black text-sm sm:text-base text-slate-800 uppercase tracking-wide">
                        Grafik Kejadian Bencana dari Tahun ke Tahun
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tren frekuensi bencana Kabupaten Bogor (2018 - 2026)</p>

                    <!-- Custom Legend Box persis seperti gambar contoh -->
                    <div class="inline-flex items-center gap-2 mt-3 px-3 py-1 rounded-md border border-slate-200 bg-slate-50/70">
                        <div class="w-8 h-3.5 rounded-xs border-2 border-[#166534] bg-[#fde047]"></div>
                        <span class="text-xs font-bold text-slate-700">Kejadian Bencana</span>
                    </div>
                </div>

                <!-- Canvas Chart -->
                <div class="relative w-full h-64 sm:h-72">
                    <canvas id="yearlyDisasterChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-400 text-center">
                Sumber: Rekapitulasi Data PUSDALOPS-PB BPBD Kabupaten Bogor
            </div>
        </div>
    </div>

    <!-- 5. STATUS STOK LOGISTIK KRITIS -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-5">
            <div>
                <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                    Status Stok Bantuan Logistik Kritis
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring ketersediaan 4 kategori bantuan darurat utama di Gudang BPBD</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">
                Gudang Induk Cibinong
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($logisticsStock as $item)
            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-800">{{ $item['category'] }}</span>
                    @if($item['status'] === 'KRITIS')
                    <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-red-100 text-red-700 animate-pulse">
                        {{ $item['status'] }}
                    </span>
                    @elseif($item['status'] === 'Waspada')
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800">
                        {{ $item['status'] }}
                    </span>
                    @else
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                        {{ $item['status'] }}
                    </span>
                    @endif
                </div>

                <div class="flex items-baseline justify-between mb-1.5">
                    <span class="text-xl font-extrabold text-slate-900">{{ number_format($item['stock']) }} <span class="text-xs font-medium text-slate-500">{{ $item['unit'] }}</span></span>
                    <span class="text-xs font-bold text-slate-500">{{ $item['percent'] }}%</span>
                </div>

                <!-- Progress Bar Stok -->
                <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden mb-2">
                    @php
                        $barColor = $item['status'] === 'KRITIS' ? 'bg-red-500' : ($item['status'] === 'Waspada' ? 'bg-amber-500' : 'bg-emerald-500');
                    @endphp
                    <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $item['percent'] }}%"></div>
                </div>

                <p class="text-[11px] text-slate-500 truncate" title="{{ $item['items'] }}">
                    {{ $item['items'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 6. TABEL LAPORAN TERBARU & TINDAKAN CEPAT (RECENT ACTIVITIES / FEEDS) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                    Aduan Warga Masuk Terbaru 
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar laporan darurat yang memerlukan respon cepat, validasi, dan penanganan Tim Reaksi Cepat (TRC)</p>
            </div>
            <a href="{{ route('admin.dashboard', ['status' => 'Pending']) }}" 
                class="text-xs font-bold text-orange-600 hover:text-orange-700 inline-flex items-center gap-1">
                <span>Lihat Semua Aduan Pending</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Waktu & Bukti Foto</th>
                        <th class="py-3 px-4">Jenis Bencana</th>
                        <th class="py-3 px-4">Judul Aduan & Pelapor</th>
                        <th class="py-3 px-4">Wilayah & Koordinat GPS</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Tindakan Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($recentReports as $r)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        
                        <!-- Waktu & Foto Thumbnail -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-2.5">
                                @php
                                    $photo = null;
                                    if ($r->foto_laporan) {
                                        $decoded = is_array($r->foto_laporan) ? $r->foto_laporan : json_decode($r->foto_laporan, true);
                                        $photo = is_array($decoded) ? ($decoded[0] ?? null) : $r->foto_laporan;
                                    }
                                @endphp
                                @if($photo)
                                <img src="{{ asset($photo) }}" alt="Foto" class="w-10 h-10 rounded-lg object-cover border border-slate-200 shrink-0">
                                @else
                                <div class="w-10 h-10 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    BPBD
                                </div>
                                @endif
                                <div>
                                    <span class="font-mono font-bold text-slate-900 block">#{{ $r->id_laporan }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $r->created_at ? $r->created_at->diffForHumans() : '-' }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Jenis Bencana -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800 block">
                                {{ $r->bencana->nama_bencana ?? 'Bencana Lain' }}
                            </span>
                        </td>

                        <!-- Judul Aduan & Pelapor -->
                        <td class="py-3 px-4 max-w-xs">
                            <p class="font-semibold text-slate-900 truncate" title="{{ $r->judul }}">
                                {{ $r->judul }}
                            </p>
                            <p class="text-[11px] text-slate-500 truncate">
                                Pelapor: <span class="text-slate-700 font-medium">{{ $r->nama_pelapor ?: 'Warga' }}</span>
                                @if($r->nomor_telepon)
                                &bull; {{ $r->nomor_telepon }}
                                @endif
                            </p>
                        </td>

                        <!-- Wilayah & Koordinat GPS -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <p class="font-semibold text-slate-800">
                                Kec. {{ $r->kecamatan->nama_kecamatan ?? '-' }}, Desa {{ $r->kelurahan->nama_kelurahan ?? '-' }}
                            </p>
                            <p class="text-[11px] font-mono text-slate-500">
                                GPS: {{ round($r->latitude, 4) }}, {{ round($r->longitude, 4) }}
                            </p>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($r->status === 'Pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pending
                                </span>
                            @elseif($r->status === 'Handling')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    Handling
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Selesai
                                </span>
                            @endif
                        </td>

                        <!-- Tombol Cepat Tinjau / Validasi -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <button type="button" onclick="openDetailModal({{ json_encode($r) }})" 
                                    class="px-3 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-[#ea5921] font-bold text-xs transition border border-orange-200/80">
                                    Tinjau / Validasi
                                </button>
                                <form action="{{ route('admin.laporan.status', $r->id_laporan) }}" method="POST" class="inline-block">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" 
                                        class="text-xs font-semibold py-1 px-2 rounded-lg border border-slate-200 bg-white hover:border-slate-300 text-slate-700 cursor-pointer outline-none transition" title="Ubah Status Cepat">
                                        <option value="Pending" {{ $r->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Handling" {{ $r->status == 'Handling' ? 'selected' : '' }}>Handling</option>
                                        <option value="Selesai" {{ $r->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            Belum ada laporan darurat yang masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- MODAL DETAIL & TINJAU LAPORAN INTERAKTIF -->
<div id="detailModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-fade-in">
        
        <!-- Header Modal -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
                <span id="modal-id" class="text-[11px] font-mono font-bold text-orange-600 block">#</span>
                <h3 id="modal-title" class="font-extrabold text-base text-slate-900 leading-tight">Detail Laporan Bencana</h3>
            </div>
            <button type="button" onclick="closeDetailModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-xs sm:text-sm">
            <div>
                <label class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Deskripsi Kejadian</label>
                <p id="modal-desc" class="text-slate-800 leading-relaxed mt-0.5 bg-slate-50 p-3 rounded-xl border border-slate-200/80">-</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pelapor</span>
                    <span id="modal-reporter" class="font-semibold text-slate-800 block mt-0.5">-</span>
                    <span id="modal-phone" class="text-[11px] text-slate-500 block">-</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Wilayah & Alamat</span>
                    <span id="modal-location" class="font-semibold text-slate-800 block mt-0.5">-</span>
                    <span id="modal-address" class="text-[11px] text-slate-500 block truncate">-</span>
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Kebutuhan Logistik & Evakuasi</label>
                <p id="modal-logistics" class="text-slate-700 mt-0.5 bg-orange-50/50 border border-orange-100 p-2.5 rounded-xl font-medium">-</p>
            </div>

            <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                <span class="text-slate-500">Koordinat GPS: <span id="modal-coords" class="font-mono text-slate-800 font-bold">-</span></span>
                <span id="modal-status-badge" class="font-bold px-2.5 py-0.5 rounded-full text-xs">-</span>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">Verifikasi status dapat diubah langsung dari dropdown tabel.</span>
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold text-xs transition">
                Tutup
            </button>
        </div>

    </div>
</div>

@push('scripts')
<!-- Leaflet & Chart.js Script Library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // ==========================================
        // 1. INISIALISASI PETA GIS LIVE MAP (LEAFLET)
        // ==========================================
        const bogorCenter = [-6.5950, 106.8166]; // Pusat Kabupaten Bogor
        const map = L.map('liveGisMap').setView(bogorCenter, 11);

        // Tile Layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap & Pusdalops BPBD Bogor'
        }).addTo(map);

        // Layer Groups untuk Toggle
        const disasterLayerGroup = L.layerGroup().addTo(map);
        const zoneLayerGroup = L.layerGroup().addTo(map);
        const poskoLayerGroup = L.layerGroup().addTo(map);

        // A. Marker Titik Bencana
        const disasterPoints = {!! json_encode($mapReports ?? []) !!};
        disasterPoints.forEach(function (dp) {
            if (dp.lat && dp.lng) {
                const marker = L.circleMarker([dp.lat, dp.lng], {
                    radius: 8,
                    fillColor: '#ea5921',
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                });

                const popupContent = `
                    <div class="text-xs">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-orange-100 text-orange-800">${dp.bencana}</span>
                        <h4 class="font-bold text-slate-900 text-sm mt-1">${dp.title}</h4>
                        <p class="text-slate-500 mt-0.5">${dp.kecamatan}, Desa ${dp.kelurahan}</p>
                        <p class="text-slate-600 mt-1">${dp.address || ''}</p>
                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="font-semibold text-slate-700">Status: ${dp.status}</span>
                            <span class="font-semibold text-red-600">${dp.victims} Korban</span>
                        </div>
                    </div>
                `;
                marker.bindPopup(popupContent);
                disasterLayerGroup.addLayer(marker);
            }
        });

        // B. Poligon / Lingkaran Zonasi Rawan (Merah, Kuning, Hijau)
        const zones = {!! json_encode($zonasiList ?? []) !!};
        zones.forEach(function (z) {
            const circle = L.circle([z.lat, z.lng], {
                color: z.color,
                fillColor: z.color,
                fillOpacity: 0.18,
                weight: 2,
                radius: z.radius
            });

            const zonePopup = `
                <div class="text-xs">
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider" style="background-color: ${z.color}20; color: ${z.color};">
                        ${z.level} - ${z.type}
                    </span>
                    <h4 class="font-bold text-slate-900 text-sm mt-1">${z.name}</h4>
                    <p class="text-slate-600 mt-1">${z.description}</p>
                    <p class="text-slate-400 mt-1 text-[11px]">Radius Bahaya: ${z.radius} meter</p>
                </div>
            `;
            circle.bindPopup(zonePopup);
            zoneLayerGroup.addLayer(circle);
        });

        // C. Titik Posko Evakuasi & Gudang Logistik
        const poskos = {!! json_encode($poskoList ?? []) !!};
        poskos.forEach(function (p) {
            const poskoMarker = L.circleMarker([p.lat, p.lng], {
                radius: 7,
                fillColor: '#2563eb',
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.95
            });

            const poskoPopup = `
                <div class="text-xs">
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800">POSKO & LOGISTIK</span>
                    <h4 class="font-bold text-slate-900 text-sm mt-1">${p.name}</h4>
                    <p class="text-slate-600 mt-0.5">${p.type}</p>
                    <p class="text-slate-500 mt-1 font-medium">Kapasitas: ${p.capacity}</p>
                    <p class="text-blue-600 font-bold mt-1">Telp: ${p.phone}</p>
                </div>
            `;
            poskoMarker.bindPopup(poskoPopup);
            poskoLayerGroup.addLayer(poskoMarker);
        });

        // Layer Toggles Event Listeners
        document.getElementById('toggleDisasters').addEventListener('change', function (e) {
            if (e.target.checked) map.addLayer(disasterLayerGroup);
            else map.removeLayer(disasterLayerGroup);
        });

        document.getElementById('toggleZones').addEventListener('change', function (e) {
            if (e.target.checked) map.addLayer(zoneLayerGroup);
            else map.removeLayer(zoneLayerGroup);
        });

        document.getElementById('togglePosko').addEventListener('change', function (e) {
            if (e.target.checked) map.addLayer(poskoLayerGroup);
            else map.removeLayer(poskoLayerGroup);
        });


        // ==========================================
        // 2. GRAFIK TREN KEJADIAN TAHUNAN (SESUAI GAMBAR)
        // ==========================================
        const yearlyCtx = document.getElementById('yearlyDisasterChart').getContext('2d');
        const yearlyLabels = {!! json_encode($yearlyTrend['labels'] ?? []) !!};
        const yearlyData = {!! json_encode($yearlyTrend['data'] ?? []) !!};

        new Chart(yearlyCtx, {
            type: 'line',
            data: {
                labels: yearlyLabels,
                datasets: [{
                    label: 'Kejadian Bencana',
                    data: yearlyData,
                    borderColor: '#166534', // Hijau tua tegas sesuai gambar referensi user
                    borderWidth: 3,
                    backgroundColor: '#fde047', // Isi kuning pada titik (dot)
                    pointBackgroundColor: '#fde047',
                    pointBorderColor: '#166534',
                    pointBorderWidth: 2,
                    pointRadius: 5.5,
                    pointHoverRadius: 7.5,
                    tension: 0, // Garis lurus antar titik persis seperti di gambar
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        callbacks: {
                            label: function(c) { return c.parsed.y + ' Kejadian Bencana'; }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#475569', font: { weight: '600' } }
                    },
                    y: {
                        min: 0,
                        max: 140,
                        ticks: { stepSize: 20, color: '#475569', font: { weight: '600' } },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });


        // ==========================================
        // 3. GRAFIK PERSEBARAN KORBAN & LAPORAN PER WILAYAH
        // ==========================================
        const impactCtx = document.getElementById('districtImpactChart').getContext('2d');
        const districtLabels = {!! json_encode($districtImpact['labels'] ?? []) !!};
        const districtKorban = {!! json_encode($districtImpact['korban'] ?? []) !!};
        const districtLaporan = {!! json_encode($districtImpact['laporan'] ?? []) !!};

        new Chart(impactCtx, {
            type: 'bar',
            data: {
                labels: districtLabels,
                datasets: [
                    {
                        label: 'Laporan Bencana',
                        data: districtLaporan,
                        backgroundColor: '#ea5921',
                        borderRadius: 6
                    },
                    {
                        label: 'Korban Jiwa/Luka',
                        data: districtKorban,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { size: 11, weight: '600' } }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 2 },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });

    });

    // Modal Detail functions
    function openDetailModal(report) {
        document.getElementById('modal-id').innerText = '#' + report.id_laporan;
        document.getElementById('modal-title').innerText = report.judul || 'Detail Laporan';
        document.getElementById('modal-desc').innerText = report.deskripsi || 'Tidak ada deskripsi.';
        document.getElementById('modal-reporter').innerText = report.nama_pelapor || 'Warga';
        document.getElementById('modal-phone').innerText = report.nomor_telepon ? 'Telp: ' + report.nomor_telepon : '-';
        
        const district = report.kecamatan ? report.kecamatan.nama_kecamatan : '-';
        const village = report.kelurahan ? report.kelurahan.nama_kelurahan : '-';
        document.getElementById('modal-location').innerText = 'Kec. ' + district + ', Desa ' + village;
        document.getElementById('modal-address').innerText = report.alamat_detail || '-';
        document.getElementById('modal-logistics').innerText = report.kebutuhan_logistik || 'Tidak ada logistik khusus.';
        document.getElementById('modal-coords').innerText = (report.latitude && report.longitude) ? report.latitude + ', ' + report.longitude : '-';

        const badge = document.getElementById('modal-status-badge');
        badge.innerText = report.status;
        if (report.status === 'Pending') {
            badge.className = 'font-bold px-2.5 py-0.5 rounded-full text-xs bg-amber-100 text-amber-800';
        } else if (report.status === 'Handling') {
            badge.className = 'font-bold px-2.5 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800';
        } else {
            badge.className = 'font-bold px-2.5 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-800';
        }

        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDetailModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endpush
@endsection
