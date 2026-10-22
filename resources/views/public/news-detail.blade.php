@extends('layouts.public')

@section('title', $article->judul . ' - AKSARA BOGOR')
@section('meta_description', Str::limit(strip_tags($article->isi), 150))

@section('content')
<div class="container mx-auto px-4 sm:px-6 py-8">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('public.news') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-[#ea5921] dark:hover:text-orange-400 transition-colors group">
            <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 group-hover:bg-orange-100 dark:group-hover:bg-orange-950/40 transition-colors">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </div>
            <span>Kembali ke Daftar Berita</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12">
        
        <!-- Main Article Content -->
        <article class="lg:col-span-8 flex flex-col bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200/90 dark:border-slate-800 shadow-sm">
            
            <!-- Meta Details -->
            <div class="flex flex-wrap items-center gap-3 text-xs font-bold mb-4">
                <span class="px-3 py-1 rounded-full bg-orange-100 dark:bg-orange-950 text-[#ea5921] dark:text-orange-400 uppercase tracking-wider text-[11px]">
                    {{ $article->sumber ?: 'PUSDALOPS BPBD' }}
                </span>
                <span class="text-slate-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $article->created_at ? $article->created_at->translatedFormat('l, d F Y - H:i') . ' WIB' : '-' }}
                </span>
            </div>

            <!-- Headline Title -->
            <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white leading-tight tracking-tight mb-6">
                {{ $article->judul }}
            </h1>

            <!-- Big Banner Image -->
            @if($article->gambar)
            <div class="mb-8 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-800 max-h-[420px]">
                <img src="{{ $article->gambar }}" alt="{{ $article->judul }}" class="w-full h-full object-cover">
            </div>
            @endif

            <!-- Article Body -->
            <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-200 text-sm sm:text-base leading-relaxed space-y-4">
                @php
                    $paragraphs = preg_split('/\r\n|\r|\n/', $article->isi);
                @endphp
                @foreach($paragraphs as $para)
                    @if(trim($para) !== '')
                    <p class="text-justify leading-relaxed">
                        {{ $para }}
                    </p>
                    @endif
                @endforeach
            </div>

            <!-- Article Footer / Share Notice -->
            <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>Sumber Berita Resmi: {{ $article->sumber ?: 'Pusdalops PB BPBD Kabupaten Bogor' }}</span>
                <a href="{{ route('public.home') }}" class="font-bold text-[#ea5921] hover:underline">
                    Buka Peta Kebencanaan →
                </a>
            </div>
        </article>

        <!-- Sidebar (Related News & Emergency Contacts) -->
        <aside class="lg:col-span-4 space-y-6">
            
            <!-- Related News Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/90 dark:border-slate-800 shadow-sm">
                <h3 class="font-black text-base text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#ea5921]"></span>
                    Berita Terkait Lainnya
                </h3>

                <div class="space-y-4">
                    @forelse($relatedNews as $rel)
                    <a href="{{ route('public.news.detail', $rel->id_berita) }}" class="flex items-start gap-3 group">
                        <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-800 overflow-hidden shrink-0">
                            @if($rel->gambar)
                            <img src="{{ $rel->gambar }}" alt="{{ $rel->judul }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-xs text-slate-400">News</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] text-slate-400 block mb-0.5">
                                {{ $rel->created_at ? $rel->created_at->translatedFormat('d M Y') : '-' }}
                            </span>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-2 group-hover:text-[#ea5921] transition-colors leading-snug">
                                {{ $rel->judul }}
                            </h4>
                        </div>
                    </a>
                    @empty
                    <p class="text-xs text-slate-400">Tidak ada berita terkait lainnya.</p>
                    @endforelse
                </div>
            </div>

            <!-- Emergency Hotline Widget -->
            <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-3xl p-6 text-white shadow-xl shadow-red-500/20">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 rounded-xl bg-white/20 backdrop-blur-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-base">Butuh Bantuan Cepat?</h4>
                        <p class="text-xs text-white/80">Layanan Darurat Bebas Pulsa</p>
                    </div>
                </div>
                <p class="text-xs text-white/90 leading-relaxed mb-4">
                    Hubungi Call Center 112 BPBD Kabupaten Bogor jika terjadi kondisi darurat bencana atau mengancam keselamatan jiwa.
                </p>
                <a href="tel:112" class="w-full inline-flex items-center justify-center gap-2 py-3 rounded-2xl bg-white text-red-600 font-black text-sm shadow-md hover:bg-slate-50 transition-all">
                    <span>Telepon Call Center 112</span>
                </a>
            </div>

        </aside>

    </div>
</div>
@endsection
