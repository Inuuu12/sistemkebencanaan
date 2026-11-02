@extends('layouts.admin')

@section('title', 'Data Kerusakan Bangunan & Infrastruktur')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-[#ea5921]">Data Kerusakan</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Data Kerusakan Bangunan & Fasilitas</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                Inventarisasi dampak fisik pada rumah tinggal, sarana ibadah, fasilitas pendidikan, dan infrastruktur umum Kabupaten Bogor.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <button type="button" onclick="openCreateModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Catat Kerusakan Baru</span>
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
            <span>Terdapat kendala pada data kerusakan:</span>
        </div>
        <ul class="list-disc pl-7 space-y-0.5 text-xs">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. REKAPITULASI METRIK KERUSAKAN NYATA -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Insiden -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Titik Kerusakan</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalInsidenKerusakan) }}</span>
                <span class="text-xs text-slate-500">Insiden</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Terverifikasi di sistem</p>
        </div>

        <!-- Total Unit Rusak -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block">Total Unit Rusak</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalUnit) }}</span>
                <span class="text-xs text-slate-500 font-semibold">Unit</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Bangunan & fasilitas</p>
        </div>

        <!-- Rusak Berat -->
        <div class="p-4 rounded-2xl bg-white border border-rose-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-800 block">Rusak Berat (RB)</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-rose-600">{{ number_format($totalRusakBerat) }}</span>
                <span class="text-xs text-rose-800 font-semibold">Unit</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Roboh / struktur rusak</p>
        </div>

        <!-- Rusak Sedang -->
        <div class="p-4 rounded-2xl bg-white border border-amber-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 block">Rusak Sedang (RS)</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-amber-700">{{ number_format($totalRusakSedang) }}</span>
                <span class="text-xs text-amber-800 font-semibold">Unit</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Sebagian dinding/atap</p>
        </div>

        <!-- Rusak Ringan -->
        <div class="p-4 rounded-2xl bg-white border border-yellow-200/90 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-yellow-800 block">Rusak Ringan (RR)</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-yellow-700">{{ number_format($totalRusakRingan) }}</span>
                <span class="text-xs text-yellow-800 font-semibold">Unit</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Retak non-struktural</p>
        </div>

        <!-- Estimasi Kerugian -->
        <div class="p-4 rounded-2xl bg-white border border-blue-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800 block">Taksiran Kerugian</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-base font-black text-blue-700">Rp {{ number_format($totalEstimasiKerugian, 0, ',', '.') }}</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Taksiran kerugian fisik</p>
        </div>
    </div>

    <!-- 3. FILTER & PENCARIAN -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.kerusakan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Pencarian Teks -->
            <div class="lg:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari jenis kerusakan, lokasi, atau deskripsi..." 
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

            <!-- Filter Tingkat Kerusakan -->
            <div class="lg:col-span-2">
                <select name="tingkat" class="w-full py-2 px-3 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Tingkat</option>
                    <option value="Berat" {{ request('tingkat') == 'Berat' ? 'selected' : '' }}>Rusak Berat (RB)</option>
                    <option value="Sedang" {{ request('tingkat') == 'Sedang' ? 'selected' : '' }}>Rusak Sedang (RS)</option>
                    <option value="Ringan" {{ request('tingkat') == 'Ringan' ? 'selected' : '' }}>Rusak Ringan (RR)</option>
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

            <!-- Tombol Submit & Reset -->
            <div class="lg:col-span-1 flex items-center gap-1.5">
                <button type="submit" title="Terapkan Filter" class="w-full py-2 px-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold flex items-center justify-center transition">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'kecamatan_id', 'tingkat', 'bencana_id']))
                    <a href="{{ route('admin.kerusakan.index') }}" title="Reset Filter" class="p-2 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- 4. TABEL UTAMA DATA KERUSAKAN -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-sm text-slate-900">Rincian Kerusakan Fasilitas & Bangunan</h2>
            <span class="text-xs text-slate-500">Menampilkan <strong>{{ $kerusakanList->total() }}</strong> catatan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[200px]">Kejadian Bencana</th>
                        <th class="py-3 px-4 min-w-[170px]">Lokasi Wilayah</th>
                        <th class="py-3 px-4 min-w-[190px]">Objek & Jenis Kerusakan</th>
                        <th class="py-3 px-4 min-w-[140px]">Tingkat & Unit</th>
                        <th class="py-3 px-4 min-w-[150px]">Estimasi Kerugian</th>
                        <th class="py-3 px-4 min-w-[180px]">Deskripsi Detail</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kerusakanList as $d)
                    @php
                        $laporan = $d->laporanBencana;
                        $kelurahan = $d->kelurahan ?? ($laporan ? $laporan->kelurahan : null);
                        $kecamatan = $kelurahan ? $kelurahan->kecamatan : ($laporan ? $laporan->kecamatan : null);
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- ID -->
                        <td class="py-3 px-4 text-center text-slate-400 font-mono text-xs">
                            #{{ $d->id_dampak }}
                        </td>

                        <!-- Kejadian Bencana -->
                        <td class="py-3 px-4">
                            @if($laporan)
                                <div class="font-bold text-slate-900 leading-snug">
                                    {{ $laporan->judul }}
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 text-orange-800">
                                        {{ $laporan->bencana->nama_bencana ?? 'Bencana' }}
                                    </span>
                                    <span class="text-[11px] text-slate-500">
                                        {{ $laporan->created_at ? $laporan->created_at->format('d/m/Y') : '-' }}
                                    </span>
                                </div>
                            @else
                                <span class="font-medium text-slate-700 italic">Pencatatan Mandiri Wilayah</span>
                                <div class="text-[11px] text-slate-400 mt-0.5">Tidak terhubung ke aduan warga</div>
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
                        </td>

                        <!-- Objek & Jenis Kerusakan -->
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 text-xs sm:text-sm">
                                {{ $d->jenis_kerusakan }}
                            </div>
                        </td>

                        <!-- Tingkat & Unit -->
                        <td class="py-3 px-4">
                            <div>
                                @if($d->tingkat_kerusakan == 'Berat')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        ● Rusak Berat
                                    </span>
                                @elseif($d->tingkat_kerusakan == 'Sedang')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        ● Rusak Sedang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-yellow-100 text-yellow-900 border border-yellow-200">
                                        ● Rusak Ringan
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs font-bold text-slate-800 mt-1">
                                {{ number_format($d->jumlah_unit) }} Unit Bangunan
                            </div>
                        </td>

                        <!-- Estimasi Kerugian -->
                        <td class="py-3 px-4">
                            @if($d->estimasi_kerugian > 0)
                                <div class="font-extrabold text-slate-900 text-xs">
                                    Rp {{ number_format($d->estimasi_kerugian, 0, ',', '.') }}
                                </div>
                            @else
                                <span class="text-xs text-slate-400 italic">Dalam penaksiran</span>
                            @endif
                        </td>

                        <!-- Deskripsi Detail -->
                        <td class="py-3 px-4">
                            @if($d->deskripsi)
                                <p class="text-xs text-slate-700 line-clamp-2" title="{{ $d->deskripsi }}">
                                    {{ $d->deskripsi }}
                                </p>
                            @else
                                <span class="text-xs text-slate-400 italic">- Tidak ada deskripsi -</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center gap-1 justify-center">
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode([
                                            'id_dampak' => $d->id_dampak,
                                            'id_laporan' => $d->id_laporan,
                                            'id_kelurahan' => $d->id_kelurahan,
                                            'id_kecamatan' => $kelurahan ? $kelurahan->id_kecamatan : ($kecamatan ? $kecamatan->id_kecamatan : ''),
                                            'jenis_kerusakan' => $d->jenis_kerusakan,
                                            'tingkat_kerusakan' => $d->tingkat_kerusakan,
                                            'jumlah_unit' => $d->jumlah_unit,
                                            'estimasi_kerugian' => (float) $d->estimasi_kerugian,
                                            'deskripsi' => $d->deskripsi,
                                        ]) }})"
                                        title="Edit Kerusakan" 
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <button type="button" 
                                        onclick="confirmDelete({{ $d->id_dampak }})"
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
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <p class="font-bold text-slate-800 text-sm">Tidak ditemukan catatan kerusakan</p>
                            <p class="text-xs text-slate-500 mt-1">Sesuaikan filter atau catat data kerusakan fasilitas melalui tombol di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kerusakanList->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $kerusakanList->links() }}
        </div>
        @endif
    </div>

</div>

<!-- 5. MODAL FORM: TAMBAH DATA KERUSAKAN -->
<div id="modal-create" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Catat Kerusakan Bangunan/Infrastruktur</h3>
                <p class="text-xs text-slate-500">Inventarisasi objek terdampak, jumlah unit, dan estimasi kerugian.</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.kerusakan.store') }}" class="p-6 space-y-4">
            @csrf

            <!-- Tautkan Laporan Bencana (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tautkan ke Laporan Kejadian <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="id_laporan" id="create_id_laporan" onchange="autoFillLocation(this, 'create')" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    <option value="">-- Tanpa Tautan (Pencatatan Mandiri Wilayah) --</option>
                    @foreach($laporanOptions as $lp)
                        <option value="{{ $lp->id_laporan }}" data-kecamatan="{{ $lp->id_kecamatan }}" data-kelurahan="{{ $lp->id_kelurahan }}">
                            #{{ $lp->id_laporan }} - {{ $lp->judul }} ({{ $lp->kecamatan->nama_kecamatan ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Kecamatan & Kelurahan -->
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

            <!-- Objek & Jenis Kerusakan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Objek / Jenis Kerusakan <span class="text-rose-600">*</span>
                </label>
                <input type="text" name="jenis_kerusakan" required placeholder="Contoh: Rumah Tinggal, Jembatan Antar-Desa, Sekolah, Saluran Irigasi" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
            </div>

            <!-- Tingkat Kerusakan, Jumlah Unit & Estimasi Biaya -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tingkat Kerusakan <span class="text-rose-600">*</span>
                    </label>
                    <select name="tingkat_kerusakan" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500 font-semibold">
                        <option value="Berat">Rusak Berat (RB)</option>
                        <option value="Sedang">Rusak Sedang (RS)</option>
                        <option value="Ringan">Rusak Ringan (RR)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah Unit <span class="text-rose-600">*</span>
                    </label>
                    <input type="number" name="jumlah_unit" value="1" min="1" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Taksiran Kerugian (Rp)
                    </label>
                    <input type="number" name="estimasi_kerugian" value="0" min="0" step="1000" placeholder="0" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <!-- Deskripsi Rincian -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Kerusakan Fisik
                </label>
                <textarea name="deskripsi" rows="3" placeholder="Jelaskan kondisi kerusakan, misalnya: 2 rumah hanyut terbawa arus, akses jalan terputus 50 meter..." class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500"></textarea>
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                    Simpan Data Kerusakan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 6. MODAL FORM: EDIT DATA KERUSAKAN -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perbarui Data Kerusakan</h3>
                <p class="text-xs text-slate-500" id="edit_modal_subtitle">Edit data unit kerusakan fisik.</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="edit_form" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Tautkan Laporan Bencana (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tautkan ke Laporan Kejadian <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="id_laporan" id="edit_id_laporan" onchange="autoFillLocation(this, 'edit')" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    <option value="">-- Tanpa Tautan (Pencatatan Mandiri Wilayah) --</option>
                    @foreach($laporanOptions as $lp)
                        <option value="{{ $lp->id_laporan }}" data-kecamatan="{{ $lp->id_kecamatan }}" data-kelurahan="{{ $lp->id_kelurahan }}">
                            #{{ $lp->id_laporan }} - {{ $lp->judul }} ({{ $lp->kecamatan->nama_kecamatan ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Kecamatan & Kelurahan -->
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

            <!-- Objek & Jenis Kerusakan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Objek / Jenis Kerusakan <span class="text-rose-600">*</span>
                </label>
                <input type="text" name="jenis_kerusakan" id="edit_jenis_kerusakan" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
            </div>

            <!-- Tingkat Kerusakan, Jumlah Unit & Estimasi Biaya -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tingkat Kerusakan <span class="text-rose-600">*</span>
                    </label>
                    <select name="tingkat_kerusakan" id="edit_tingkat_kerusakan" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500 font-semibold">
                        <option value="Berat">Rusak Berat (RB)</option>
                        <option value="Sedang">Rusak Sedang (RS)</option>
                        <option value="Ringan">Rusak Ringan (RR)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah Unit <span class="text-rose-600">*</span>
                    </label>
                    <input type="number" name="jumlah_unit" id="edit_jumlah_unit" min="1" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Taksiran Kerugian (Rp)
                    </label>
                    <input type="number" name="estimasi_kerugian" id="edit_estimasi_kerugian" min="0" step="1000" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <!-- Deskripsi Rincian -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Kerusakan Fisik
                </label>
                <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500"></textarea>
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

    function openCreateModal() {
        document.getElementById('modal-create').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('modal-create').classList.add('hidden');
    }

    function openEditModal(data) {
        const form = document.getElementById('edit_form');
        form.action = `/admin/kerusakan/${data.id_dampak}`;

        document.getElementById('edit_modal_subtitle').textContent = `Perbarui rincian kerusakan #${data.id_dampak}`;
        document.getElementById('edit_id_laporan').value = data.id_laporan || '';
        
        if (data.id_kecamatan) {
            document.getElementById('edit_kecamatan').value = data.id_kecamatan;
            filterKelurahanDropdown('edit', data.id_kelurahan);
        } else {
            document.getElementById('edit_kecamatan').value = '';
            document.getElementById('edit_kelurahan').innerHTML = '<option value="">Pilih Kelurahan</option>';
        }

        document.getElementById('edit_jenis_kerusakan').value = data.jenis_kerusakan || '';
        document.getElementById('edit_tingkat_kerusakan').value = data.tingkat_kerusakan || 'Berat';
        document.getElementById('edit_jumlah_unit').value = data.jumlah_unit || 1;
        document.getElementById('edit_estimasi_kerugian').value = data.estimasi_kerugian || 0;
        document.getElementById('edit_deskripsi').value = data.deskripsi || '';

        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modal-edit').classList.add('hidden');
    }

    function confirmDelete(id) {
        if (confirm(`Yakin ingin menghapus data kerusakan #${id}? Data yang dihapus tidak dapat dipulihkan.`)) {
            const form = document.getElementById('delete_form');
            form.action = `/admin/kerusakan/${id}`;
            form.submit();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush
