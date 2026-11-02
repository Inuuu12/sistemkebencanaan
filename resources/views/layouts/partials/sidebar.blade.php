<!-- Sidebar Kunker Style: Bersih, Minimalis, dengan Submenu Beranimasi -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 shadow-sm select-none">
    
    <!-- Bagian Atas: Brand & Navigasi -->
    <div class="overflow-y-auto">
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-orange-50 border border-orange-200/80 p-1 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/aksara-logo.png') }}" alt="Logo AKSARA" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="font-extrabold text-sm text-slate-900 tracking-tight block leading-none">AKSARA BOGOR</span>
                </div>
            </a>

            <!-- Tombol Tutup di Mobile -->
            <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Menu Navigasi -->
        <nav class="p-4 space-y-1.5">
            <div class="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                Menu Utama
            </div>

            <!-- Item: Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') && !request()->has('status') ? 'bg-[#ea5921] text-white shadow-sm shadow-orange-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            @php
                $pendingCount = \App\Models\LaporanBencana::where('status', 'Pending')->count();
                $isLaporanActive = request()->routeIs('admin.laporan.*') || request()->routeIs('admin.korban.*') || request()->routeIs('admin.pengungsian.*') || request()->routeIs('admin.kerusakan.*') || request()->has('status');
            @endphp

            <!-- Menu dengan Submenu 1: Laporan Bencana -->
            <div>
                <button type="button" onclick="toggleSubmenu('submenu-laporan', 'arrow-laporan')" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ $isLaporanActive ? 'bg-orange-50/60 text-[#ea5921] font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 {{ $isLaporanActive ? 'text-[#ea5921]' : 'text-slate-500 group-hover:text-slate-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Laporan Darurat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($pendingCount > 0)
                        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800">
                            {{ $pendingCount }}
                        </span>
                        @endif
                        <svg id="arrow-laporan" class="w-4 h-4 text-slate-400 transition-transform duration-300 ease-in-out {{ $isLaporanActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                <!-- Submenu Animasi -->
                <div id="submenu-laporan" class="submenu-collapse overflow-hidden transition-all duration-300 ease-in-out" 
                     style="{{ $isLaporanActive ? '' : 'max-height: 0px;' }}">
                    <ul class="pl-8 pr-2 py-1 space-y-1 border-l-2 border-slate-100 ml-4 my-1">
                        <li>
                            <a href="{{ route('admin.laporan.validasi') }}" 
                                class="flex items-center justify-between py-1.5 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('admin.laporan.validasi') ? 'text-[#ea5921] bg-orange-50 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                                <span>Validasi Aduan Warga</span>
                                @if($pendingCount > 0)
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.korban.index') }}" 
                                class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('admin.korban.*') ? 'text-[#ea5921] bg-orange-50 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                                Data Korban
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pengungsian.index') }}" 
                                class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('admin.pengungsian.*') ? 'text-[#ea5921] bg-orange-50 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                                Data Pengungsian
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.kerusakan.index') }}" 
                                class="block py-1.5 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('admin.kerusakan.*') ? 'text-[#ea5921] bg-orange-50 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition-colors">
                                Data Kerusakan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Menu dengan Submenu 2: Logistik dan Bantuan -->
            <div>
                <button type="button" onclick="toggleSubmenu('submenu-logistik', 'arrow-logistik')" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all text-slate-600 hover:text-slate-900 hover:bg-slate-50 group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Logistik & Bantuan</span>
                    </div>
                    <svg id="arrow-logistik" class="w-4 h-4 text-slate-400 transition-transform duration-300 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Submenu Animasi -->
                <div id="submenu-logistik" class="submenu-collapse overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 0px;">
                    <ul class="pl-8 pr-2 py-1 space-y-1 border-l-2 border-slate-100 ml-4 my-1">
                        <li>
                            <a href="#" class="block py-1.5 px-3 rounded-lg text-xs font-medium text-slate-600 hover:text-[#ea5921] hover:bg-orange-50/50 transition-colors">
                                Gudang & Stok Bantuan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="block py-1.5 px-3 rounded-lg text-xs font-medium text-slate-600 hover:text-[#ea5921] hover:bg-orange-50/50 transition-colors">
                                Distribusi Bantuan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Menu dengan Submenu 3: Data Wilayah -->
            <div>
                <button type="button" onclick="toggleSubmenu('submenu-wilayah', 'arrow-wilayah')" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all text-slate-600 hover:text-slate-900 hover:bg-slate-50 group">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0 text-slate-500 group-hover:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>GIS dan Layer Peta</span>
                    </div>
                    <svg id="arrow-wilayah" class="w-4 h-4 text-slate-400 transition-transform duration-300 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Submenu Animasi -->
                <div id="submenu-wilayah" class="submenu-collapse overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 0px;">
                    <ul class="pl-8 pr-2 py-1 space-y-1 border-l-2 border-slate-100 ml-4 my-1">
                        <li>
                            <a href="#" class="block py-1.5 px-3 rounded-lg text-xs font-medium text-slate-600 hover:text-[#ea5921] hover:bg-orange-50/50 transition-colors">
                                 Kelurahan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="block py-1.5 px-3 rounded-lg text-xs font-medium text-slate-600 hover:text-[#ea5921] hover:bg-orange-50/50 transition-colors">
                               Kecamatan
                            </a>
                        </li>
                        <li>
                            <a href="#" class="block py-1.5 px-3 rounded-lg text-xs font-medium text-slate-600 hover:text-[#ea5921] hover:bg-orange-50/50 transition-colors">
                                Jenis Bencana
                            </a>
                        </li>
                        <li>
                            <a href="#" class="block py-1.5 px-3 rounded-lg text-xs font-medium text-slate-600 hover:text-[#ea5921] hover:bg-orange-50/50 transition-colors">
                                Titik Rawan Bencana
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-4 px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                Akses Publik
            </div>

            <!-- Item: Lihat Web Publik / Peta -->
            <a href="{{ route('public.home') }}" target="_blank" 
                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span>Peta Kebencanaan</span>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </nav>
    </div>

    <!-- Bagian Bawah: Profil Pengguna & Logout Sederhana -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        <div class="flex items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-full bg-[#ea5921] text-white flex items-center justify-center font-bold text-xs uppercase shrink-0">
                    {{ substr(Auth::user()->nama ?? 'Admin', 0, 2) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate leading-tight">
                        {{ Auth::user()->nama ?? 'Petugas BPBD' }}
                    </p>
                    <p class="text-[10px] text-slate-500 truncate">
                        {{ Auth::user()->email ?? 'admin@bpbd.bogor.go.id' }}
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('public.logout') }}" method="POST">
            @csrf
            <button type="submit" 
                class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar Akun</span>
            </button>
        </form>
    </div>
</aside>

<script>
    function toggleSubmenu(submenuId, arrowId) {
        const submenu = document.getElementById(submenuId);
        const arrow = document.getElementById(arrowId);
        if (!submenu) return;

        const isCollapsed = !submenu.style.maxHeight || submenu.style.maxHeight === '0px';

        if (isCollapsed) {
            // Animasi membuka secara halus sesuai scrollHeight konten
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            if (arrow) arrow.classList.add('rotate-180');
        } else {
            // Animasi menutup kembali ke 0px
            submenu.style.maxHeight = '0px';
            if (arrow) arrow.classList.remove('rotate-180');
        }
    }
</script>
