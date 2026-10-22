<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Kebencanaan Kabupaten Bogor - AKSARA BOGOR')</title>
    <meta name="description" content="@yield('meta_description', 'Portal Resmi Pemantauan dan Tanggap Darurat Bencana Alam Kabupaten Bogor. PUSDAOPS PB BPBD Kabupaten Bogor.')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/aksara-logo-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/aksara-logo-192.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN for reliable zero-build instant rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ff4800ff',
                            100: '#f78c00ff',
                            200: '#ffb62c',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#ea5921',
                            600: '#d94812',
                            700: '#c2380f',
                            800: '#9a2e12',
                            900: '#7c2912',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Leaflet GIS CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(243, 6, 6, 0.03);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(150,150,150,0.3);
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(150,150,150,0.5);
        }


        /* Leaflet Boundary Tooltip */
        .sigab-boundary-label {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            pointer-events: none;
        }
        .sigab-boundary-label::before {
            display: none;
        }

        /* Marquee Category Rail */
        @keyframes marquee-scroll {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .category-rail-track {
            display: flex;
            width: max-content;
            animation: marquee-scroll 45s linear infinite;
        }
        .category-rail-track:hover {
            animation-play-state: paused;
        }
    </style>

    <script>
        // Check theme immediately to prevent white flash in dark mode
        if (localStorage.getItem('aksara_theme') === 'dark' || (!localStorage.getItem('aksara_theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-[#0a0a0a] text-slate-800 dark:text-slate-100 antialiased selection:bg-[#ea5921] selection:text-white transition-colors duration-300">
    <div class="min-h-screen @yield('body_class', 'flex flex-col') relative">
        
        <!-- HEADER & NAVBAR -->
<header class="@yield('header_class', 'sticky top-0') z-[500] bg-[#ea5921] border-b border-orange-600/80 shadow-md transition-colors duration-300" style="z-index: 500;">
            <div class="container mx-auto flex h-14 sm:h-16 items-center justify-between gap-2 sm:gap-4 px-3 sm:px-6">
                
                <!-- Logo & Branding -->
                <a href="{{ route('public.home') }}" class="flex shrink-0 items-center gap-2.5 sm:gap-3 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center group-hover:scale-105 transition-transform shrink-0">
                        <img src="{{ asset('images/aksara-logo.png') }}" alt="AKSARA BOGOR" class="w-full h-full object-contain drop-shadow-sm">
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="font-black text-sm sm:text-lg tracking-tight text-white leading-tight whitespace-nowrap flex items-center gap-1.5">
                            AKSARA BOGOR
                        </span>
                        <span class="text-[10px] sm:text-[11px] text-white/90 font-medium hidden md:block -mt-0.5">
                            Akses Informasi Kebencanaan Kabupaten Bogor
                        </span>
                    </div>
                </a>

                <!-- Header Actions & Navigation -->
                <nav class="flex items-center gap-1.5 sm:gap-2.5 text-sm font-medium ml-auto">
                    
                    @if(Route::is('public.home'))
                    @endif
                <!-- Theme Toggle (Light / Dark) -->
                    <button type="button" id="theme-toggle" onclick="toggleTheme()" 
                        class="inline-flex h-8 w-8 sm:h-10 sm:w-10 shrink-0 items-center justify-center rounded-xl border border-white/30 bg-white/15 text-white hover:bg-white/25 shadow-sm transition-all active:scale-95" 
                        title="Ganti Tema">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg class="hidden dark:block w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="5" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg class="block dark:hidden w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                    <!-- Berita Link -->
                    <a href="{{ route('public.news') }}" 
                        class="inline-flex h-8 sm:h-10 items-center justify-center gap-1.5 rounded-xl border border-white/30 bg-white/15 hover:bg-white/25 text-white shadow-sm transition-colors px-2.5 sm:px-3 text-xs font-bold shrink-0 {{ Route::is('public.news*') ? 'bg-white text-[#ea5921] border-white' : '' }}">
                        <span class="hidden sm:inline">Berita</span>
                    </a>

                    <!-- Lapor Bencana Button -->
                    <a href="{{ route('public.aduan') }}" 
                        class="inline-flex h-8 sm:h-10 items-center justify-center gap-1.5 rounded-xl bg-white hover:bg-orange-50 text-[#ea5921] px-3 sm:px-4 py-1 font-black text-xs shadow-md border border-white/60 transition-all hover:scale-[1.02] active:scale-[0.98] shrink-0">
                        <span>Lapor Bencana</span>
                    </a>

                    <!-- Masuk Button -->
                    <a href="{{ route('public.login') }}" 
                        class="inline-flex h-8 sm:h-10 items-center justify-center gap-1.5 rounded-xl bg-white hover:bg-orange-50 text-[#ea5921] px-3 sm:px-4 py-1 font-black text-xs shadow-md border border-white/60 transition-all hover:scale-[1.02] active:scale-[0.98] shrink-0">
                        <span>Masuk</span>
                    </a>
                </nav>
            </div>

            <!-- Cuaca -->
            @if(isset($weather) && count($weather) > 0)
            <div id="weather-banner" class="bg-white/95 dark:bg-slate-900/95 border-t border-orange-600/30 dark:border-slate-800 overflow-hidden py-1.5 sm:py-2 text-xs shadow-sm">
                <div class="container mx-auto px-3 sm:px-6 flex items-center gap-3">
                    <div class="flex items-center gap-1.5 shrink-0 pr-3 border-r border-orange-200 dark:border-slate-800 font-extrabold text-[11px] text-[#ea5921] dark:text-amber-400 uppercase tracking-wider">
                        <span>Info Cuaca </span>
                    </div>

                    <div class="flex-1 overflow-hidden">
                        <marquee behavior="scroll" direction="left" scrollamount="4" class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                            @foreach($weather as $w)
                                <span class="mr-8">
                                    <span class="text-[#ea5921] dark:text-amber-400 font-bold">{{ $w['location'] }}</span>: 
                                    {{ $w['temp'] }}°C, {{ $w['condition'] }} 
                                    @if(isset($w['humidity'])) <span class="text-slate-500 dark:text-slate-400 text-[11px]">(Lembab {{ $w['humidity'] }}%)</span> @endif
                                </span>
                            @endforeach
                        </marquee>
                    </div>
                </div>
            </div>
            @endif
        </header>

        <!-- MAIN CONTENT -->
        <main class="@yield('main_class', 'flex-1')">
            @yield('content')
        </main>

        <!-- FOOTER (Rendered on standard pages, hidden on fullscreen map mode) -->
        @sectionMissing('hide_footer')
        <footer class="border-t border-slate-200 dark:border-slate-800 mt-20 py-10 bg-white/60 dark:bg-slate-950/60 backdrop-blur-md">
            <div class="container mx-auto px-6 text-center text-sm text-slate-500 dark:text-slate-400">
                <div class="flex items-center justify-center gap-2 mb-3">
                    <img src="{{ asset('images/aksara-logo.png') }}" alt="Logo" class="w-7 h-7 object-contain">
                    <span class="font-extrabold text-slate-800 dark:text-white">AKSARA BOGOR</span>
                </div>
                <p>&copy; {{ date('Y') }} Sistem Informasi Kebencanaan Kabupaten Bogor.</p>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                    Pusat Pengendalian Operasi Penanggulangan Bencana (PUSDALOPS PB) - BPBD Kabupaten Bogor.
                </p>
            </div>
        </footer>
        @endif

    </div>

    <!-- Theme Toggle Script -->
    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('aksara_theme', isDark ? 'dark' : 'light');
            window.dispatchEvent(new Event('theme-changed'));
        }
    </script>

    @stack('scripts')
</body>
</html>
