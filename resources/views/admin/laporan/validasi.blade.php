@extends('layouts.admin')

@section('title', 'Validasi Aduan Warga - Laporan Darurat')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER HALAMAN & BREADCRUMB -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Validasi Aduan Warga</span>
            </h1>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                <span>Lihat di Peta GIS</span>
            </a>
        </div>
    </div>

    <!-- 2. STATUS TAB COUNTER (Pills Navigation) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <!-- Tab: Semua -->
        <a href="{{ route('admin.laporan.validasi', array_merge(request()->except('status', 'page'), [])) }}" 
           class="p-3.5 rounded-2xl border transition-all flex items-center justify-between {{ !request('status') ? 'bg-white border-slate-300 text-white shadow-md' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider block {{ !request('status') ? 'text-black' : 'text-slate-400' }}">Semua Aduan</span>
                <span class="text-xl font-extrabold text-black block mt-0.5">{{ $totalAll }}</span>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ !request('status') ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
        </a>

        <!-- Tab: Menunggu Validasi -->
        <a href="{{ route('admin.laporan.validasi', array_merge(request()->except('status', 'page'), ['status' => 'Pending'])) }}" 
           class="p-3.5 rounded-2xl border transition-all flex items-center justify-between {{ request('status') === 'Pending' ? 'bg-amber-500 border-amber-500 text-white shadow-md' : 'bg-white border-slate-200 text-slate-700 hover:border-amber-300 hover:bg-amber-50/40' }}">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider block {{ request('status') === 'Pending' ? 'text-amber-100' : 'text-amber-600' }}">Perlu Validasi</span>
                <span class="text-xl font-extrabold block mt-0.5">{{ $totalPending }}</span>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ request('status') === 'Pending' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </a>

        <!-- Tab: Sedang Ditangani -->
        <a href="{{ route('admin.laporan.validasi', array_merge(request()->except('status', 'page'), ['status' => 'Handling'])) }}" 
           class="p-3.5 rounded-2xl border transition-all flex items-center justify-between {{ request('status') === 'Handling' ? 'bg-blue-600 border-blue-600 text-white shadow-md' : 'bg-white border-slate-200 text-slate-700 hover:border-blue-300 hover:bg-blue-50/40' }}">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider block {{ request('status') === 'Handling' ? 'text-blue-100' : 'text-blue-600' }}">Ditangani</span>
                <span class="text-xl font-extrabold block mt-0.5">{{ $totalHandling }}</span>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ request('status') === 'Handling' ? 'bg-blue-700 text-white' : 'bg-blue-100 text-blue-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </a>

        <!-- Tab: Selesai -->
        <a href="{{ route('admin.laporan.validasi', array_merge(request()->except('status', 'page'), ['status' => 'Selesai'])) }}" 
           class="p-3.5 rounded-2xl border transition-all flex items-center justify-between {{ request('status') === 'Selesai' ? 'bg-emerald-600 border-emerald-600 text-white shadow-md' : 'bg-white border-slate-200 text-slate-700 hover:border-emerald-300 hover:bg-emerald-50/40' }}">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider block {{ request('status') === 'Selesai' ? 'text-emerald-100' : 'text-emerald-600' }}">Selesai</span>
                <span class="text-xl font-extrabold block mt-0.5">{{ $totalSelesai }}</span>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ request('status') === 'Selesai' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </a>

        <!-- Tab: Ditolak / Hoaks -->
        <a href="{{ route('admin.laporan.validasi', array_merge(request()->except('status', 'page'), ['status' => 'Ditolak'])) }}" 
           class="col-span-2 sm:col-span-1 p-3.5 rounded-2xl border transition-all flex items-center justify-between {{ request('status') === 'Ditolak' ? 'bg-rose-600 border-rose-600 text-white shadow-md' : 'bg-white border-slate-200 text-slate-700 hover:border-rose-300 hover:bg-rose-50/40' }}">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider block {{ request('status') === 'Ditolak' ? 'text-rose-100' : 'text-rose-600' }}">Ditolak</span>
                <span class="text-xl font-extrabold block mt-0.5">{{ $totalDitolak }}</span>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ request('status') === 'Ditolak' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </a>
    </div>

    <!-- 3. FILTER & PENCARIAN TIKET -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.laporan.validasi') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <!-- Search Field -->
            <div class="sm:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pelapor, judul aduan, alamat..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-slate-50/50">
            </div>

            <!-- Filter Jenis Bencana -->
            <div class="sm:col-span-3">
                <select name="bencana_id" 
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white text-slate-700">
                    <option value="">Semua Jenis Bencana</option>
                    @foreach($disasters as $d)
                    <option value="{{ $d->id_bencana }}" {{ request('bencana_id') == $d->id_bencana ? 'selected' : '' }}>
                        {{ $d->nama_bencana }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kecamatan -->
            <div class="sm:col-span-2">
                <select name="kecamatan_id" 
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition bg-white text-slate-700">
                    <option value="">Semua Wilayah</option>
                    @foreach($districts as $k)
                    <option value="{{ $k->id_kecamatan }}" {{ request('kecamatan_id') == $k->id_kecamatan ? 'selected' : '' }}>
                        {{ $k->nama_kecamatan }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition text-center shadow-xs">
                    Terapkan
                </button>
                @if(request()->hasAny(['q', 'bencana_id', 'kecamatan_id', 'status']))
                <a href="{{ route('admin.laporan.validasi') }}" 
                   class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition text-center" 
                   title="Reset Semua Filter">
                    Reset
                </a>
                @endif
            </div>

        </form>
    </div>

    <!-- 4. DAFTAR KARTU ADUAN DARURAT (Rich Operational List) -->
    <div class="space-y-4">
        @forelse($reports as $item)
            @php
                // Parse foto thumbnail
                $photo = null;
                $allPhotos = [];
                if ($item->foto_laporan) {
                    $decoded = is_array($item->foto_laporan) ? $item->foto_laporan : json_decode($item->foto_laporan, true);
                    if (is_array($decoded)) {
                        $allPhotos = $decoded;
                        $photo = $decoded[0] ?? null;
                    } else {
                        $photo = $item->foto_laporan;
                        $allPhotos = [$item->foto_laporan];
                    }
                }

                // Format nomor WhatsApp
                $phoneClean = preg_replace('/[^0-9]/', '', (string)$item->nomor_telepon);
                if (str_starts_with($phoneClean, '0')) {
                    $phoneClean = '62' . substr($phoneClean, 1);
                }
                $waText = urlencode("Halo Bpk/Ibu " . ($item->nama_pelapor ?: 'Warga') . ", kami dari Posko Komando BPBD Kabupaten Bogor ingin mengonfirmasi validasi laporan darurat: \"" . $item->judul . "\".");
                $waLink = $phoneClean ? "https://wa.me/{$phoneClean}?text={$waText}" : null;
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-slate-300 shadow-xs hover:shadow-md transition duration-200 overflow-hidden">
                
                <!-- Header Kartu: Status, Tiket ID, Waktu -->
                <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-200/80 text-slate-800 font-mono font-bold text-xs tracking-wide">
                            #ADU-{{ str_pad($item->id_laporan, 5, '0', STR_PAD_LEFT) }}
                        </span>

                        <span class="px-2.5 py-1 rounded-lg font-bold text-xs bg-orange-100 text-orange-800 border border-orange-200/60">
                            {{ $item->bencana->nama_bencana ?? 'Bencana Umum' }}
                        </span>

                        <span class="text-xs text-slate-400 font-medium hidden sm:inline">&bull;</span>

                        <span class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }} WIB
                            <span class="text-slate-400">({{ $item->created_at ? $item->created_at->diffForHumans() : '' }})</span>
                        </span>
                    </div>

                    <!-- Status Pill -->
                    <div>
                        @if($item->status === 'Pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Validasi
                            </span>
                        @elseif($item->status === 'Handling')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-xs">
                                Dalam Penanganan
                            </span>
                        @elseif($item->status === 'Selesai')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                                Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-xs">
                                Ditolak
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Konten Utama Kartu -->
                <div class="p-5 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Kolom Kiri: Bukti Foto (lg:col-span-3) -->
                    <div class="lg:col-span-3">
                        @if($photo)
                            <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-100 aspect-video lg:aspect-4/3 cursor-pointer"
                                 onclick="openPhotoLightbox('{{ asset($photo) }}', '{{ addslashes($item->judul) }}')">
                                <img src="{{ asset($photo) }}" alt="Bukti Aduan" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                    Perbesar Foto
                                </div>
                                <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-slate-900/70 text-white text-[10px] font-bold backdrop-blur-xs">
                                    Bukti Warga
                                </span>
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 aspect-video lg:aspect-4/3 flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                                <svg class="w-8 h-8 text-slate-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-medium">Tanpa Lampiran Foto</span>
                            </div>
                        @endif
                    </div>

                    <!-- Kolom Tengah: Rincian Aduan & Pelapor (lg:col-span-6) -->
                    <div class="lg:col-span-6 space-y-3.5">
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-snug">
                                {{ $item->judul }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed bg-slate-50/70 p-3 rounded-xl border border-slate-100">
                                {{ $item->deskripsi ?: 'Tidak ada rincian tambahan yang dicantumkan pelapor.' }}
                            </p>
                        </div>

                        <!-- Data Pelapor & Kontak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-white border border-slate-200/80">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pelapor</span>
                                <div class="font-bold text-slate-800 text-sm mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $item->nama_pelapor ?: 'Warga Anonim' }}</span>
                                </div>
                                @if($item->nomor_telepon)
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="font-mono text-slate-600">{{ $item->nomor_telepon }}</span>
                                    @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" 
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] border border-emerald-200 transition" 
                                       title="Hubungi pelapor via WhatsApp untuk verifikasi faktual">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.54 1.769.814 2.791.814 3.18 0 5.766-2.587 5.767-5.766.001-3.182-2.585-5.768-5.767-5.768zm0 10.355c-.886 0-1.72-.25-2.428-.702l-.174-.108-1.579.414.421-1.539-.117-.187c-.496-.787-.758-1.464-.758-2.467 0-2.518 2.05-4.568 4.568-4.568 2.517 0 4.567 2.05 4.568 4.568 0 2.518-2.051 4.568-4.568 4.568zm5.727-13.064c-2.39-2.39-5.568-3.708-8.95-3.708-6.974 0-12.65 5.676-12.65 12.65 0 2.228.581 4.402 1.685 6.315l-1.791 6.541 6.691-1.755c1.856.997 3.945 1.527 6.065 1.527 6.974 0 12.65-5.676 12.65-12.65 0-3.382-1.318-6.56-3.7-8.92zm-5.727 20.399c-1.884 0-3.724-.507-5.322-1.468l-.382-.228-3.957 1.038 1.056-3.856-.25-.398c-1.047-1.666-1.6-3.606-1.6-5.597 0-5.872 4.778-10.65 10.65-10.65 2.846 0 5.521 1.108 7.534 3.121 2.012 2.013 3.12 4.688 3.12 7.534 0 5.872-4.778 10.65-10.65 10.65z"/></svg>
                                        Chat WA
                                    </a>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <!-- Lokasi & Geotag -->
                            <div class="p-3 rounded-xl bg-white border border-slate-200/80">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi & Wilayah</span>
                                <div class="font-bold text-slate-800 text-sm mt-0.5">
                                    Kec. {{ $item->kecamatan->nama_kecamatan ?? '-' }}, Desa {{ $item->kelurahan->nama_kelurahan ?? '-' }}
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-1" title="{{ $item->alamat_detail }}">
                                    {{ $item->alamat_detail ?: 'Alamat detail tidak disebutkan' }}
                                </p>
                                @if($item->latitude && $item->longitude)
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="font-mono text-[11px] text-slate-500">{{ round($item->latitude, 4) }}, {{ round($item->longitude, 4) }}</span>
                                    <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank" rel="noopener noreferrer" 
                                       class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                        <span>Buka Maps</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Kebutuhan Mendesak & Korban -->
                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Korban Terdampak: <strong class="text-slate-900">{{ $item->jumlah_korban ?? 0 }} Jiwa</strong>
                            </span>

                            @if($item->kebutuhan_logistik)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-orange-50 text-orange-800 font-semibold text-xs border border-orange-200/80">
                                <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Logistik: <span class="font-normal">{{ Str::limit($item->kebutuhan_logistik, 35) }}</span>
                            </span>
                            @endif
                        </div>

                    </div>

                    <!-- Kolom Kanan: Aksi Validasi & Tanggapan Cepat (lg:col-span-3) -->
                    <div class="lg:col-span-3 bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">
                            Tindakan Petugas
                        </span>

                        <!-- Tombol Buka Modal Validasi Lengkap -->
                        <button type="button" 
                                onclick='openValidationModal(@json($item), @json($item->penanganan ?? null))'
                                class="w-full py-2.5 px-4 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-sm shadow-orange-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Validasi & Tindak Lanjut</span>
                        </button>

                        <!-- Aksi Cepat 1-Klik -->
                        <div class="space-y-1.5 pt-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ubah Status Cepat:</span>
                            
                            <form action="{{ route('admin.laporan.status', $item->id_laporan) }}" method="POST" class="grid grid-cols-2 gap-1.5">
                                @csrf
                                @if($item->status !== 'Handling')
                                <button type="submit" name="status" value="Handling" 
                                        class="py-1.5 px-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] border border-blue-200 transition text-center" 
                                        title="Kirim Tim TRC Lapangan">
                                    🚀 Kirim TRC
                                </button>
                                @endif

                                @if($item->status !== 'Selesai')
                                <button type="submit" name="status" value="Selesai" 
                                        class="py-1.5 px-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] border border-emerald-200 transition text-center" 
                                        title="Tandai Masalah Selesai">
                                    ✓ Selesai
                                </button>
                                @endif

                                @if($item->status !== 'Ditolak')
                                <button type="submit" name="status" value="Ditolak" 
                                        onclick="return confirm('Apakah Anda yakin ingin menolak laporan ini sebagai data fiktif / di luar wilayah?')"
                                        class="py-1.5 px-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200 transition text-center col-span-2" 
                                        title="Tolak Laporan Fiktif">
                                    ✕ Tolak / Laporan Fiktif
                                </button>
                                @endif
                            </form>
                        </div>

                        <!-- Catatan Riwayat Terakhir jika ada -->
                        @if($item->penanganan && $item->penanganan->count() > 0)
                        <div class="pt-2 border-t border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Terakhir:</span>
                            <p class="text-[11px] text-slate-600 italic mt-0.5 bg-white p-2 rounded-lg border border-slate-100">
                                "{{ Str::limit($item->penanganan->last()->catatan, 65) }}"
                            </p>
                        </div>
                        @endif

                    </div>

                </div>

            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-orange-50 border border-orange-100 text-[#ea5921] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h4 class="text-base font-bold text-slate-900">Tidak ada aduan warga ditemukan</h4>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Tidak ada laporan bencana yang cocok dengan kriteria filter saat ini. Coba ubah kata kunci pencarian atau reset filter.
                </p>
                <div class="mt-4">
                    <a href="{{ route('admin.laporan.validasi') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition">
                        Reset Filter
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- 5. PAGINASI -->
    @if($reports->hasPages())
    <div class="bg-white rounded-2xl border border-slate-200 px-5 py-4 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <span class="text-slate-500">
            Menampilkan <span class="font-bold text-slate-800">{{ $reports->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-slate-800">{{ $reports->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ $reports->total() }}</span> aduan
        </span>
        <div>
            {{ $reports->links() }}
        </div>
    </div>
    @endif

</div>

<!-- ======================================================== -->
<!-- MODAL VALIDASI & DISPOSISI LAPORAN ADUAN                 -->
<!-- ======================================================== -->
<div id="validationModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-fade-in flex flex-col max-h-[90vh]">
        
        <!-- Header Modal -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
            <div>
                <span id="vmodal-ticket" class="text-xs font-mono font-bold text-orange-600 block">#ADU-00000</span>
                <h3 id="vmodal-title" class="font-extrabold text-base text-slate-900 leading-tight">Validasi Aduan Warga</h3>
            </div>
            <button type="button" onclick="closeValidationModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Body Form -->
        <form id="vmodal-form" method="POST" action="" class="flex-1 overflow-y-auto p-5 space-y-4 text-xs sm:text-sm">
            @csrf

            <!-- Ringkasan Info Pelapor & Lokasi -->
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Pelapor: <strong id="vmodal-reporter" class="text-slate-800">-</strong></span>
                    <span class="text-slate-500">No. HP: <strong id="vmodal-phone" class="font-mono text-slate-800">-</strong></span>
                </div>
                <div class="flex items-center justify-between border-t border-slate-200/60 pt-2">
                    <span class="text-slate-500">Wilayah: <strong id="vmodal-location" class="text-slate-800">-</strong></span>
                    <span class="text-slate-500">Bencana: <strong id="vmodal-disaster" class="text-orange-600 font-bold">-</strong></span>
                </div>
                <div class="border-t border-slate-200/60 pt-2 text-slate-600">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Deskripsi:</span>
                    <p id="vmodal-desc" class="mt-0.5 leading-relaxed text-slate-700">-</p>
                </div>
            </div>

            <!-- Pilihan Status Validasi -->
            <div>
                <label class="font-bold text-slate-700 text-xs block mb-2">
                    Tetapkan Status Validasi & Penanganan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    
                    <!-- Opsi: Pending -->
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-amber-300 hover:bg-amber-50/30 cursor-pointer transition">
                        <input type="radio" name="status" value="Pending" id="status-pending" class="text-amber-500 focus:ring-amber-400">
                        <div>
                            <span class="font-bold text-slate-800 block">Menunggu Validasi</span>
                            <span class="text-[10px] text-slate-500 block">Perlu verifikasi lebih lanjut</span>
                        </div>
                    </label>

                    <!-- Opsi: Handling -->
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/30 cursor-pointer transition">
                        <input type="radio" name="status" value="Handling" id="status-handling" class="text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-blue-700 block">Terima & Kirim TRC</span>
                            <span class="text-[10px] text-slate-500 block">Disposisi tim lapangan aktif</span>
                        </div>
                    </label>

                    <!-- Opsi: Selesai -->
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/30 cursor-pointer transition">
                        <input type="radio" name="status" value="Selesai" id="status-selesai" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="font-bold text-emerald-700 block">Tandai Selesai</span>
                            <span class="text-[10px] text-slate-500 block">Situasi sudah terkendali</span>
                        </div>
                    </label>

                    <!-- Opsi: Ditolak -->
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-rose-300 hover:bg-rose-50/30 cursor-pointer transition">
                        <input type="radio" name="status" value="Ditolak" id="status-ditolak" class="text-rose-600 focus:ring-rose-500">
                        <div>
                            <span class="font-bold text-rose-700 block">Tolak Aduan</span>
                            <span class="text-[10px] text-slate-500 block">Laporan fiktif / luar area</span>
                        </div>
                    </label>

                </div>
            </div>

            <!-- Catatan Verifikasi / Instruksi Disposisi -->
            <div>
                <label for="catatan" class="font-bold text-slate-700 text-xs block mb-1">
                    Catatan Verifikasi & Arahan Petugas <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <textarea id="catatan" name="catatan" rows="3" 
                          placeholder="Tuliskan catatan verifikasi lapangan, nomor kontak personil TRC yang dikerahkan, atau alasan penolakan aduan..." 
                          class="w-full p-3 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 outline-none transition"></textarea>
                <span class="text-[11px] text-slate-400 mt-1 block">Catatan ini akan tersimpan dalam riwayat penanganan logistik dan aduan.</span>
            </div>

            <!-- Footer Tombol Aksi -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeValidationModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white font-bold text-xs transition shadow-sm shadow-orange-500/20">
                    Simpan & Perbarui Status
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL LIGHTBOX FOTO BUKTI ADUAN                          -->
<!-- ======================================================== -->
<div id="photoLightboxModal" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm hidden items-center justify-center p-4" onclick="closePhotoLightbox()">
    <div class="relative max-w-3xl w-full bg-black/60 rounded-2xl overflow-hidden shadow-2xl p-2" onclick="event.stopPropagation()">
        <button type="button" onclick="closePhotoLightbox()" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img id="lightbox-image" src="" alt="Bukti Foto" class="w-full max-h-[80vh] object-contain rounded-xl">
        <p id="lightbox-caption" class="text-white text-xs sm:text-sm text-center py-2 px-4 font-semibold"></p>
    </div>
</div>

@push('scripts')
<script>
    // 1. Logika Modal Validasi & Disposisi
    function openValidationModal(report, penanganan) {
        const modal = document.getElementById('validationModal');
        const form = document.getElementById('vmodal-form');
        
        // Atur URL form action
        form.action = `/admin/laporan/${report.id_laporan}/status`;

        // Isi data modal
        document.getElementById('vmodal-ticket').innerText = `#ADU-${String(report.id_laporan).padStart(5, '0')}`;
        document.getElementById('vmodal-title').innerText = report.judul || 'Validasi Aduan Warga';
        document.getElementById('vmodal-reporter').innerText = report.nama_pelapor || 'Warga';
        document.getElementById('vmodal-phone').innerText = report.nomor_telepon || '-';
        document.getElementById('vmodal-location').innerText = `Kec. ${report.kecamatan?.nama_kecamatan || '-'}, Desa ${report.kelurahan?.nama_kelurahan || '-'}`;
        document.getElementById('vmodal-disaster').innerText = report.bencana?.nama_bencana || 'Bencana';
        document.getElementById('vmodal-desc').innerText = report.deskripsi || 'Tidak ada deskripsi.';

        // Set radio status terpilih
        const radioMap = {
            'Pending': 'status-pending',
            'Handling': 'status-handling',
            'Selesai': 'status-selesai',
            'Ditolak': 'status-ditolak'
        };
        const currentRadioId = radioMap[report.status] || 'status-pending';
        const radioElem = document.getElementById(currentRadioId);
        if (radioElem) radioElem.checked = true;

        // Reset catatan textarea
        document.getElementById('catatan').value = '';

        // Tampilkan modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeValidationModal() {
        const modal = document.getElementById('validationModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // 2. Lightbox Foto
    function openPhotoLightbox(src, caption) {
        document.getElementById('lightbox-image').src = src;
        document.getElementById('lightbox-caption').innerText = caption || 'Bukti Foto Kejadian';
        const lb = document.getElementById('photoLightboxModal');
        lb.classList.remove('hidden');
        lb.classList.add('flex');
    }

    function closePhotoLightbox() {
        const lb = document.getElementById('photoLightboxModal');
        lb.classList.remove('flex');
        lb.classList.add('hidden');
    }

    // Tutup modal jika tombol ESC ditekan
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeValidationModal();
            closePhotoLightbox();
        }
    });
</script>
@endpush

@endsection
