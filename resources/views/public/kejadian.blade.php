@extends('layouts.public')

@section('title', 'Daftar Kejadian Bencana - AKSARA BOGOR')
@section('meta_description', 'Daftar resmi pemantauan kejadian bencana alam dan situasi kedaruratan terkini di 40 Kecamatan Kabupaten Bogor. BPBD Kabupaten Bogor.')

@section('content')
<!-- Hero Header Section -->
<section class="bg-gradient-to-b from-orange-50/90 via-orange-50/40 to-transparent dark:from-orange-950/30 dark:via-transparent dark:to-transparent pt-10 pb-12 sm:pt-14 sm:pb-16 border-b border-orange-100/70 dark:border-slate-800">
    <div class="container mx-auto px-4 sm:px-6 max-w-5xl text-center">
        
        <!-- Badge BPBD -->
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
            Daftar Kejadian Bencana Alam
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl mx-auto mb-8">
            Informasi penanganan darurat, sebaran titik lokasi, status tindakan Tim Reaksi Cepat (TRC), serta dampak kebencanaan di seluruh wilayah Kabupaten Bogor.
        </p>

        <!-- 4 Kartu Metrik Ringkas Real-time -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 max-w-4xl mx-auto text-left">
            
            <!-- Total Kejadian -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">Total Kejadian</span>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</span>
                        <span class="text-xs text-slate-400">Laporan</span>
                    </div>
                </div>
            </div>

            <!-- Sedang Ditangani -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-amber-200/80 dark:border-amber-900/50 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700 dark:text-amber-400 block">Sedang Ditangani</span>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">{{ number_format($stats['handling']) }}</span>
                        <span class="text-xs text-amber-700 dark:text-amber-400 font-semibold">Titik</span>
                    </div>
                </div>
            </div>

            <!-- Selesai / Tuntas -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-200/80 dark:border-emerald-900/50 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block">Tuntas / Selesai</span>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($stats['selesai']) }}</span>
                        <span class="text-xs text-emerald-700 dark:text-emerald-400 font-semibold">Lokasi</span>
                    </div>
                </div>
            </div>

            <!-- Total Korban Terdampak -->
            <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-rose-200/80 dark:border-rose-900/50 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-rose-700 dark:text-rose-400 block">Jiwa Terdampak</span>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400">{{ number_format($stats['total_korban']) }}</span>
                        <span class="text-xs text-rose-700 dark:text-rose-400 font-semibold">Jiwa</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Filter & Search Toolbar Section -->
<section class="container mx-auto px-4 sm:px-6 -mt-6">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 shadow-xl border border-slate-200/90 dark:border-slate-800">
        <form action="{{ route('public.kejadian') }}" method="GET" class="space-y-4">
            
            <!-- Baris 1: Search Query + Dropdown Bencana + Dropdown Kecamatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                
                <!-- Input Pencarian -->
                <div class="lg:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, kronologi kejadian, atau lokasi..." 
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#ea5921]">
                </div>

                <!-- Filter Jenis Bencana -->
                <div class="lg:col-span-4">
                    <select name="bencana_id" class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#ea5921]">
                        <option value="">Semua Jenis Bencana</option>
                        @foreach($bencanaList as $b)
                        <option value="{{ $b->id_bencana }}" {{ request('bencana_id') == $b->id_bencana ? 'selected' : '' }}>
                            {{ $b->nama_bencana }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kecamatan -->
                <div class="lg:col-span-3">
                    <select name="kecamatan_id" class="w-full py-2.5 px-3.5 text-xs sm:text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#ea5921]">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatanList as $kec)
                        <option value="{{ $kec->id_kecamatan }}" {{ request('kecamatan_id') == $kec->id_kecamatan ? 'selected' : '' }}>
                            {{ $kec->nama_kecamatan }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Baris 2: Filter Status Tabs + Urutan + Tombol Filter -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                
                <!-- Status Pills -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 mr-1 hidden sm:inline">Status:</span>
                    
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                        Semua Status
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'Handling']) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'Handling' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-100' }}">
                        <span>Sedang Ditangani</span>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'Selesai']) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'Selesai' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100' }}">
                        <span>Tuntas (Selesai)</span>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'Pending']) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'Pending' ? 'bg-slate-700 text-white shadow-sm dark:bg-slate-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                        <span>Menunggu Verifikasi</span>
                    </a>
                </div>

                <!-- Sort & Action Buttons -->
                <div class="flex items-center gap-2 ml-auto w-full sm:w-auto justify-end">
                    <select name="urutan" onchange="this.form.submit()" class="py-1.5 px-3 text-xs font-semibold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none">
                        <option value="terbaru" {{ request('urutan') == 'terbaru' ? 'selected' : '' }}>Waktu: Terbaru</option>
                        <option value="terlama" {{ request('urutan') == 'terlama' ? 'selected' : '' }}>Waktu: Terlama</option>
                        <option value="korban_terbanyak" {{ request('urutan') == 'korban_terbanyak' ? 'selected' : '' }}>Dampak: Korban Terbanyak</option>
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white text-xs font-black shadow-md transition-all active:scale-95">
                        Terapkan
                    </button>

                    @if(request()->hasAny(['q', 'bencana_id', 'kecamatan_id', 'status', 'urutan']))
                    <a href="{{ route('public.kejadian') }}" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Reset filter">
                        Reset
                    </a>
                    @endif
                </div>

            </div>
        </form>
    </div>
</section>

<!-- Incident Cards List Grid -->
<section class="container mx-auto px-4 sm:px-6 py-10">
    
    @if($reports->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        @foreach($reports as $item)
        @php
            $disasterName = $item->bencana->nama_bencana ?? 'Bencana Alam';
            $kecName = $item->kecamatan->nama_kecamatan ?? '-';
            $kelName = $item->kelurahan->nama_kelurahan ?? '-';
            $hasPhoto = !empty($item->photos_list);
            $thumbnail = $hasPhoto ? $item->photos_list[0] : null;

            // Status Styling
            $statusStyle = match($item->status) {
                'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                'Handling' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                default => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            };

            $statusText = match($item->status) {
                'Selesai' => 'Penanganan Selesai',
                'Handling' => 'Sedang Ditangani',
                default => 'Menunggu Verifikasi',
            };

            // Hitung dampak rumah rusak
            $totalRumahRusak = ($item->jumlah_rumah_rusak_berat ?? 0) + ($item->jumlah_rumah_rusak_sedang ?? 0) + ($item->jumlah_rumah_rusak_ringan ?? 0);
        @endphp

        <a href="{{ route('public.kejadian.detail', $item->id_laporan) }}" 
           class="group flex flex-col bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-2xl hover:shadow-orange-500/10 hover:border-orange-400 dark:hover:border-orange-600 transition-all duration-300 hover:-translate-y-1">
            
            <!-- Banner / Thumbnail Image Section -->
            <div class="h-48 sm:h-52 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                @if($thumbnail)
                <img src="{{ $thumbnail }}" alt="{{ $item->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/30"></div>
                @else
                <!-- Gradient Fallback Pattern dengan Icon Bencana Besar -->
                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-orange-50 via-amber-50 to-orange-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 p-6 text-center relative">
                    <img src="{{ asset($item->icon_url) }}" alt="{{ $disasterName }}" class="w-16 h-16 object-contain opacity-85 group-hover:scale-110 transition-transform duration-300 mb-1 drop-shadow-md">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Pusdalops PB Bogor</span>
                </div>
                @endif

                <!-- Badge Kategori Bencana di Kiri Atas -->
                <div class="absolute top-3.5 left-3.5 flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 dark:bg-slate-900/95 text-slate-800 dark:text-white backdrop-blur-md shadow-md text-xs font-black">
                    <img src="{{ asset($item->icon_url) }}" alt="{{ $disasterName }}" class="w-4 h-4 object-contain">
                    <span>{{ $disasterName }}</span>
                </div>

                <!-- Badge Status di Kanan Atas -->
                <div class="absolute top-3.5 right-3.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border backdrop-blur-md shadow-sm {{ $statusStyle }}">
                    @if($item->status === 'Handling')
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping mr-1"></span>
                    @endif
                    <span>{{ $statusText }}</span>
                </div>

                <!-- Info Waktu di Bawah Banner -->
                <div class="absolute bottom-2.5 left-3.5 right-3.5 flex items-center justify-between text-[11px] font-bold text-white drop-shadow-md">
                    <div class="flex items-center gap-1.5 bg-black/40 backdrop-blur-md px-2.5 py-0.5 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}</span>
                    </div>
                    @if($hasPhoto)
                    <div class="flex items-center gap-1 bg-black/40 backdrop-blur-md px-2 py-0.5 rounded-full text-[10px]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                        <span>{{ count($item->photos_list) }} Foto</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Content Body Section -->
            <div class="p-5 sm:p-6 flex flex-col flex-1">
                
                <!-- Lokasi Kecamatan & Desa -->
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 truncate">
                    <svg class="w-4 h-4 text-[#ea5921] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="truncate">Kec. {{ $kecName }}, Desa {{ $kelName }}</span>
                </div>

                <!-- Judul Kejadian -->
                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white line-clamp-2 group-hover:text-[#ea5921] transition-colors leading-snug mb-2">
                    {{ $item->judul }}
                </h3>

                <!-- Potongan Deskripsi / Kronologi -->
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed mb-4">
                    {{ Str::limit(strip_tags($item->deskripsi), 130) }}
                </p>

                <!-- Impact Badges (Korban & Kerusakan) -->
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if(($item->jumlah_korban ?? 0) > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>{{ $item->jumlah_korban }} Korban</span>
                    </span>
                    @endif

                    @if($totalRumahRusak > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>{{ $totalRumahRusak }} Rumah Rusak</span>
                    </span>
                    @endif

                    @if(!empty($item->kebutuhan_logistik))
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Bantuan Logistik</span>
                    </span>
                    @endif
                </div>

                <!-- Footer Card Action -->
                <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs font-black text-[#ea5921]">
                    <span class="flex items-center gap-1">
                        <span>Buka Detail Lengkap</span>
                    </span>
                </div>

            </div>
        </a>
        @endforeach
    </div>

    <!-- Paginasi -->
    <div class="mt-12 flex justify-center">
        {{ $reports->links() }}
    </div>

    @else
    <!-- Empty State -->
    <div class="max-w-md mx-auto text-center py-16 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-orange-100 dark:bg-orange-950/60 text-[#ea5921] flex items-center justify-center mx-auto mb-4 text-2xl">
            🚨
        </div>
        <h3 class="text-lg font-black text-slate-900 dark:text-white mb-1.5">Tidak Ditemukan Kejadian</h3>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
            Tidak ada laporan kejadian bencana aktif yang cocok dengan parameter filter pencarian Anda saat ini.
        </p>
        <a href="{{ route('public.kejadian') }}" class="px-5 py-2.5 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white text-xs font-black shadow-md transition-all inline-block">
            Lihat Semua Kejadian
        </a>
    </div>
    @endif

</section>
@endsection
