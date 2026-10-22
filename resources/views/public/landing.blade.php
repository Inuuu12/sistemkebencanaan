@extends('layouts.public')

@section('title', 'Peta Pantau Kebencanaan Kabupaten Bogor - AKSARA BOGOR')
@section('meta_description', 'Peta Informasi dan Pemantauan Bencana Alam Realtime Wilayah Kabupaten Bogor. Pusdalops BPBD Kabupaten Bogor.')

@section('body_class', 'h-screen overflow-hidden flex flex-col')
@section('header_class', 'relative z-[500] shadow-sm')
@section('main_class', 'flex-1 relative overflow-hidden w-full h-full')
@section('hide_footer', true)

@section('content')
<div class="relative w-full h-full overflow-hidden select-none">
    
    <!-- map -->
    <div id="public-map" class="absolute inset-0 z-0 bg-slate-100 dark:bg-slate-900 w-full h-full"></div>

    <!-- bar side publik -->
    <div class="absolute top-4 sm:top-5 left-3 sm:left-4 z-[400] flex flex-col gap-2 pointer-events-none">
        <div id="quick-tools-bar" class="pointer-events-auto flex sm:flex-col items-center gap-2 p-1.5 sm:p-2 rounded-2xl sm:rounded-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-white/60 dark:border-slate-800/80 shadow-lg shadow-black/5 max-w-[calc(100vw-24px)] overflow-x-auto sm:overflow-visible">
            
            <!-- Berita Link -->
            <a href="{{ route('public.news') }}" title="Berita Kebencanaan"
                class="group flex items-center justify-center p-2.5 sm:p-3 rounded-xl sm:rounded-full bg-white/90 dark:bg-slate-800/90 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shrink-0">
                <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </a>

            <!-- Button kontak darurat -->
            <button type="button" onclick="toggleModal('contact-modal')" title="Kontak Darurat BPBD"
                class="group relative flex items-center justify-center p-2.5 sm:p-3 rounded-xl sm:rounded-full bg-white/90 dark:bg-slate-800/90 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shrink-0">
                <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </button>

            <!-- button filter peta -->
            <button type="button" onclick="toggleModal('filter-modal')" title="Filter Peta"
                class="group flex items-center justify-center p-2.5 sm:p-3 rounded-xl sm:rounded-full bg-white/90 dark:bg-slate-800/90 text-teal-600 hover:text-teal-700 hover:bg-teal-50 dark:hover:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shrink-0">
                <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </button>

            <!-- button pilihan peta -->
            <button type="button" onclick="toggleModal('basemap-modal')" title="Pilihan Peta"
                class="group flex items-center justify-center p-2.5 sm:p-3 rounded-xl sm:rounded-full bg-white/90 dark:bg-slate-800/90 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shrink-0">
                <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </button>

            <!-- button informasi kejadian bencana -->
            <button type="button" onclick="toggleModal('info-bencana-panel')" title="Daftar Kejadian Bencana"
                class="group relative flex items-center justify-center p-2.5 sm:p-3 rounded-xl sm:rounded-full bg-white/90 dark:bg-slate-800/90 text-orange-500 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shrink-0">
                <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                @if(count($reports) > 0)
                <span class="absolute -top-1 -right-1 px-1.5 py-0.5 rounded-full text-[9px] font-black bg-[#ea5921] text-white">
                    {{ count($reports) }}
                </span>
                @endif
            </button>

            <!-- button lokasi saya -->
            <button type="button" onclick="locateUserGPS()" title="Lokasi Saya"
                class="group flex items-center justify-center p-2.5 sm:p-3 rounded-xl sm:rounded-full bg-white/90 dark:bg-slate-800/90 text-cyan-600 hover:text-cyan-700 hover:bg-cyan-50 dark:hover:bg-slate-700 shadow-sm border border-slate-100 dark:border-slate-700 transition-all hover:scale-105 active:scale-95 shrink-0">
                <svg class="w-5 h-5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- informasi jumlah kejadian -->
    <div class="absolute top-4 right-3 sm:right-4 z-[400] pointer-events-none">
        <div class="pointer-events-auto bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border border-white/60 dark:border-slate-800/80 rounded-2xl shadow-lg px-3.5 py-2 flex items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">
                    {{ $stats['total_laporan'] }} Kejadian
                </span>
            </div>
            <div class="h-3 w-px bg-slate-300 dark:bg-slate-700"></div>
            <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                Kabupaten Bogor
            </div>
        </div>
    </div>

    <!-- bar side bawah -->
    <div class="absolute bottom-3 left-3 right-3 sm:left-4 sm:right-4 z-[400] pointer-events-none">
        <div class="pointer-events-auto bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-white/60 dark:border-slate-800/80 rounded-2xl shadow-xl overflow-hidden p-2">
            <div class="flex items-center justify-between px-2 pb-1.5 border-b border-slate-100 dark:border-slate-800 text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <span class="text-[9px] font-normal lowercase opacity-75">pilih kategori bencana</span>
            </div>

            <div class="overflow-hidden py-1 relative">
                <div class="category-rail-track flex items-center gap-2 sm:gap-3">
                    @foreach($bencanaTypes as $bt)
                    <button type="button" onclick="filterByCategory('{{ $bt['nama'] }}')"
                        class="shrink-0 flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700 hover:border-orange-400 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all shadow-sm active:scale-95 group">
                        <img src="{{ asset($bt['icon']) }}" alt="{{ $bt['nama'] }}" class="w-5 h-5 object-contain group-hover:scale-110 transition-transform">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 group-hover:text-[#ea5921] whitespace-nowrap">
                            {{ $bt['nama'] }}
                        </span>
                    </button>
                    @endforeach

                    <!-- duplikat untuk animasi -->
                    @foreach($bencanaTypes as $bt)
                    <button type="button" onclick="filterByCategory('{{ $bt['nama'] }}')"
                        class="shrink-0 flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700 hover:border-orange-400 hover:bg-orange-50 dark:hover:bg-slate-700 transition-all shadow-sm active:scale-95 group">
                        <img src="{{ asset($bt['icon']) }}" alt="{{ $bt['nama'] }}" class="w-5 h-5 object-contain group-hover:scale-110 transition-transform">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 group-hover:text-[#ea5921] whitespace-nowrap">
                            {{ $bt['nama'] }}
                        </span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- panel informasi kejadian bencana -->
    <div id="info-bencana-panel" class="hidden absolute top-20 left-3 sm:left-20 bottom-24 z-[500] w-[calc(100vw-24px)] sm:w-[380px] bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl rounded-3xl shadow-2xl border border-slate-200/90 dark:border-slate-800 flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-[#ea5921]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white leading-tight">
                        Informasi Kejadian Bencana
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Kabupaten Bogor</p>
                </div>
            </div>
            <button type="button" onclick="toggleModal('info-bencana-panel')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- List Kejadian -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse($reports as $rep)
            <div onclick="focusReportOnMap({{ $rep['id'] }})" class="group cursor-pointer p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/70 hover:border-orange-400 dark:hover:border-orange-500 transition-all hover:shadow-md">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800/60 flex items-center justify-center shrink-0">
                        <img src="{{ asset($rep['icon']) }}" alt="{{ $rep['bencana_nama'] }}" class="w-6 h-6 object-contain">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full {{ $rep['status'] === 'Selesai' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300' : ($rep['status'] === 'Handling' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300' : 'bg-red-100 text-red-700 dark:bg-red-950/80 dark:text-red-300') }}">
                                {{ $rep['status'] }}
                            </span>
                            <span class="text-[10px] text-slate-400">{{ $rep['time_ago'] }}</span>
                        </div>
                        <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate group-hover:text-[#ea5921] transition-colors">
                            {{ $rep['title'] }}
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                            Kec. {{ $rep['district'] }}, Desa {{ $rep['village'] }}
                        </p>
                        @if($rep['total_victims'] > 0)
                        <div class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/50 px-2 py-0.5 rounded-md">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>{{ $rep['total_victims'] }} Jiwa Terdampak</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-slate-400 text-xs">
                Tidak ada laporan bencana yang aktif.
            </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL 2: DETAIL LAPORAN BENCANA (BOTTOM SHEET / MODAL) -->
    <div id="detail-bencana-modal" class="hidden fixed inset-0 z-[600] bg-slate-950/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-in fade-in duration-200">
        <div class="w-full sm:max-w-xl max-h-[85vh] bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 bg-slate-50/50 dark:bg-slate-950/50">
                <div class="flex items-center gap-2.5">
                    <img id="detail-icon" src="{{ asset('icon/banjir.png') }}" class="w-8 h-8 object-contain" alt="Icon">
                    <div>
                        <span id="detail-bencana" class="text-xs font-black text-[#ea5921] uppercase tracking-wider block">Banjir</span>
                        <span id="detail-status" class="inline-block text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300">Pending</span>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Content Body -->
            <div class="p-5 overflow-y-auto space-y-4 flex-1">
                <h3 id="detail-title" class="text-lg font-black text-slate-900 dark:text-white leading-tight">
                    Judul Laporan
                </h3>

                <!-- Lokasi & Waktu Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs bg-slate-50 dark:bg-slate-800/50 p-3 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Wilayah</span>
                        <span id="detail-wilayah" class="font-extrabold text-slate-800 dark:text-slate-200">Kecamatan Cibinong</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Waktu Kejadian</span>
                        <span id="detail-time" class="font-extrabold text-slate-800 dark:text-slate-200">-</span>
                    </div>
                    <div class="sm:col-span-2 mt-1">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Alamat Detail</span>
                        <span id="detail-address" class="text-slate-700 dark:text-slate-300 font-medium">-</span>
                    </div>
                </div>

                <!-- Korban Stats Card -->
                <div id="detail-korban-container" class="bg-red-50/80 dark:bg-red-950/30 border border-red-200/80 dark:border-red-900/40 rounded-2xl p-3.5">
                    <span class="text-[10px] font-black uppercase text-red-700 dark:text-red-400 tracking-wider block mb-2">
                        Data Dampak Korban
                    </span>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-white/80 dark:bg-slate-900/60 p-2 rounded-xl">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">Meninggal</span>
                            <span id="detail-meninggal" class="text-base font-black text-red-600 dark:text-red-400">0</span>
                        </div>
                        <div class="bg-white/80 dark:bg-slate-900/60 p-2 rounded-xl">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">Luka-luka</span>
                            <span id="detail-luka" class="text-base font-black text-amber-600 dark:text-amber-400">0</span>
                        </div>
                        <div class="bg-white/80 dark:bg-slate-900/60 p-2 rounded-xl">
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block font-bold">Mengungsi</span>
                            <span id="detail-mengungsi" class="text-base font-black text-blue-600 dark:text-blue-400">0</span>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Kejadian -->
                <div>
                    <span class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider block mb-1.5">
                        Kronologi / Keterangan
                    </span>
                    <p id="detail-desc" class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/60">
                        -
                    </p>
                </div>

                <!-- Kebutuhan Logistik -->
                <div id="detail-logistics-container" class="hidden">
                    <span class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider block mb-1.5">
                        Kebutuhan Logistik Darurat
                    </span>
                    <p id="detail-logistics" class="text-xs text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 p-3 rounded-2xl border border-amber-200/60 dark:border-amber-900/40">
                        -
                    </p>
                </div>

                <!-- Foto Laporan -->
                <div id="detail-photo-container" class="hidden">
                    <span class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider block mb-1.5">
                        Dokumentasi Foto Lapangan
                    </span>
                    <img id="detail-photo" src="" alt="Dokumentasi Bencana" class="w-full max-h-60 object-cover rounded-2xl border border-slate-200 dark:border-slate-800">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0">
                <a id="detail-nav-btn" href="#" target="_blank" rel="noopener noreferrer"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white font-bold text-xs shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Navigasi Petunjuk Arah</span>
                </a>
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 3: KONTAK DARURAT HOTLINE BPBD -->
    <div id="contact-modal" class="hidden fixed inset-0 z-[600] bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in duration-200">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-red-100 dark:bg-red-950/60 text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-slate-900 dark:text-white">Kontak Darurat Kabupaten Bogor</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Siaga 24 Jam Kedaruratan & Penyelamatan</p>
                    </div>
                </div>
                <button type="button" onclick="toggleModal('contact-modal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 space-y-2.5 max-h-[70vh] overflow-y-auto">
                @foreach($emergencyContacts as $c)
                <a href="tel:{{ $c['phone'] }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 hover:border-red-400 dark:hover:border-red-500 hover:bg-red-50/50 dark:hover:bg-red-950/20 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-950/80 text-red-600 dark:text-red-400 flex items-center justify-center font-black text-sm">
                            {{ substr($c['phone'], 0, 3) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400">
                                {{ $c['name'] }}
                            </h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $c['description'] }}</p>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 rounded-xl bg-red-600 text-white font-extrabold text-xs group-hover:scale-105 transition-transform shrink-0">
                        Panggil
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL 4: FILTER PETA KEJADIAN -->
    <div id="filter-modal" class="hidden fixed inset-0 z-[600] bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in duration-200">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-slate-900 dark:text-white">Filter Data Kejadian</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih parameter untuk menyaring peta</p>
                    </div>
                </div>
                <button type="button" onclick="toggleModal('filter-modal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('public.home') }}" method="GET" class="p-5 space-y-4">
                <!-- Kecamatan -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Kecamatan (Kab. Bogor)
                    </label>
                    <select name="kecamatan_id" id="filter-kecamatan" onchange="loadFilterKelurahan(this.value)"
                        class="w-full text-xs font-medium px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatanList as $kec)
                        <option value="{{ $kec->id_kecamatan }}" {{ request('kecamatan_id') == $kec->id_kecamatan ? 'selected' : '' }}>
                            {{ $kec->nama_kecamatan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jenis Bencana -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Jenis Kejadian Bencana
                    </label>
                    <select name="bencana_id"
                        class="w-full text-xs font-medium px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                        <option value="">Semua Jenis Bencana</option>
                        @foreach($bencanaTypes as $bt)
                        <option value="{{ $bt['id'] }}" {{ request('bencana_id') == $bt['id'] ? 'selected' : '' }}>
                            {{ $bt['nama'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Penanganan -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Status Penanganan
                    </label>
                    <select name="status"
                        class="w-full text-xs font-medium px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending (Menunggu Verifikasi)</option>
                        <option value="Handling" {{ request('status') == 'Handling' ? 'selected' : '' }}>Handling (Sedang Ditangani)</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai (Penanganan Tuntas)</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2.5 pt-2">
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white text-xs font-extrabold shadow-md transition-all">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('public.home') }}" class="py-2.5 px-4 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: PILIHAN BASEMAP LAYER PETA -->
    <div id="basemap-modal" class="hidden fixed inset-0 z-[600] bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in duration-200">
        <div class="w-full max-w-sm bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-slate-900 dark:text-white">Pilihan Tampilan Peta</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih jenis layer basemap</p>
                    </div>
                </div>
                <button type="button" onclick="toggleModal('basemap-modal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="p-4 space-y-2">
                <button type="button" onclick="changeBasemap('osm')" class="w-full flex items-center justify-between p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-slate-800 transition-all text-left">
                    <div>
                        <h4 class="font-extrabold text-xs text-slate-800 dark:text-white">OpenStreetMap (Standar)</h4>
                        <p class="text-[10px] text-slate-400">Peta jalan & fitur perkotaan jelas</p>
                    </div>
                    <span class="w-4 h-4 rounded-full border border-emerald-500 flex items-center justify-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                </button>

                <button type="button" onclick="changeBasemap('satellite')" class="w-full flex items-center justify-between p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-slate-800 transition-all text-left">
                    <div>
                        <h4 class="font-extrabold text-xs text-slate-800 dark:text-white">Satelit Esri / Google</h4>
                        <p class="text-[10px] text-slate-400">Citra foto udara topografi asli</p>
                    </div>
                    <span class="w-4 h-4 rounded-full border border-slate-300"></span>
                </button>

                <button type="button" onclick="changeBasemap('topo')" class="w-full flex items-center justify-between p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-slate-800 transition-all text-left">
                    <div>
                        <h4 class="font-extrabold text-xs text-slate-800 dark:text-white">Topografi & Kontur</h4>
                        <p class="text-[10px] text-slate-400">Ketinggian tanah, pegunungan, lereng</p>
                    </div>
                    <span class="w-4 h-4 rounded-full border border-slate-300"></span>
                </button>

                <button type="button" onclick="changeBasemap('dark')" class="w-full flex items-center justify-between p-3 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-slate-800 transition-all text-left">
                    <div>
                        <h4 class="font-extrabold text-xs text-slate-800 dark:text-white">CartoDB Dark Matter</h4>
                        <p class="text-[10px] text-slate-400">Sangat nyaman untuk mode malam</p>
                    </div>
                    <span class="w-4 h-4 rounded-full border border-slate-300"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 6: PETUNJUK TOUR PENGGUNAAN PETA -->
    <div id="tour-modal" class="hidden fixed inset-0 z-[700] bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in duration-200">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
            <div class="px-5 pt-4 pb-2 flex items-center justify-between">
                <span id="tour-badge" class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300">
                    Langkah 1 dari 4
                </span>
                <button type="button" onclick="toggleModal('tour-modal')" class="text-xs font-bold text-slate-400 hover:text-slate-600">
                    Lewati
                </button>
            </div>

            <div class="p-5 text-center flex flex-col items-center">
                <div id="tour-icon-box" class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-[#ea5921] flex items-center justify-center text-3xl mb-3 shadow-inner">
                    🗺️
                </div>
                <h3 id="tour-title" class="text-base font-black text-slate-900 dark:text-white mb-1">
                    Peta Interaktif Bencana
                </h3>
                <p id="tour-subtitle" class="text-xs font-bold text-[#ea5921] mb-2">
                    Pemantauan Kejadian Real-time
                </p>
                <p id="tour-desc" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-800/60 p-3 rounded-2xl text-left border border-slate-100 dark:border-slate-800">
                    Peta menampilkan titik-titik kejadian bencana seperti banjir, longsor, dan pohon tumbang di seluruh 40 kecamatan Kabupaten Bogor. Klik pin bencana untuk melihat detail.
                </p>
            </div>

            <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <button type="button" id="tour-prev-btn" onclick="prevTourStep()" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
                    ← Kembali
                </button>
                <button type="button" id="tour-next-btn" onclick="nextTourStep()" class="px-4 py-2 rounded-xl bg-[#ea5921] text-white text-xs font-bold shadow-md hover:bg-[#d94812]">
                    Lanjut →
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // DATA DARI SERVER LARAVEL BLADE (PHP Murni)
    const rawReports = @json($reports);
    const bencanaTypes = @json($bencanaTypes);
    const kecamatanList = @json($kecamatanList);

    // BOGOR COORDINATES
    const BOGOR_MAP_CENTER = [-6.48, 106.83];
    const BOGOR_MAP_ZOOM = 10;

    let map = null;
    let markersLayer = null;
    let boundariesLayer = null;
    let activeTileLayer = null;
    let userMarker = null;
    let userCircle = null;

    // TILE PROVIDERS
    const TILES = {
        osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }),
        satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 18,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        }),
        topo: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            maxZoom: 17,
            attribution: 'Map data: &copy; OpenStreetMap contributors, SRTM | Map style: &copy; OpenTopoMap (CC-BY-SA)'
        }),
        dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; CARTO'
        })
    };

    // INISIALISASI PETA LEAFLET
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        loadBoundaries();
        renderMarkers(rawReports);
    });

    function initMap() {
        // Cek mode gelap untuk basemap awal
        const isDark = document.documentElement.classList.contains('dark');
        activeTileLayer = isDark ? TILES.dark : TILES.osm;

        map = L.map('public-map', {
            center: BOGOR_MAP_CENTER,
            zoom: BOGOR_MAP_ZOOM,
            zoomControl: false,
            layers: [activeTileLayer]
        });

        // Letakkan zoom control di kanan atas
        L.control.zoom({ position: 'topright' }).addTo(map);

        markersLayer = L.layerGroup().addTo(map);
        boundariesLayer = L.layerGroup().addTo(map);

        // Listener tema otomatis mengubah basemap
        window.addEventListener('theme-changed', function() {
            const isDarkNow = document.documentElement.classList.contains('dark');
            changeBasemap(isDarkNow ? 'dark' : 'osm');
        });

        setTimeout(function() {
            if (map) map.invalidateSize();
        }, 200);
    }

    // GANTI BASEMAP TILE LAYER
    function changeBasemap(type) {
        if (!map || !TILES[type]) return;
        map.removeLayer(activeTileLayer);
        activeTileLayer = TILES[type];
        map.addLayer(activeTileLayer);
        toggleModal('basemap-modal');
    }

    // LOAD GEOJSON ADMINISTRATIVE BOUNDARIES KAB BOGOR
    function loadBoundaries() {
        fetch('{{ route("public.api.boundaries", ["level" => "kecamatan"]) }}')
            .then(res => res.json())
            .then(geojsonData => {
                if (!geojsonData || !geojsonData.features) return;

                L.geoJSON(geojsonData, {
                    style: function(feature) {
                        return {
                            color: '#ea5921',
                            weight: 1.5,
                            opacity: 0.65,
                            fillColor: '#ea5921',
                            fillOpacity: 0.04,
                            dashArray: '3, 3'
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        const name = feature.properties?.name || feature.properties?.NAMOBJ || feature.properties?.KECAMATAN || '';
                        if (name) {
                            layer.bindTooltip(`Kec. ${name}`, {
                                permanent: false,
                                direction: 'center',
                                className: 'sigab-boundary-label'
                            });
                        }

                        layer.on('mouseover', function(e) {
                            const l = e.target;
                            l.setStyle({
                                weight: 2.5,
                                fillOpacity: 0.15,
                                color: '#d94812'
                            });
                        });
                        layer.on('mouseout', function(e) {
                            const l = e.target;
                            l.setStyle({
                                weight: 1.5,
                                fillOpacity: 0.04,
                                color: '#ea5921'
                            });
                        });
                    }
                }).addTo(boundariesLayer);
            })
            .catch(err => console.log('Boundary load note:', err));
    }

    // RENDER DISASTER MARKERS PADA PETA
    function renderMarkers(reports) {
        if (!markersLayer) return;
        markersLayer.clearLayers();

        reports.forEach(report => {
            if (!report.lat || !report.lng) return;

            // Buat Custom Icon Badge
            const statusColor = report.status === 'Selesai' ? '#10b981' : (report.status === 'Handling' ? '#f59e0b' : '#ef4444');
            const customIconHtml = `
                <div class="relative cursor-pointer flex items-center justify-center transform hover:scale-110 transition-transform" style="width: 44px; height: 44px;">
                    <div class="relative w-10 h-10 rounded-full bg-white dark:bg-slate-900 border-2 shadow-xl flex items-center justify-center p-1.5" style="border-color: ${statusColor};">
                        <img src="${report.icon}" alt="${report.bencana_nama}" class="w-full h-full object-contain">
                    </div>
                </div>
            `;

            const icon = L.divIcon({
                html: customIconHtml,
                className: 'custom-disaster-marker',
                iconSize: [44, 44],
                iconAnchor: [22, 22],
                popupAnchor: [0, -24]
            });

            const marker = L.marker([report.lat, report.lng], { icon: icon });

            // Popup Konten
            const popupContent = `
                <div class="p-2 min-w-[220px] text-slate-800 dark:text-slate-100 font-sans">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <img src="${report.icon}" class="w-5 h-5 object-contain" alt="Icon">
                        <span class="text-[11px] font-black uppercase text-[#ea5921]">${report.bencana_nama}</span>
                        <span class="ml-auto text-[9px] font-black px-1.5 py-0.5 rounded-full" style="background-color: ${statusColor}20; color: ${statusColor};">${report.status}</span>
                    </div>
                    <h4 class="font-extrabold text-xs leading-tight mb-1">${report.title}</h4>
                    <p class="text-[10px] text-slate-500 mb-2 truncate">Kec. ${report.district}, Desa ${report.village}</p>
                    <button type="button" onclick="showReportDetail(${report.id})" class="w-full py-1.5 px-3 rounded-lg bg-[#ea5921] hover:bg-[#d94812] text-white text-[11px] font-bold text-center block shadow-sm">
                        Lihat Detail Kejadian
                    </button>
                </div>
            `;

            marker.bindPopup(popupContent, {
                className: 'custom-leaflet-popup',
                maxWidth: 280
            });

            marker.reportId = report.id;
            markersLayer.addLayer(marker);
        });
    }

    // FOCUS DAN BUKA DETAIL DARI PANEL INFORMASI
    function focusReportOnMap(id) {
        const report = rawReports.find(r => r.id === id);
        if (!report || !map) return;

        map.setView([report.lat, report.lng], 14, { animate: true });
        toggleModal('info-bencana-panel'); // tutup drawer list
        showReportDetail(id); // buka modal detail
    }

    // FILTER BERDASARKAN KATEGORI (KLIK DI BOTTOM RAIL)
    function filterByCategory(categoryName) {
        const filtered = rawReports.filter(r => r.bencana_nama.toLowerCase().includes(categoryName.toLowerCase()));
        if (filtered.length > 0) {
            renderMarkers(filtered);
            const bounds = L.latLngBounds(filtered.map(r => [r.lat, r.lng]));
            map.fitBounds(bounds, { padding: [60, 60], maxZoom: 13 });
        } else {
            renderMarkers(rawReports);
            alert('Tidak ditemukan laporan aktif untuk kategori ' + categoryName);
        }
    }

    // MODAL HANDLERS
    function toggleModal(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            el.classList.toggle('hidden');
        }
    }

    function showReportDetail(id) {
        const rep = rawReports.find(r => r.id === id);
        if (!rep) return;

        document.getElementById('detail-icon').src = rep.icon;
        document.getElementById('detail-bencana').innerText = rep.bencana_nama;
        document.getElementById('detail-status').innerText = rep.status;
        document.getElementById('detail-title').innerText = rep.title;
        document.getElementById('detail-wilayah').innerText = `Kecamatan ${rep.district}, Desa ${rep.village}`;
        document.getElementById('detail-time').innerText = rep.created_at_formatted;
        document.getElementById('detail-address').innerText = rep.address || 'Alamat tidak dispesifikasikan';
        document.getElementById('detail-desc').innerText = rep.description;

        // Korban stats
        document.getElementById('detail-meninggal').innerText = rep.meninggal;
        document.getElementById('detail-luka').innerText = rep.luka;
        document.getElementById('detail-mengungsi').innerText = rep.mengungsi;

        // Logistik
        const logContainer = document.getElementById('detail-logistics-container');
        if (rep.logistics) {
            document.getElementById('detail-logistics').innerText = rep.logistics;
            logContainer.classList.remove('hidden');
        } else {
            logContainer.classList.add('hidden');
        }

        // Foto Laporan
        const photoContainer = document.getElementById('detail-photo-container');
        if (rep.photos && rep.photos.length > 0 && rep.photos[0]) {
            document.getElementById('detail-photo').src = rep.photos[0];
            photoContainer.classList.remove('hidden');
        } else {
            photoContainer.classList.add('hidden');
        }

        // Navigasi Google Maps
        document.getElementById('detail-nav-btn').href = `https://www.google.com/maps/dir/?api=1&destination=${rep.lat},${rep.lng}`;

        document.getElementById('detail-bencana-modal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detail-bencana-modal').classList.add('hidden');
    }

    // GEOLOCATION USER GPS
    function locateUserGPS() {
        if (!navigator.geolocation) {
            alert('Perangkat Anda tidak mendukung fitur lokasi GPS.');
            return;
        }

        navigator.geolocation.getCurrentPosition(function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            if (userMarker) map.removeLayer(userMarker);
            if (userCircle) map.removeLayer(userCircle);

            userMarker = L.circleMarker([lat, lng], {
                radius: 8,
                fillColor: '#06b6d4',
                color: '#ffffff',
                weight: 3,
                opacity: 1,
                fillOpacity: 1
            }).addTo(map);

            userCircle = L.circle([lat, lng], {
                radius: pos.coords.accuracy || 150,
                color: '#06b6d4',
                weight: 1,
                fillOpacity: 0.15
            }).addTo(map);

            userMarker.bindTooltip('Lokasi Anda Saat Ini', { permanent: true, direction: 'top' });
            map.setView([lat, lng], 14, { animate: true });
        }, function(err) {
            alert('Gagal mendeteksi lokasi GPS. Pastikan izin lokasi diizinkan di browser Anda.');
        }, { enableHighAccuracy: true });
    }

    // TOUR PANDUAN PETA
    const tourSteps = [
        {
            badge: 'Langkah 1 dari 4',
            icon: '🗺️',
            title: 'Peta Interaktif Bencana',
            subtitle: 'Pemantauan Kejadian Real-time',
            desc: 'Peta menampilkan titik-titik kejadian bencana seperti banjir, longsor, kebakaran, dan angin kencang di seluruh 40 kecamatan Kabupaten Bogor. Klik pin bencana untuk melihat rincian.'
        },
        {
            badge: 'Langkah 2 dari 4',
            icon: '🛠️',
            title: 'Alat Cepat (Quick Tools)',
            subtitle: 'Tombol Pintas di Kiri Layar',
            desc: 'Gunakan panel tombol mengambang di sisi kiri untuk membuka Berita terkini, Kontak Darurat BPBD, Filter Wilayah, Pilihan Layer Peta, dan Daftar Informasi Kejadian.'
        },
        {
            badge: 'Langkah 3 dari 4',
            icon: '⚡',
            title: 'Kategori Kejadian Bencana',
            subtitle: 'Marquee Rail Bawah',
            desc: 'Kategori di bagian bawah dapat diklik langsung untuk memfilter peta secara instan sesuai jenis bencana yang Anda inginkan.'
        },
        {
            badge: 'Langkah 4 dari 4',
            icon: '📢',
            title: 'Lapor Kejadian Bencana',
            subtitle: 'Partisipasi Warga Masyarakat',
            desc: 'Menemukan bencana di sekitar Anda? Klik tombol "Lapor Bencana" di kanan atas navbar untuk mengirimkan aduan beserta foto dan koordinat GPS langsung ke Pusdalops BPBD!'
        }
    ];

    let currentTourStep = 0;

    function openTourModal() {
        currentTourStep = 0;
        updateTourContent();
        document.getElementById('tour-modal').classList.remove('hidden');
    }

    function updateTourContent() {
        const step = tourSteps[currentTourStep];
        document.getElementById('tour-badge').innerText = step.badge;
        document.getElementById('tour-icon-box').innerText = step.icon;
        document.getElementById('tour-title').innerText = step.title;
        document.getElementById('tour-subtitle').innerText = step.subtitle;
        document.getElementById('tour-desc').innerText = step.desc;

        document.getElementById('tour-prev-btn').style.visibility = currentTourStep === 0 ? 'hidden' : 'visible';
        document.getElementById('tour-next-btn').innerText = currentTourStep === tourSteps.length - 1 ? 'Selesai ✓' : 'Lanjut →';
    }

    function nextTourStep() {
        if (currentTourStep < tourSteps.length - 1) {
            currentTourStep++;
            updateTourContent();
        } else {
            toggleModal('tour-modal');
        }
    }

    function prevTourStep() {
        if (currentTourStep > 0) {
            currentTourStep--;
            updateTourContent();
        }
    }
</script>
@endpush
