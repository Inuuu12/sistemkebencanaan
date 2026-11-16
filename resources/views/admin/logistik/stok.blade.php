@extends('layouts.admin')

@section('title', 'Gudang & Stok Bantuan Logistik - BPBD Kabupaten Bogor')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Gudang & Inventaris Stok Bantuan</span>
            </h1>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <button type="button" onclick="openCreateModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                <span>Catat Stok Bantuan</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi Flash Message -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
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

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
        <div class="font-semibold mb-1 flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Terdapat kendala pada isian data:</span>
        </div>
        <ul class="list-disc pl-7 space-y-0.5 text-xs">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. REKAPITULASI METRIK GUDANG & LOGISTIK -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Catatan Item -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Entri Stok</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($totalItem) }}</span>
                    <span class="text-xs text-slate-500 font-semibold">Item</span>
                </div>
            </div>
        </div>

        <!-- Stok Masuk -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block">Total Stok (Masuk)</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-emerald-600">{{ number_format($totalMasuk) }}</span>
                    <span class="text-xs text-emerald-800 font-semibold">Unit/Paket</span>
                </div>
            </div>
        </div>
        <!-- Stok Keluar -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800 block">Penyaluran (Keluar)</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-blue-600">{{ number_format($totalKeluar) }}</span>
                    <span class="text-xs text-blue-800 font-semibold">Unit/Paket</span>
                </div>
            </div>
        </div>

        <!-- Kategori Logistik -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Kategori Logistik</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($totalKategori) }}</span>
                    <span class="text-xs text-slate-500 font-semibold">Kelompok</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. FILTER, PENCARIAN & TOOLBAR -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
        <form action="{{ route('admin.logistik.stok') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Input Cari -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama barang, asal sumber, tujuan gudang..." 
                           class="w-full pl-9 pr-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <!-- Filter Kategori -->
                <div class="w-full sm:w-48">
                    <select name="kategori_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $cat)
                            <option value="{{ $cat->id_kategori }}" {{ request('kategori_id') == $cat->id_kategori ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tipe Mutasi -->
                <div class="w-full sm:w-40">
                    <select name="tipe" onchange="this.form.submit()"
                            class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="">Semua Tipe</option>
                        <option value="MASUK" {{ request('tipe') == 'MASUK' ? 'selected' : '' }}>Stok Masuk</option>
                        <option value="KELUAR" {{ request('tipe') == 'KELUAR' ? 'selected' : '' }}>Stok Keluar</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl bg-slate-800 text-white hover:bg-slate-900 transition cursor-pointer">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'kategori_id', 'tipe']))
                    <a href="{{ route('admin.logistik.stok') }}" 
                       class="px-3 py-2 text-xs sm:text-sm font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. TABEL DAFTAR STOK LOGISTIK -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Inventaris Stok Gudang Logistik</h2>
                <p class="text-xs text-slate-500">Total {{ $stokList->total() }}  logistik bantuan.</p>
            </div>
            <a href="{{ route('admin.logistik.distribusi') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#ea5921] hover:underline">
                <span>Lihat Riwayat Distribusi Bantuan</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 min-w-[200px]">Barang & Kategori</th>
                        <th class="py-3 px-4 min-w-[120px] text-center">Tipe Aliran</th>
                        <th class="py-3 px-4 min-w-[140px] text-center">Jumlah & Satuan</th>
                        <th class="py-3 px-4 min-w-[200px]">Sumber / Tujuan</th>
                        <th class="py-3 px-4 min-w-[130px]">Tanggal & Petugas</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stokList as $stok)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- ID -->
                        <td class="py-3 px-4 text-center text-slate-400 font-mono text-xs">
                            #{{ $stok->id_stok }}
                        </td>

                        <!-- Barang & Kategori -->
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 block leading-snug">
                                {{ $stok->nama_item }}
                            </span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 text-orange-800">
                                    {{ $stok->kategoriStok->nama_kategori ?? 'Umum' }}
                                </span>
                                @if($stok->keterangan)
                                    <span class="text-[11px] text-slate-400 truncate max-w-[180px]" title="{{ $stok->keterangan }}">
                                        {{ $stok->keterangan }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Tipe Aliran -->
                        <td class="py-3 px-4 text-center">
                            @if($stok->tipe === 'MASUK')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                    Stok Masuk
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                    Stok Keluar
                                </span>
                            @endif
                        </td>

                        <!-- Jumlah & Satuan -->
                        <td class="py-3 px-4 text-center">
                            <div class="inline-block">
                                <span class="text-base font-black {{ $stok->tipe === 'MASUK' ? 'text-emerald-700' : 'text-blue-700' }}">
                                    {{ $stok->tipe === 'MASUK' ? '+' : '-' }}{{ number_format($stok->jumlah) }}
                                </span>
                                <span class="text-xs font-semibold text-slate-500 block">
                                    {{ $stok->satuan }}
                                </span>
                            </div>
                        </td>

                        <!-- Sumber / Tujuan -->
                        <td class="py-3 px-4">
                            @if($stok->sumber)
                                <div class="text-xs text-slate-700 flex items-center gap-1">
                                    <span class="text-[10px] font-bold uppercase text-slate-400">Asal:</span>
                                    <span class="font-semibold">{{ $stok->sumber }}</span>
                                </div>
                            @endif
                            @if($stok->tujuan)
                                <div class="text-xs text-slate-600 flex items-center gap-1 mt-0.5">
                                    <span class="text-[10px] font-bold uppercase text-slate-400">Gudang/Tujuan:</span>
                                    <span>{{ $stok->tujuan }}</span>
                                </div>
                            @endif
                            @if(!$stok->sumber && !$stok->tujuan)
                                <span class="text-xs text-slate-400 italic">Pencatatan umum gudang</span>
                            @endif
                        </td>

                        <!-- Tanggal & Petugas -->
                        <td class="py-3 px-4">
                            <div class="font-medium text-slate-800">
                                {{ $stok->tanggal ? \Carbon\Carbon::parse($stok->tanggal)->translatedFormat('d M Y') : '-' }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Dicatat: {{ $stok->recorder->nama ?? 'Petugas BPBD' }}
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <!-- Tombol Edit -->
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode([
                                            'id_stok' => $stok->id_stok,
                                            'nama_item' => $stok->nama_item,
                                            'id_kategori' => $stok->id_kategori,
                                            'tipe' => $stok->tipe,
                                            'jumlah' => $stok->jumlah,
                                            'satuan' => $stok->satuan,
                                            'tanggal' => $stok->tanggal ? \Carbon\Carbon::parse($stok->tanggal)->format('Y-m-d') : '',
                                            'sumber' => $stok->sumber,
                                            'tujuan' => $stok->tujuan,
                                            'keterangan' => $stok->keterangan,
                                        ]) }})"
                                        title="Edit Stok" 
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <!-- Tombol Hapus -->
                                <button type="button" 
                                        onclick="confirmDelete({{ $stok->id_stok }}, '{{ addslashes($stok->nama_item) }}')"
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
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <p class="font-bold text-slate-800 text-sm">Belum ada catatan stok bantuan logistik</p>
                            <p class="text-xs text-slate-500 mt-1">Gunakan tombol "Catat Stok Bantuan" di atas untuk menambahkan inventaris bantuan baru.</p>
                            @if(request()->hasAny(['q', 'kategori_id', 'tipe']))
                                <a href="{{ route('admin.logistik.stok') }}" class="inline-block mt-3 px-3 py-1.5 text-xs font-semibold text-[#ea5921] bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                                    Bersihkan Filter
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stokList->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $stokList->links() }}
        </div>
        @endif
    </div>

</div>

<!-- 5. MODAL FORM: TAMBAH STOK BANTUAN -->
<div id="modal-create" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Catat Stok Logistik Baru</h3>
                <p class="text-xs text-slate-500">Penerimaan bantuan masuk atau pengeluaran logistik darurat.</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.logistik.stok.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tipe Mutasi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipe Aliran Stok <span class="text-rose-600">*</span>
                    </label>
                    <select name="tipe" required 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="MASUK">Penerimaan (Stok Masuk)</option>
                        <option value="KELUAR">Pengeluaran (Stok Keluar)</option>
                    </select>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Logistik <span class="text-rose-600">*</span>
                    </label>
                    <select name="id_kategori" required 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        @foreach($kategoriList as $cat)
                            <option value="{{ $cat->id_kategori }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nama Item -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Barang Logistik <span class="text-rose-600">*</span>
                </label>
                <input type="text" id="create_nama_item" name="nama_item" required placeholder="Contoh: Beras Premium 5kg, Mie Instan Sedap, Selimut Wool, Masker Medis" 
                       class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Jumlah -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah <span class="text-rose-600">*</span>
                    </label>
                    <input type="number" name="jumlah" min="1" value="1" required 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <!-- Satuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Satuan <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="satuan" required placeholder="Dus, Box, Paket, Kg, Unit" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Sumber / Donatur -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Sumber / Donatur Bantuan
                    </label>
                    <input type="text" name="sumber" placeholder="Contoh: BNPB Pusat, APBD Bogor, Kemensos, Donasi Warga" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <!-- Lokasi Gudang / Tujuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Lokasi Gudang / Tujuan
                    </label>
                    <input type="text" name="tujuan" placeholder="Contoh: Gudang Utama Cibinong - Rak A2" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan Tambahan
                </label>
                <textarea name="keterangan" rows="2" placeholder="Catatan kondisi barang, nomor surat jalan, tanggal kadaluarsa sembako..." 
                          class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition"></textarea>
            </div>

            <!-- Tombol Aksi Form -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-[#ea5921] hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                    Simpan Catatan Stok
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 6. MODAL FORM: EDIT STOK BANTUAN -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perbarui Data Stok Bantuan</h3>
                <p class="text-xs text-slate-500">Koreksi jumlah atau informasi barang logistik.</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-edit-stok" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipe Aliran Stok <span class="text-rose-600">*</span>
                    </label>
                    <select id="edit_tipe" name="tipe" required 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="MASUK">Penerimaan (Stok Masuk)</option>
                        <option value="KELUAR">Pengeluaran (Stok Keluar)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Logistik <span class="text-rose-600">*</span>
                    </label>
                    <select id="edit_id_kategori" name="id_kategori" required 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        @foreach($kategoriList as $cat)
                            <option value="{{ $cat->id_kategori }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Barang Logistik <span class="text-rose-600">*</span>
                </label>
                <input type="text" id="edit_nama_item" name="nama_item" required 
                       class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah <span class="text-rose-600">*</span>
                    </label>
                    <input type="number" id="edit_jumlah" name="jumlah" min="1" required 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Satuan <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" id="edit_satuan" name="satuan" required 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" id="edit_tanggal" name="tanggal" required 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Sumber / Donatur Bantuan
                    </label>
                    <input type="text" id="edit_sumber" name="sumber" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Lokasi Gudang / Tujuan
                    </label>
                    <input type="text" id="edit_tujuan" name="tujuan" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan Tambahan
                </label>
                <textarea id="edit_keterangan" name="keterangan" rows="2" 
                          class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-[#ea5921] hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 7. MODAL KONFIRMASI HAPUS -->
<div id="modal-delete" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Konfirmasi Hapus Stok</h3>
            <p id="delete-message" class="text-xs text-slate-600 mt-1 leading-relaxed">
                Apakah Anda yakin ingin menghapus data stok logistik ini?
            </p>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-center gap-2.5">
            <button type="button" onclick="closeDeleteModal()" 
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-100 transition cursor-pointer">
                Batal
            </button>
            <form id="form-delete-stok" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 transition shadow-xs cursor-pointer">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modal-create').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => {
            const input = document.getElementById('create_nama_item');
            if (input) input.focus();
        }, 100);
    }

    function closeCreateModal() {
        document.getElementById('modal-create').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openEditModal(data) {
        const form = document.getElementById('form-edit-stok');
        form.action = "{{ url('admin/logistik/stok') }}/" + data.id_stok;

        document.getElementById('edit_nama_item').value = data.nama_item || '';
        document.getElementById('edit_id_kategori').value = data.id_kategori || '';
        document.getElementById('edit_tipe').value = data.tipe || 'MASUK';
        document.getElementById('edit_jumlah').value = data.jumlah || 1;
        document.getElementById('edit_satuan').value = data.satuan || '';
        document.getElementById('edit_tanggal').value = data.tanggal || '';
        document.getElementById('edit_sumber').value = data.sumber || '';
        document.getElementById('edit_tujuan').value = data.tujuan || '';
        document.getElementById('edit_keterangan').value = data.keterangan || '';

        document.getElementById('modal-edit').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditModal() {
        document.getElementById('modal-edit').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function confirmDelete(id, nama) {
        const form = document.getElementById('form-delete-stok');
        form.action = "{{ url('admin/logistik/stok') }}/" + id;

        document.getElementById('delete-message').innerHTML = `Apakah Anda yakin ingin menghapus catatan stok <strong>"${nama}"</strong>? Tindakan ini tidak dapat dibatalkan.`;

        document.getElementById('modal-delete').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDeleteModal() {
        document.getElementById('modal-delete').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeDeleteModal();
        }
    });

    ['modal-create', 'modal-edit', 'modal-delete'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === this) {
                    closeCreateModal();
                    closeEditModal();
                    closeDeleteModal();
                }
            });
        }
    });
</script>
@endpush
@endsection
