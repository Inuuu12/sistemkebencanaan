@extends('layouts.public')

@php
    $disasterName = $report->bencana->nama_bencana ?? 'Bencana Alam';
    $kecName = $report->kecamatan->nama_kecamatan ?? '-';
    $kelName = $report->kelurahan->nama_kelurahan ?? '-';
    
    // Status Text & Style
    $statusText = match($report->status) {
        'Selesai' => 'Penanganan Selesai',
        'Handling' => 'Sedang Ditangani',
        default => 'Menunggu Verifikasi',
    };

    $statusBadge = match($report->status) {
        'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
        'Handling' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
        default => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
    };

    $korbanDetail = $report->korban->first();
    $meninggal = $report->jumlah_korban_jiwa ?? ($korbanDetail->jumlah_meninggal ?? 0);
    $lukaBerat = $report->jumlah_korban_luka_berat ?? ($korbanDetail->jumlah_luka_berat ?? 0);
    $lukaRingan = ($report->jumlah_korban_luka_ringan ?? 0) + ($report->jumlah_korban_luka_sedang ?? 0) ?: ($korbanDetail->jumlah_luka_ringan ?? 0);
    $mengungsi = $korbanDetail->jumlah_mengungsi ?? 0;
    $hilang = $korbanDetail->jumlah_hilang ?? 0;

    $rumahRB = $report->jumlah_rumah_rusak_berat ?? 0;
    $rumahRS = $report->jumlah_rumah_rusak_sedang ?? 0;
    $rumahRR = $report->jumlah_rumah_rusak_ringan ?? 0;
    $totalRumah = $rumahRB + $rumahRS + $rumahRR;
@endphp

@section('title', $report->judul . ' - AKSARA BOGOR')
@section('meta_description', Str::limit(strip_tags($report->deskripsi), 150))

@section('content')
<div class="container mx-auto px-4 sm:px-6 py-6 sm:py-10">
    
    <!-- Top Navigation & Action Buttons -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 sm:mb-8">
        <a href="{{ route('public.kejadian') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-[#ea5921] dark:hover:text-orange-400 transition-colors group">
            <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 transition-colors">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </div>
            <span>Kembali ke Daftar Kejadian</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('public.home') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors">
                <svg class="w-4 h-4 text-[#ea5921]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <span>Lihat di Peta Pantau</span>
            </a>
            
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $report->latitude }},{{ $report->longitude }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white text-xs font-black shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Rute Google Maps</span>
            </a>
        </div>
    </div>

    <!-- Layout Grid: Main Report + Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10">
        
        <!-- KOLOM UTAMA: DETAIL KEJADIAN (8 Kolom) -->
        <article class="lg:col-span-8 flex flex-col space-y-6 sm:space-y-8">
            
            <!-- Card 1: Identitas & Kronologi Utama -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-9 border border-slate-200/90 dark:border-slate-800 shadow-sm">
                
                <!-- Kategori & Status Badges -->
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-100 dark:bg-orange-950/80 text-[#ea5921] dark:text-orange-400 text-xs font-black uppercase tracking-wider border border-orange-200 dark:border-orange-800/60">
                            <img src="{{ asset($iconUrl) }}" alt="{{ $disasterName }}" class="w-4 h-4 object-contain">
                            <span>{{ $disasterName }}</span>
                        </div>

                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider border {{ $statusBadge }}">
                            @if($report->status === 'Handling')
                            <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-ping mr-1"></span>
                            @endif
                            <span>{{ $statusText }}</span>
                        </span>
                    </div>

                    <!-- Waktu Lapor -->
                    <span class="text-xs font-bold text-slate-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $report->created_at ? $report->created_at->translatedFormat('l, d F Y - H:i') . ' WIB' : '-' }}</span>
                    </span>
                </div>

                <!-- Judul Utama -->
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white leading-tight tracking-tight mb-5">
                    {{ $report->judul }}
                </h1>

                <!-- Lokasi & Pelapor Pill Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-800 mb-6 text-xs font-semibold">
                    <div class="flex items-start gap-2.5">
                        <div class="p-2 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-[#ea5921] shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wider">Wilayah Kejadian</span>
                            <span class="text-slate-800 dark:text-slate-100 font-bold">Kec. {{ $kecName }}, Desa/Kel. {{ $kelName }}</span>
                            @if($report->rt || $report->rw)
                            <span class="text-slate-500 block text-[11px]">RT {{ $report->rt ?? '-' }} / RW {{ $report->rw ?? '-' }}</span>
                            @endif
                            @if($report->alamat_detail)
                            <p class="text-slate-500 dark:text-slate-400 text-[11px] mt-1">{{ $report->alamat_detail }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 sm:border-l sm:border-slate-200 sm:dark:border-slate-700 sm:pl-3">
                        <div class="p-2 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-extrabold text-slate-400 block tracking-wider">Sumber Informasi</span>
                            <span class="text-slate-800 dark:text-slate-100 font-bold">{{ $report->nama_pelapor ?: 'Petugas / Warga Terverifikasi' }}</span>
                            <span class="text-slate-400 block text-[11px]">Laporan Masuk Sistem Pusdalops PB</span>
                        </div>
                    </div>
                </div>

                <!-- Foto-foto Dokumentasi Lapangan -->
                @if(count($photosList) > 0)
                <div class="mb-7">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#ea5921]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Dokumentasi Lapangan ({{ count($photosList) }} Foto)</span>
                    </h3>

                    @if(count($photosList) === 1)
                    <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 max-h-[460px] bg-slate-100 dark:bg-slate-800">
                        <img src="{{ $photosList[0] }}" alt="{{ $report->judul }}" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($photosList as $idx => $p)
                        <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 h-52 sm:h-60 bg-slate-100 dark:bg-slate-800">
                            <img src="{{ $p }}" alt="Foto Dokumentasi {{ $idx+1 }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

                <!-- Kronologi / Deskripsi Kejadian -->
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#ea5921]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Kronologi &amp; Rincian Kejadian</span>
                    </h3>
                    <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-200 text-sm sm:text-base leading-relaxed space-y-3 bg-slate-50/50 dark:bg-slate-800/30 p-4 sm:p-5 rounded-2xl border border-slate-100 dark:border-slate-800">
                        @php
                            $paragraphs = preg_split('/\r\n|\r|\n/', $report->deskripsi);
                        @endphp
                        @foreach($paragraphs as $para)
                            @if(trim($para) !== '')
                            <p class="text-justify leading-relaxed">
                                {{ $para }}
                            </p>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Card 2: Peta Lokasi Kejadian Interaktif -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-slate-900 dark:text-white">Peta Titik Koordinat Lokasi</h3>
                            <p class="text-xs text-slate-400">Lat: {{ $report->latitude }}, Lng: {{ $report->longitude }}</p>
                        </div>
                    </div>
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 font-extrabold text-xs transition-colors">
                        Buka Rute Navigasi →
                    </a>
                </div>

                <div id="incident-detail-map" class="w-full h-72 sm:h-80 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-inner"></div>
            </div>

            <!-- Card 3: Rincian Dampak Korban & Kerusakan Fisik -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
                
                <!-- Dampak Korban Jiwa -->
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-rose-700 dark:text-rose-400 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Dampak Korban Jiwa &amp; Warga Terdampak</span>
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center">
                        <!-- Meninggal -->
                        <div class="p-3.5 rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-900/60">
                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 dark:text-rose-400 block">Meninggal</span>
                            <span class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-0.5 block">{{ number_format($meninggal) }}</span>
                            <span class="text-[10px] text-rose-800 dark:text-rose-300 font-bold">Jiwa</span>
                        </div>

                        <!-- Luka Berat -->
                        <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/60">
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400 block">Luka Berat</span>
                            <span class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5 block">{{ number_format($lukaBerat) }}</span>
                            <span class="text-[10px] text-amber-800 dark:text-amber-300 font-bold">Jiwa</span>
                        </div>

                        <!-- Luka Ringan -->
                        <div class="p-3.5 rounded-2xl bg-yellow-50/70 dark:bg-yellow-950/30 border border-yellow-200/80 dark:border-yellow-900/60">
                            <span class="text-[10px] font-black uppercase tracking-wider text-yellow-800 dark:text-yellow-400 block">Luka Ringan</span>
                            <span class="text-2xl font-black text-yellow-700 dark:text-yellow-400 mt-0.5 block">{{ number_format($lukaRingan) }}</span>
                            <span class="text-[10px] text-yellow-800 dark:text-yellow-300 font-bold">Jiwa</span>
                        </div>

                        <!-- Mengungsi -->
                        <div class="p-3.5 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/60">
                            <span class="text-[10px] font-black uppercase tracking-wider text-blue-700 dark:text-blue-400 block">Mengungsi</span>
                            <span class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-0.5 block">{{ number_format($mengungsi) }}</span>
                            <span class="text-[10px] text-blue-800 dark:text-blue-300 font-bold">Jiwa</span>
                        </div>

                        <!-- Hilang -->
                        <div class="p-3.5 rounded-2xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/80 dark:border-purple-900/60 col-span-2 sm:col-span-1">
                            <span class="text-[10px] font-black uppercase tracking-wider text-purple-700 dark:text-purple-400 block">Hilang / SAR</span>
                            <span class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-0.5 block">{{ number_format($hilang) }}</span>
                            <span class="text-[10px] text-purple-800 dark:text-purple-300 font-bold">Jiwa</span>
                        </div>
                    </div>
                </div>

                <!-- Dampak Kerusakan Bangunan / Rumah -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Kerusakan Bangunan &amp; Tempat Tinggal</span>
                    </h3>

                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-3.5 rounded-2xl bg-rose-50/70 dark:bg-rose-950/20 border border-rose-200/70 dark:border-rose-900/50">
                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 dark:text-rose-400 block">Rusak Berat</span>
                            <span class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 mt-0.5 block">{{ number_format($rumahRB) }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold">Unit</span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-900/50">
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400 block">Rusak Sedang</span>
                            <span class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5 block">{{ number_format($rumahRS) }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold">Unit</span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-yellow-50/70 dark:bg-yellow-950/20 border border-yellow-200/70 dark:border-yellow-900/50">
                            <span class="text-[10px] font-black uppercase tracking-wider text-yellow-800 dark:text-yellow-400 block">Rusak Ringan</span>
                            <span class="text-xl sm:text-2xl font-black text-yellow-700 dark:text-yellow-400 mt-0.5 block">{{ number_format($rumahRR) }}</span>
                            <span class="text-[10px] text-slate-500 font-semibold">Unit</span>
                        </div>
                    </div>
                </div>

                <!-- Kebutuhan Logistik & Bantuan Mendesak -->
                @if(!empty($report->kebutuhan_logistik))
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-black uppercase tracking-wider text-blue-700 dark:text-blue-400 mb-2.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Kebutuhan Mendesak Lapangan / Logistik</span>
                    </h3>
                    <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-900/60 text-xs sm:text-sm font-semibold text-blue-900 dark:text-blue-200 leading-relaxed">
                        {{ $report->kebutuhan_logistik }}
                    </div>
                </div>
                @endif

            </div>

        </article>

        <!-- KOLOM SIDEBAR KANAN (4 Kolom) -->
        <aside class="lg:col-span-4 space-y-6 sm:space-y-8">
            
            <!-- Widget Kontak Darurat Cepat BPBD -->
            <div class="bg-gradient-to-br from-red-600 via-rose-600 to-orange-600 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                
                <span class="text-[10px] font-black uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full inline-block mb-3 backdrop-blur-md">
                    Hotline Siaga 24 Jam
                </span>

                <h3 class="text-xl font-black mb-1.5 leading-snug">
                    Hubungi Kedaruratan BPBD Kab. Bogor
                </h3>
                <p class="text-xs text-white/90 leading-relaxed mb-5">
                    Hubungi call center resmi apabila terdapat potensi bahaya susulan atau membutuhkan evakuasi cepat.
                </p>

                <div class="space-y-2.5">
                    <a href="tel:112" class="flex items-center justify-between p-3 rounded-2xl bg-white text-slate-900 hover:bg-red-50 transition-all font-black shadow-md group">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center font-black text-sm">
                                112
                            </div>
                            <span class="text-xs">Call Center Bebas Pulsa</span>
                        </div>
                        <span class="text-xs text-red-600 group-hover:translate-x-1 transition-transform">Hubungi →</span>
                    </a>

                    <a href="tel:0218751070" class="flex items-center justify-between p-3 rounded-2xl bg-white/15 hover:bg-white/25 text-white transition-all font-bold text-xs backdrop-blur-md border border-white/20">
                        <span>Pusdalops: (021) 875-1070</span>
                        <span>Telepon →</span>
                    </a>
                </div>
            </div>

            <!-- Widget Kejadian Terkait Lainnya -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-sm">
                <h3 class="font-black text-base text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#ea5921]"></span>
                    <span>Kejadian Terkait Lainnya</span>
                </h3>

                <div class="space-y-4">
                    @forelse($relatedReports as $rel)
                    <a href="{{ route('public.kejadian.detail', $rel->id_laporan) }}" class="flex items-start gap-3 group">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-slate-800 border border-orange-200/60 dark:border-slate-700 flex items-center justify-center shrink-0 p-2 group-hover:scale-105 transition-transform">
                            <img src="{{ asset($rel->icon_url) }}" alt="{{ $rel->bencana->nama_bencana ?? 'Bencana' }}" class="w-full h-full object-contain">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] font-bold text-slate-400 block mb-0.5">
                                Kec. {{ $rel->kecamatan->nama_kecamatan ?? '-' }} • {{ $rel->created_at ? $rel->created_at->diffForHumans() : '' }}
                            </span>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-2 group-hover:text-[#ea5921] transition-colors leading-snug">
                                {{ $rel->judul }}
                            </h4>
                        </div>
                    </a>
                    @empty
                    <p class="text-xs text-slate-400 py-2">Tidak ada kejadian terkait lainnya saat ini.</p>
                    @endforelse
                </div>

                <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 text-center">
                    <a href="{{ route('public.kejadian') }}" class="text-xs font-black text-[#ea5921] hover:underline">
                        Lihat Seluruh Daftar Kejadian →
                    </a>
                </div>
            </div>

            <!-- Banner Lapor Kejadian Warga -->
            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/80 text-center">
                <div class="w-12 h-12 rounded-2xl bg-[#ea5921]/10 text-[#ea5921] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h4 class="font-black text-sm text-slate-900 dark:text-white mb-1">Menemukan Bencana di Wilayah Anda?</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                    Kirimkan laporan aduan realtime lengkap dengan foto dan koordinat GPS agar tim BPBD segera menindaklanjuti.
                </p>
                <a href="{{ route('public.aduan') }}" class="inline-block w-full py-2.5 px-4 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white text-xs font-black shadow-md transition-all">
                    Buat Laporan Baru
                </a>
            </div>

        </aside>

    </div>
</div>

<!-- Leaflet Mini Map Initialization Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ (float) $report->latitude }};
        const lng = {{ (float) $report->longitude }};

        if (document.getElementById('incident-detail-map') && typeof L !== 'undefined') {
            const map = L.map('incident-detail-map', {
                zoomControl: true,
                scrollWheelZoom: false,
            }).setView([lat, lng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Custom Marker Icon
            const customIcon = L.icon({
                iconUrl: '{{ asset($iconUrl) }}',
                iconSize: [40, 40],
                iconAnchor: [20, 20],
                popupAnchor: [0, -20]
            });

            const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
            marker.bindPopup(`
                <div style="font-family: inherit; font-size: 12px; line-height: 1.4;">
                    <strong style="color: #ea5921; font-weight: 800;">{{ $disasterName }}</strong><br>
                    <span style="font-weight: 700;">{{ addslashes($report->judul) }}</span><br>
                    <span style="color: #64748b; font-size: 11px;">Kec. {{ $kecName }}, Desa {{ $kelName }}</span>
                </div>
            `).openPopup();
        }
    });
</script>
@endsection
