@extends('layouts.admin')

@section('title', 'Data Korban Bencana - Rekapitulasi & Manajemen')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-[#ea5921]">Data Korban</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Data Korban & Dampak Bencana</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                Pencatatan rincian dampak kemanusiaan: korban jiwa, luka-luka, pengungsi, dan orang hilang di Kabupaten Bogor.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <button type="button" onclick="openCreateModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Catat Data Korban</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi Flash Message -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
        <div class="font-semibold mb-1 flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Terdapat kendala pada data yang dikirim:</span>
        </div>
        <ul class="list-disc pl-7 space-y-0.5 text-xs">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. REKAPITULASI KARTU METRIK NYATA -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Titik / Insiden -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Titik Insiden</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalTitikKejadian) }}</span>
                <span class="text-xs text-slate-500">Lokasi</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Terekam di sistem</p>
        </div>

        <!-- Korban Jiwa (Meninggal) -->
        <div class="p-4 rounded-2xl bg-white border border-rose-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 block">Meninggal Dunia</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-rose-600">{{ number_format($totalMeninggal) }}</span>
                <span class="text-xs text-rose-700 font-semibold">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Korban jiwa terverifikasi</p>
        </div>

        <!-- Luka Berat -->
        <div class="p-4 rounded-2xl bg-white border border-amber-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 block">Luka Berat</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-amber-700">{{ number_format($totalLukaBerat) }}</span>
                <span class="text-xs text-amber-800 font-semibold">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Perlu rawat intensif</p>
        </div>

        <!-- Luka Ringan -->
        <div class="p-4 rounded-2xl bg-white border border-yellow-200/90 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-yellow-800 block">Luka Ringan</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-yellow-700">{{ number_format($totalLukaRingan) }}</span>
                <span class="text-xs text-yellow-800 font-semibold">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Rawat jalan / P3K</p>
        </div>

        <!-- Pengungsi -->
        <div class="p-4 rounded-2xl bg-white border border-blue-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700 block">Mengungsi</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-blue-600">{{ number_format($totalMengungsi) }}</span>
                <span class="text-xs text-blue-700 font-semibold">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Di posko & keluarga</p>
        </div>

        <!-- Hilang -->
        <div class="p-4 rounded-2xl bg-white border border-purple-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700 block">Hilang / SAR</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-purple-600">{{ number_format($totalHilang) }}</span>
                <span class="text-xs text-purple-700 font-semibold">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Proses pencarian</p>
        </div>
    </div>

    <!-- 3. FILTER & PENCARIAN -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.korban.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Pencarian Teks -->
            <div class="lg:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kejadian, lokasi, atau catatan..." 
                       class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <!-- Filter Kecamatan -->
            <div class="lg:col-span-3">
                <select name="kecamatan_id" class="w-full py-2 px-3 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Kecamatan</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id_kecamatan }}" {{ request('kecamatan_id') == $d->id_kecamatan ? 'selected' : '' }}>
                            Kec. {{ $d->nama_kecamatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Jenis Bencana -->
            <div class="lg:col-span-2">
                <select name="bencana_id" class="w-full py-2 px-3 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Bencana</option>
                    @foreach($disasters as $b)
                        <option value="{{ $b->id_bencana }}" {{ request('bencana_id') == $b->id_bencana ? 'selected' : '' }}>
                            {{ $b->nama_bencana }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kondisi Spesifik -->
            <div class="lg:col-span-2">
                <select name="kondisi" class="w-full py-2 px-3 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Dampak</option>
                    <option value="meninggal" {{ request('kondisi') == 'meninggal' ? 'selected' : '' }}>Ada Korban Meninggal</option>
                    <option value="luka" {{ request('kondisi') == 'luka' ? 'selected' : '' }}>Ada Korban Luka</option>
                    <option value="mengungsi" {{ request('kondisi') == 'mengungsi' ? 'selected' : '' }}>Ada Pengungsi</option>
                    <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>Ada Korban Hilang</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="lg:col-span-1 flex items-center gap-1.5">
                <button type="submit" title="Terapkan Filter" class="w-full py-2 px-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold flex items-center justify-center transition">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'kecamatan_id', 'bencana_id', 'kondisi']))
                    <a href="{{ route('admin.korban.index') }}" title="Reset Filter" class="p-2 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- 4. TABEL UTAMA DATA KORBAN -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-sm text-slate-900">Rincian Data Korban Terdata</h2>
            <span class="text-xs text-slate-500">Menampilkan <strong>{{ $korbanList->total() }}</strong> catatan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[200px]">Kejadian Bencana</th>
                        <th class="py-3 px-4 min-w-[180px]">Lokasi Wilayah</th>
                        <th class="py-3 px-4 min-w-[240px]">Rincian Jiwa Terdampak</th>
                        <th class="py-3 px-4 min-w-[200px]">Keterangan Kondisi</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($korbanList as $k)
                    @php
                        $laporan = $k->laporanBencana;
                        $kelurahan = $k->kelurahan ?? ($laporan ? $laporan->kelurahan : null);
                        $kecamatan = $kelurahan ? $kelurahan->kecamatan : ($laporan ? $laporan->kecamatan : null);
                        $totalJiwa = $k->jumlah_meninggal + $k->jumlah_luka_berat + $k->jumlah_luka_ringan + $k->jumlah_mengungsi + $k->jumlah_hilang;
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- ID -->
                        <td class="py-3 px-4 text-center text-slate-400 font-mono text-xs">
                            #{{ $k->id_korban }}
                        </td>

                        <!-- Kejadian Bencana -->
                        <td class="py-3 px-4">
                            @if($laporan)
                                <div class="font-bold text-slate-900 hover:text-[#ea5921] transition-colors leading-snug">
                                    {{ $laporan->judul }}
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 text-orange-800">
                                        {{ $laporan->bencana->nama_bencana ?? 'Bencana' }}
                                    </span>
                                    <span class="text-[11px] text-slate-500">
                                        {{ $laporan->tanggal_kejadian ? \Carbon\Carbon::parse($laporan->tanggal_kejadian)->translatedFormat('d M Y') : $laporan->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            @else
                                <span class="font-medium text-slate-700 italic">Pencatatan Mandiri Wilayah</span>
                                <div class="text-[11px] text-slate-400 mt-0.5">Tidak ditautkan ke aduan warga</div>
                            @endif
                        </td>

                        <!-- Lokasi Wilayah -->
                        <td class="py-3 px-4">
                            <div class="font-semibold text-slate-800">
                                {{ $kelurahan ? $kelurahan->nama_kelurahan : '-' }}
                            </div>
                            <div class="text-xs text-slate-500">
                                Kec. {{ $kecamatan ? $kecamatan->nama_kecamatan : '-' }}
                            </div>
                            @if($laporan && $laporan->alamat_detail)
                                <div class="text-[11px] text-slate-400 mt-1 line-clamp-1" title="{{ $laporan->alamat_detail }}">
                                    {{ $laporan->alamat_detail }}
                                </div>
                            @endif
                        </td>

                        <!-- Rincian Jiwa Terdampak -->
                        <td class="py-3 px-4">
                            <div class="flex flex-wrap gap-1.5 items-center">
                                @if($k->jumlah_meninggal > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        {{ $k->jumlah_meninggal }} Meninggal
                                    </span>
                                @endif

                                @if($k->jumlah_luka_berat > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        {{ $k->jumlah_luka_berat }} Luka Berat
                                    </span>
                                @endif

                                @if($k->jumlah_luka_ringan > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-yellow-100 text-yellow-900 border border-yellow-200">
                                        {{ $k->jumlah_luka_ringan }} Luka Ringan
                                    </span>
                                @endif

                                @if($k->jumlah_mengungsi > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-100 text-blue-900 border border-blue-200">
                                        {{ $k->jumlah_mengungsi }} Mengungsi
                                    </span>
                                @endif

                                @if($k->jumlah_hilang > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-purple-100 text-purple-900 border border-purple-200">
                                        {{ $k->jumlah_hilang }} Hilang
                                    </span>
                                @endif

                                @if($totalJiwa == 0)
                                    <span class="text-xs text-slate-400 italic">Nihil korban jiwa/luka</span>
                                @endif
                            </div>
                            <div class="text-[11px] font-semibold text-slate-600 mt-1">
                                Total: <span class="text-slate-900">{{ number_format($totalJiwa) }} Jiwa</span>
                            </div>
                        </td>

                        <!-- Keterangan Kondisi -->
                        <td class="py-3 px-4">
                            @if($k->keterangan)
                                <p class="text-xs text-slate-700 line-clamp-2" title="{{ $k->keterangan }}">
                                    {{ $k->keterangan }}
                                </p>
                            @else
                                <span class="text-xs text-slate-400 italic">- Tidak ada catatan -</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center gap-1 justify-center">
                                <!-- Tombol Edit -->
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode([
                                            'id_korban' => $k->id_korban,
                                            'id_laporan' => $k->id_laporan,
                                            'id_kelurahan' => $k->id_kelurahan,
                                            'id_kecamatan' => $kelurahan ? $kelurahan->id_kecamatan : ($kecamatan ? $kecamatan->id_kecamatan : ''),
                                            'jumlah_meninggal' => $k->jumlah_meninggal,
                                            'jumlah_luka_berat' => $k->jumlah_luka_berat,
                                            'jumlah_luka_ringan' => $k->jumlah_luka_ringan,
                                            'jumlah_mengungsi' => $k->jumlah_mengungsi,
                                            'jumlah_hilang' => $k->jumlah_hilang,
                                            'keterangan' => $k->keterangan,
                                        ]) }})"
                                        title="Edit Data" 
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <!-- Tombol Hapus -->
                                <button type="button" 
                                        onclick="confirmDelete({{ $k->id_korban }})"
                                        title="Hapus Data" 
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="font-bold text-slate-800 text-sm">Tidak ditemukan data korban</p>
                            <p class="text-xs text-slate-500 mt-1">Coba sesuaikan filter pencarian atau catat data korban baru melalui tombol di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($korbanList->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $korbanList->links() }}
        </div>
        @endif
    </div>

</div>

<!-- 5. MODAL FORM: TAMBAH / CATAT DATA KORBAN BARU -->
<div id="modal-create" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Catat Rincian Korban Bencana</h3>
                <p class="text-xs text-slate-500">Tambahkan jumlah jiwa terdampak dan catatan penanganan medis/pengungsian.</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.korban.store') }}" class="p-6 space-y-4">
            @csrf

            <!-- Tautkan ke Laporan Bencana (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tautkan ke Aduan/Laporan Kejadian <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="id_laporan" id="create_id_laporan" onchange="autoFillLocation(this, 'create')" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    <option value="">-- Tanpa Tautan (Pencatatan Mandiri Wilayah) --</option>
                    @foreach($laporanOptions as $lp)
                        <option value="{{ $lp->id_laporan }}" 
                                data-kecamatan="{{ $lp->id_kecamatan }}" 
                                data-kelurahan="{{ $lp->id_kelurahan }}">
                            #{{ $lp->id_laporan }} - {{ $lp->judul }} ({{ $lp->kecamatan->nama_kecamatan ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Kecamatan & Kelurahan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kecamatan <span class="text-rose-600">*</span>
                    </label>
                    <select id="create_kecamatan" onchange="filterKelurahanDropdown('create')" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kecamatan</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id_kecamatan }}">{{ $d->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kelurahan/Desa <span class="text-rose-600">*</span>
                    </label>
                    <select name="id_kelurahan" id="create_kelurahan" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kelurahan</option>
                    </select>
                </div>
            </div>

            <!-- Grid Angka Dampak Korban -->
            <div class="pt-2 border-t border-slate-100">
                <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Rincian Korban Jiwa & Kondisi (Jiwa)
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="p-2.5 rounded-xl bg-rose-50/60 border border-rose-200">
                        <label class="block text-[11px] font-bold text-rose-800 uppercase mb-1">Meninggal</label>
                        <input type="number" name="jumlah_meninggal" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-rose-300 bg-white focus:ring-2 focus:ring-rose-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-amber-50/60 border border-amber-200">
                        <label class="block text-[11px] font-bold text-amber-900 uppercase mb-1">Luka Berat</label>
                        <input type="number" name="jumlah_luka_berat" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-amber-300 bg-white focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-yellow-50/60 border border-yellow-200">
                        <label class="block text-[11px] font-bold text-yellow-900 uppercase mb-1">Luka Ringan</label>
                        <input type="number" name="jumlah_luka_ringan" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-yellow-300 bg-white focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-blue-50/60 border border-blue-200">
                        <label class="block text-[11px] font-bold text-blue-900 uppercase mb-1">Mengungsi</label>
                        <input type="number" name="jumlah_mengungsi" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-blue-300 bg-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-purple-50/60 border border-purple-200">
                        <label class="block text-[11px] font-bold text-purple-900 uppercase mb-1">Hilang</label>
                        <input type="number" name="jumlah_hilang" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-purple-300 bg-white focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Keterangan & Catatan Medis/Posko -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan & Catatan Khusus
                </label>
                <textarea name="keterangan" rows="2" placeholder="Contoh: 2 korban luka dirawat di RSUD Cibinong, 15 pengungsi berada di Balai Desa..." class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500"></textarea>
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                    Simpan Data Korban
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 6. MODAL FORM: EDIT DATA KORBAN -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perbarui Data Korban</h3>
                <p class="text-xs text-slate-500" id="edit_modal_subtitle">Edit rincian data korban.</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="edit_form" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Tautkan ke Laporan Bencana (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tautkan ke Aduan/Laporan Kejadian <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="id_laporan" id="edit_id_laporan" onchange="autoFillLocation(this, 'edit')" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    <option value="">-- Tanpa Tautan (Pencatatan Mandiri Wilayah) --</option>
                    @foreach($laporanOptions as $lp)
                        <option value="{{ $lp->id_laporan }}" 
                                data-kecamatan="{{ $lp->id_kecamatan }}" 
                                data-kelurahan="{{ $lp->id_kelurahan }}">
                            #{{ $lp->id_laporan }} - {{ $lp->judul }} ({{ $lp->kecamatan->nama_kecamatan ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Pilih Kecamatan & Kelurahan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kecamatan <span class="text-rose-600">*</span>
                    </label>
                    <select id="edit_kecamatan" onchange="filterKelurahanDropdown('edit')" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kecamatan</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id_kecamatan }}">{{ $d->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kelurahan/Desa <span class="text-rose-600">*</span>
                    </label>
                    <select name="id_kelurahan" id="edit_kelurahan" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kelurahan</option>
                    </select>
                </div>
            </div>

            <!-- Grid Angka Dampak Korban -->
            <div class="pt-2 border-t border-slate-100">
                <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Rincian Korban Jiwa & Kondisi (Jiwa)
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="p-2.5 rounded-xl bg-rose-50/60 border border-rose-200">
                        <label class="block text-[11px] font-bold text-rose-800 uppercase mb-1">Meninggal</label>
                        <input type="number" name="jumlah_meninggal" id="edit_jumlah_meninggal" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-rose-300 bg-white focus:ring-2 focus:ring-rose-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-amber-50/60 border border-amber-200">
                        <label class="block text-[11px] font-bold text-amber-900 uppercase mb-1">Luka Berat</label>
                        <input type="number" name="jumlah_luka_berat" id="edit_jumlah_luka_berat" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-amber-300 bg-white focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-yellow-50/60 border border-yellow-200">
                        <label class="block text-[11px] font-bold text-yellow-900 uppercase mb-1">Luka Ringan</label>
                        <input type="number" name="jumlah_luka_ringan" id="edit_jumlah_luka_ringan" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-yellow-300 bg-white focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-blue-50/60 border border-blue-200">
                        <label class="block text-[11px] font-bold text-blue-900 uppercase mb-1">Mengungsi</label>
                        <input type="number" name="jumlah_mengungsi" id="edit_jumlah_mengungsi" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-blue-300 bg-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="p-2.5 rounded-xl bg-purple-50/60 border border-purple-200">
                        <label class="block text-[11px] font-bold text-purple-900 uppercase mb-1">Hilang</label>
                        <input type="number" name="jumlah_hilang" id="edit_jumlah_hilang" value="0" min="0" required class="w-full text-sm font-bold py-1.5 px-2.5 rounded-lg border border-purple-300 bg-white focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <!-- Keterangan & Catatan Medis/Posko -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan & Catatan Khusus
                </label>
                <textarea name="keterangan" id="edit_keterangan" rows="2" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500"></textarea>
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition shadow-sm cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 7. FORM DELETE TERSEMBUNYI -->
<form id="delete_form" method="POST" action="" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    // Data Kecamatan & Kelurahan untuk dynamic dropdown
    const districtsData = @json($districts);

    function filterKelurahanDropdown(prefix, selectedKelurahanId = null) {
        const kecSelect = document.getElementById(`${prefix}_kecamatan`);
        const kelSelect = document.getElementById(`${prefix}_kelurahan`);
        const kecId = kecSelect.value;

        kelSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
        if (!kecId) return;

        const district = districtsData.find(d => d.id_kecamatan == kecId);
        if (district && district.kelurahan) {
            district.kelurahan.forEach(kel => {
                const opt = document.createElement('option');
                opt.value = kel.id_kelurahan;
                opt.textContent = kel.nama_kelurahan;
                if (selectedKelurahanId && kel.id_kelurahan == selectedKelurahanId) {
                    opt.selected = true;
                }
                kelSelect.appendChild(opt);
            });
        }
    }

    function autoFillLocation(selectElem, prefix) {
        const opt = selectElem.selectedOptions[0];
        if (!opt || !opt.value) return;

        const kecId = opt.getAttribute('data-kecamatan');
        const kelId = opt.getAttribute('data-kelurahan');

        if (kecId) {
            document.getElementById(`${prefix}_kecamatan`).value = kecId;
            filterKelurahanDropdown(prefix, kelId);
        }
    }

    // Modal Create Handlers
    function openCreateModal() {
        document.getElementById('modal-create').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('modal-create').classList.add('hidden');
    }

    // Modal Edit Handlers
    function openEditModal(data) {
        const form = document.getElementById('edit_form');
        form.action = `/admin/korban/${data.id_korban}`;

        document.getElementById('edit_modal_subtitle').textContent = `Perbarui rincian korban untuk entri #${data.id_korban}`;
        document.getElementById('edit_id_laporan').value = data.id_laporan || '';
        
        if (data.id_kecamatan) {
            document.getElementById('edit_kecamatan').value = data.id_kecamatan;
            filterKelurahanDropdown('edit', data.id_kelurahan);
        } else {
            document.getElementById('edit_kecamatan').value = '';
            document.getElementById('edit_kelurahan').innerHTML = '<option value="">Pilih Kelurahan</option>';
        }

        document.getElementById('edit_jumlah_meninggal').value = data.jumlah_meninggal || 0;
        document.getElementById('edit_jumlah_luka_berat').value = data.jumlah_luka_berat || 0;
        document.getElementById('edit_jumlah_luka_ringan').value = data.jumlah_luka_ringan || 0;
        document.getElementById('edit_jumlah_mengungsi').value = data.jumlah_mengungsi || 0;
        document.getElementById('edit_jumlah_hilang').value = data.jumlah_hilang || 0;
        document.getElementById('edit_keterangan').value = data.keterangan || '';

        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modal-edit').classList.add('hidden');
    }

    // Delete Confirmation
    function confirmDelete(id) {
        if (confirm(`Yakin ingin menghapus data korban #${id}? Data yang dihapus tidak dapat dipulihkan.`)) {
            const form = document.getElementById('delete_form');
            form.action = `/admin/korban/${id}`;
            form.submit();
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush
