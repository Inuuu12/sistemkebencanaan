@extends('layouts.public')

@section('title', 'Berita Kebencanaan - AKSARA BOGOR')
@section('meta_description', 'Pusat Informasi, Peringatan Dini, dan Berita Terkini Penanggulangan Bencana Kabupaten Bogor.')

@section('content')
<!-- Hero Header Section -->
<section class="bg-gradient-to-b from-orange-50/80 to-transparent dark:from-orange-950/20 dark:to-transparent py-12 sm:py-16 border-b border-orange-100/60 dark:border-slate-800">
    <div class="container mx-auto px-4 sm:px-6 text-center max-w-3xl">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 dark:bg-orange-950/80 text-[#ea5921] dark:text-orange-400 text-xs font-black uppercase tracking-wider mb-4 border border-orange-200 dark:border-orange-800/60">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            Pusat Informasi Publik
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight mb-4">
            Berita & Publikasi Kebencanaan
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
            Rilis pers resmi, info peringatan dini cuaca ekstrem, dan penanganan bencana di wilayah Kabupaten Bogor langsung dari Pusdalops PB BPBD.
        </p>

        <!-- Search Form -->
        <form action="{{ route('public.news') }}" method="GET" class="mt-8 max-w-lg mx-auto flex items-center gap-2 bg-white dark:bg-slate-900 p-1.5 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-800">
            <div class="pl-3 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="2"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/>
                </svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau topik berita..." 
                class="w-full text-xs sm:text-sm bg-transparent border-0 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-0">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white text-xs font-black shadow-md transition-all shrink-0">
                Cari
            </button>
        </form>
    </div>
</section>

<!-- News Articles Grid -->
<section class="container mx-auto px-4 sm:px-6 py-12">
    @if($news->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        @foreach($news as $item)
        <a href="{{ route('public.news.detail', $item->id_berita) }}" class="group flex flex-col bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-orange-500/10 hover:border-orange-300 dark:hover:border-orange-800/80 transition-all duration-300">
            <!-- Thumbnail Image -->
            <div class="h-48 sm:h-52 bg-slate-100 dark:bg-slate-800 relative overflow-hidden">
                @if($item->gambar)
                <img src="{{ $item->gambar }}" alt="{{ $item->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-gradient-to-br from-orange-50 to-amber-100 dark:from-slate-800 dark:to-slate-900">
                    <svg class="w-10 h-10 text-orange-400/50 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">AKSARA NEWS</span>
                </div>
                @endif
                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/90 dark:bg-slate-900/90 text-[#ea5921] dark:text-orange-400 backdrop-blur-md shadow-sm">
                    {{ $item->sumber ?: 'PUSDALOPS BPBD' }}
                </span>
            </div>

            <!-- Content -->
            <div class="p-5 sm:p-6 flex flex-col flex-1">
                <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400 mb-2.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $item->created_at ? $item->created_at->translatedFormat('d F Y') : '-' }}</span>
                </div>

                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white line-clamp-2 group-hover:text-[#ea5921] transition-colors leading-snug mb-2">
                    {{ $item->judul }}
                </h3>

                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed mb-5">
                    {{ Str::limit(strip_tags($item->isi), 140) }}
                </p>

                <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs font-black text-[#ea5921]">
                    <span>Mulai Membaca</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-12 flex justify-center">
        {{ $news->links() }}
    </div>
    @else
    <div class="max-w-md mx-auto text-center py-16 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-orange-100 dark:bg-orange-950/60 text-[#ea5921] flex items-center justify-center mx-auto mb-4 text-2xl">
            📰
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Belum Ada Berita</h3>
        <p class="text-xs text-slate-500 mb-6">Tidak ditemukan berita dengan kata kunci yang Anda masukkan.</p>
        <a href="{{ route('public.news') }}" class="px-5 py-2.5 rounded-xl bg-[#ea5921] text-white text-xs font-black">
            Lihat Semua Berita
        </a>
    </div>
    @endif
</section>
@endsection
