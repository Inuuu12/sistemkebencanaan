<!-- Top Navbar Kunker Style: Simpel, Bersih, Fungsional -->
<header class="sticky top-0 z-30 h-16 bg-white/90 backdrop-blur-md border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
    
    <!-- Sisi Kiri: Tombol Hamburger & Breadcrumb -->
    <div class="flex items-center gap-3">
        <!-- Tombol Hamburger Mobile -->
        <button type="button" onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Judul & Breadcrumb -->
        <div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">
                @yield('header_title', 'Dashboard Kebencanaan')
            </h1>
            <p class="text-xs text-slate-500 hidden sm:block">
                Pusat Pengendalian Operasi Penanggulangan Bencana (PUSDALOPS-PB)
            </p>
        </div>
    </div>

    <!-- Sisi Kanan: Status Siaga & Profil Singkat -->
    <div class="flex items-center gap-3">
        <!-- Badge Siaga Bencana -->
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Siaga Bencana Aktif</span>
        </div>

        <div class="h-6 w-px bg-slate-200 hidden md:block"></div>

        <!-- Tanggal Hari Ini -->
        <div class="text-right hidden sm:block">
            <span class="text-xs font-semibold text-slate-700 block">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
            <span class="text-[10px] text-slate-400">Kabupaten Bogor, Jawa Barat</span>
        </div>

        <!-- Tombol Shortcut Web Publik -->
        <a href="{{ route('public.home') }}" target="_blank" 
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-orange-300 text-slate-700 hover:text-orange-600 bg-white hover:bg-orange-50 text-xs font-semibold transition-colors shadow-2xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <span class="hidden sm:inline">Web Publik</span>
        </a>
    </div>
</header>
