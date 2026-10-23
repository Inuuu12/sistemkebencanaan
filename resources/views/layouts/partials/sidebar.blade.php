<!-- Sidebar Kunker Style: Bersih, Minimalis, Terstruktur -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 shadow-sm">
    
    <!-- Bagian Atas: Brand & Navigasi -->
    <div>
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-orange-50 border border-orange-200/80 p-1 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/aksara-logo.png') }}" alt="Logo AKSARA" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="font-extrabold text-sm text-slate-900 tracking-tight block leading-none">AKSARA BOGOR</span>
                    <span class="text-[10px] font-semibold text-orange-600 uppercase tracking-wider">Panel Petugas</span>
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

            <!-- Item: Menunggu Verifikasi (Pending) -->
            <a href="{{ route('admin.dashboard', ['status' => 'Pending']) }}" 
                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request('status') == 'Pending' ? 'bg-amber-500 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                    </svg>
                    <span>Laporan Pending</span>
                </div>
                @php
                    $pendingCount = \App\Models\LaporanBencana::where('status', 'Pending')->count();
                @endphp
                @if($pendingCount > 0)
                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ request('status') == 'Pending' ? 'bg-white text-amber-700' : 'bg-amber-100 text-amber-800' }}">
                    {{ $pendingCount }}
                </span>
                @endif
            </a>

            <!-- Item: Dalam Penanganan -->
            <a href="{{ route('admin.dashboard', ['status' => 'Handling']) }}" 
                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request('status') == 'Handling' ? 'bg-blue-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Dalam Penanganan</span>
                </div>
            </a>

            <!-- Item: Selesai Ditangani -->
            <a href="{{ route('admin.dashboard', ['status' => 'Selesai']) }}" 
                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request('status') == 'Selesai' ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Laporan Selesai</span>
                </div>
            </a>

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
