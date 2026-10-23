@extends('layouts.admin')

@section('title', 'Dashboard Administrasi')
@section('header_title', 'Ikhtisar Kebencanaan Kabupaten Bogor')

@section('content')
<div class="space-y-6">

    <!-- 1. KARTU STATISTIK RINGKAS (KUNKER STYLE) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Laporan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Aduan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalReports) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Semua laporan terdata</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-orange-50 text-[#ea5921] border border-orange-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <!-- Menunggu Verifikasi (Pending) -->
        <a href="{{ route('admin.dashboard', ['status' => 'Pending']) }}" 
            class="bg-white p-5 rounded-2xl border border-amber-200/80 shadow-xs flex items-center justify-between hover:border-amber-300 hover:shadow-sm transition-all group">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Menunggu (Pending)</p>
                </div>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($pendingReports) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5 group-hover:text-amber-600 transition-colors">Perlu verifikasi &bull; Klik filter &rarr;</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                </svg>
            </div>
        </a>

        <!-- Sedang Ditangani (Handling) -->
        <a href="{{ route('admin.dashboard', ['status' => 'Handling']) }}" 
            class="bg-white p-5 rounded-2xl border border-blue-200/80 shadow-xs flex items-center justify-between hover:border-blue-300 hover:shadow-sm transition-all group">
            <div>
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Sedang Ditangani</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-blue-600 mt-1">{{ number_format($handlingReports) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5 group-hover:text-blue-600 transition-colors">Tim TRC di lokasi &bull; Klik filter &rarr;</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </a>

        <!-- Selesai (Completed) -->
        <a href="{{ route('admin.dashboard', ['status' => 'Selesai']) }}" 
            class="bg-white p-5 rounded-2xl border border-emerald-200/80 shadow-xs flex items-center justify-between hover:border-emerald-300 hover:shadow-sm transition-all group">
            <div>
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Penanganan Selesai</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($completedReports) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5 group-hover:text-emerald-600 transition-colors">Tuntas terkondisikan &bull; Klik filter &rarr;</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </a>

    </div>

    <!-- 2. FILTER & PENCARIAN SEDERHANA -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- Input Cari Kata Kunci -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Aduan / Pelapor</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, nama pelapor, atau alamat..." 
                        class="w-full text-xs sm:text-sm pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-[#ea5921] focus:ring-1 focus:ring-[#ea5921] outline-none transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Laporan</label>
                <select name="status" class="w-full text-xs sm:text-sm px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 focus:bg-white focus:border-[#ea5921] outline-none transition">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                    <option value="Handling" {{ request('status') == 'Handling' ? 'selected' : '' }}>Handling (Penanganan)</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <!-- Filter Jenis Bencana -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Jenis Bencana</label>
                <select name="bencana_id" class="w-full text-xs sm:text-sm px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 focus:bg-white focus:border-[#ea5921] outline-none transition">
                    <option value="">Semua Jenis Bencana</option>
                    @foreach($disasters as $d)
                    <option value="{{ $d->id_bencana }}" {{ request('bencana_id') == $d->id_bencana ? 'selected' : '' }}>
                        {{ $d->nama_bencana }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" 
                    class="flex-1 py-2 px-3.5 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white text-xs font-semibold shadow-xs transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['q', 'status', 'bencana_id', 'kecamatan_id']))
                <a href="{{ route('admin.dashboard') }}" 
                    class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Reset Filter">
                    Reset
                </a>
                @endif
            </div>

        </form>
    </div>

    <!-- 3. TABEL DATA LAPORAN (KUNKER MINIMALIST) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Header Tabel -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-base text-slate-900">Daftar Laporan Kejadian Bencana</h2>
                <p class="text-xs text-slate-500">Menampilkan {{ $reports->total() }} laporan terdata di seluruh wilayah Kabupaten Bogor</p>
            </div>
            
            <div class="text-xs text-slate-500 font-medium">
                Halaman {{ $reports->currentPage() }} dari {{ $reports->lastPage() }}
            </div>
        </div>

        <!-- Tabel Responsif -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4">ID & Waktu</th>
                        <th class="py-3 px-4">Jenis Bencana</th>
                        <th class="py-3 px-4">Judul & Pelapor</th>
                        <th class="py-3 px-4">Wilayah</th>
                        <th class="py-3 px-4 text-center">Korban</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($reports as $r)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        
                        <!-- ID & Waktu -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="font-mono font-bold text-slate-900 block">#{{ $r->id_laporan }}</span>
                            <span class="text-[11px] text-slate-400">
                                {{ $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-' }}
                            </span>
                        </td>

                        <!-- Jenis Bencana -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#ea5921]"></span>
                                <span class="font-semibold text-slate-800">
                                    {{ $r->bencana->nama_bencana ?? 'Bencana Lainnya' }}
                                </span>
                            </div>
                        </td>

                        <!-- Judul & Pelapor -->
                        <td class="py-3.5 px-4 max-w-xs">
                            <p class="font-semibold text-slate-900 truncate leading-snug" title="{{ $r->judul }}">
                                {{ $r->judul }}
                            </p>
                            <p class="text-[11px] text-slate-500 truncate">
                                Pelapor: <span class="font-medium text-slate-700">{{ $r->nama_pelapor ?: 'Warga' }}</span> 
                                @if($r->nomor_telepon)
                                &bull; {{ $r->nomor_telepon }}
                                @endif
                            </p>
                        </td>

                        <!-- Wilayah -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <p class="font-semibold text-slate-800">
                                Kec. {{ $r->kecamatan->nama_kecamatan ?? '-' }}
                            </p>
                            <p class="text-[11px] text-slate-500">
                                Desa/Kel. {{ $r->kelurahan->nama_kelurahan ?? '-' }}
                            </p>
                        </td>

                        <!-- Korban -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if(($r->jumlah_korban ?? 0) > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                                {{ $r->jumlah_korban }} Jiwa
                            </span>
                            @else
                            <span class="text-slate-400 text-xs">Nihil</span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if($r->status === 'Pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Pending
                                </span>
                            @elseif($r->status === 'Handling')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    Handling
                                </span>
                            @elseif($r->status === 'Selesai')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Selesai
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $r->status }}
                                </span>
                            @endif
                        </td>

                        <!-- Aksi Cepat -->
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1">
                                <!-- Tombol Lihat Detail Modal -->
                                <button type="button" onclick="openDetailModal({{ json_encode($r) }})" 
                                    class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" 
                                    title="Lihat Detail Aduan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>

                                <!-- Form Ubah Status Cepat -->
                                <form action="{{ route('admin.laporan.status', $r->id_laporan) }}" method="POST" class="inline-block">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" 
                                        class="text-[11px] font-semibold py-1 px-2 rounded-lg border border-slate-200 bg-white hover:border-slate-300 text-slate-700 cursor-pointer outline-none transition">
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
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="font-semibold text-slate-600">Tidak ada data laporan yang sesuai filter.</p>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci atau bersihkan filter pencarian.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reports->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $reports->links() }}
        </div>
        @endif

    </div>

    <!-- 4. STATISTIK PER JENIS BENCANA (BOTTOM WIDGET) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Bar Sebaran Bencana -->
        <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <h3 class="font-bold text-sm text-slate-900 mb-1">Sebaran Jenis Bencana Terbanyak</h3>
            <p class="text-xs text-slate-500 mb-4">Frekuensi kejadian bencana berdasarkan aduan yang masuk</p>

            <div class="space-y-3.5">
                @foreach($disasterStats as $ds)
                @php
                    $pct = $totalReports > 0 ? round(($ds->laporan_bencana_count / $totalReports) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-800">{{ $ds->nama_bencana }}</span>
                        <span class="text-slate-500">{{ $ds->laporan_bencana_count }} Laporan ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-[#ea5921]" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Info Siaga & Kontak TRC BPBD -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 rounded-2xl border border-slate-700 shadow-xs flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-white/10 p-2 flex items-center justify-center border border-white/15 mb-3">
                    <img src="{{ asset('images/aksara-logo.png') }}" alt="BPBD" class="w-full h-full object-contain">
                </div>
                <h4 class="font-bold text-base leading-tight mb-1">PUSDALOPS-PB 24/7</h4>
                <p class="text-xs text-slate-300 leading-relaxed mb-4">
                    Pusat Pengendalian Operasi Penanggulangan Bencana Kabupaten Bogor selalu siaga melayani koordinasi evakuasi dan logistik.
                </p>
                
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between py-1.5 px-3 rounded-lg bg-white/5 border border-white/10">
                        <span class="text-slate-300">Call Center BPBD:</span>
                        <span class="font-bold text-orange-400">021-87914800</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 px-3 rounded-lg bg-white/5 border border-white/10">
                        <span class="text-slate-300">WhatsApp Darurat:</span>
                        <span class="font-bold text-emerald-400">0812-1010-9008</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-white/10 mt-4 text-[11px] text-slate-400">
                Kabupaten Bogor Tangguh Bencana
            </div>
        </div>

    </div>

</div>

<!-- 5. MODAL DETAIL LAPORAN INTERAKTIF -->
<div id="detailModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-fade-in">
        
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <span id="modal-id" class="text-[11px] font-mono font-bold text-orange-600 block">#</span>
                <h3 id="modal-title" class="font-extrabold text-base text-slate-900 leading-tight">Detail Laporan</h3>
            </div>
            <button type="button" onclick="closeDetailModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto text-xs sm:text-sm">
            <div>
                <label class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Deskripsi Aduan</label>
                <p id="modal-desc" class="text-slate-800 leading-relaxed mt-0.5 bg-slate-50 p-3 rounded-xl border border-slate-200/80">-</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pelapor</span>
                    <span id="modal-reporter" class="font-semibold text-slate-800 block mt-0.5">-</span>
                    <span id="modal-phone" class="text-[11px] text-slate-500 block">-</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi & Wilayah</span>
                    <span id="modal-location" class="font-semibold text-slate-800 block mt-0.5">-</span>
                    <span id="modal-address" class="text-[11px] text-slate-500 block truncate">-</span>
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Kebutuhan Logistik & Evakuasi</label>
                <p id="modal-logistics" class="text-slate-700 mt-0.5 bg-orange-50/50 border border-orange-100 p-2.5 rounded-xl font-medium">-</p>
            </div>

            <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                <span class="text-slate-400">Koordinat: <span id="modal-coords" class="font-mono text-slate-600">-</span></span>
                <span id="modal-status-badge" class="font-bold px-2.5 py-0.5 rounded-full text-xs">-</span>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold text-xs transition">
                Tutup
            </button>
        </div>

    </div>
</div>

<script>
    function openDetailModal(report) {
        document.getElementById('modal-id').innerText = '#' + report.id_laporan;
        document.getElementById('modal-title').innerText = report.judul || 'Laporan Kejadian';
        document.getElementById('modal-desc').innerText = report.deskripsi || 'Tidak ada deskripsi rinci.';
        document.getElementById('modal-reporter').innerText = report.nama_pelapor || 'Warga';
        document.getElementById('modal-phone').innerText = report.nomor_telepon ? 'Telp: ' + report.nomor_telepon : '-';
        
        const district = report.kecamatan ? report.kecamatan.nama_kecamatan : '-';
        const village = report.kelurahan ? report.kelurahan.nama_kelurahan : '-';
        document.getElementById('modal-location').innerText = 'Kec. ' + district + ', Desa ' + village;
        document.getElementById('modal-address').innerText = report.alamat_detail || '-';
        document.getElementById('modal-logistics').innerText = report.kebutuhan_logistik || 'Tidak ada catatan logistik khusus.';
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
@endsection
