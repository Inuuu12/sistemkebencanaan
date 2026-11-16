@extends('layouts.admin')

@section('title', 'Distribusi Bantuan Logistik - BPBD Kabupaten Bogor')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-400">Logistik & Bantuan</span>
                <span>/</span>
                <span class="text-[#ea5921]">Distribusi Bantuan</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Distribusi & Penyaluran Bantuan</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                Monitoring riwayat penyaluran logistik darurat ke posko pengungsian, desa terdampak, dan penerima manfaat di Kabupaten Bogor.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <button type="button" onclick="openCreateModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Catat Distribusi Baru</span>
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
            <span>Terdapat kendala pada data input distribusi:</span>
        </div>
        <ul class="list-disc pl-7 space-y-0.5 text-xs">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. REKAPITULASI METRIK DISTRIBUSI -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Penyaluran -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Penyaluran</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($totalDistribusi) }}</span>
                    <span class="text-xs text-slate-500 font-semibold">Tugas</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Misi bantuan logistik</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-orange-50 border border-orange-200/60 text-[#ea5921] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
        </div>

        <!-- Proses Kirim -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 block">Proses Pengiriman</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-amber-600">{{ number_format($totalProses) }}</span>
                    <span class="text-xs text-amber-800 font-semibold">Armada</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Dalam perjalanan TRC BPBD</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/60 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>

        <!-- Tiba di Posko -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800 block">Tiba di Posko / Lokasi</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-blue-600">{{ number_format($totalTiba) }}</span>
                    <span class="text-xs text-blue-800 font-semibold">Titik</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Sedang dibagikan ke warga</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200/60 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        <!-- Selesai Diterima -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block">Selesai / Diterima</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl sm:text-3xl font-black text-emerald-600">{{ number_format($totalTerkirim) }}</span>
                    <span class="text-xs text-emerald-800 font-semibold">Tersalur</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Berita acara serah terima valid</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/60 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- 3. FILTER, PENCARIAN & TOOLBAR -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
        <form action="{{ route('admin.logistik.distribusi') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Input Cari -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari penerima, lokasi desa, nama bantuan..." 
                           class="w-full pl-9 pr-3.5 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <!-- Filter Status -->
                <div class="w-full sm:w-44">
                    <select name="status" onchange="this.form.submit()"
                            class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="">Semua Status</option>
                        <option value="Proses Kirim" {{ request('status') == 'Proses Kirim' ? 'selected' : '' }}>Proses Kirim</option>
                        <option value="Tiba di Lokasi" {{ request('status') == 'Tiba di Lokasi' ? 'selected' : '' }}>Tiba di Lokasi</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai / Diterima</option>
                    </select>
                </div>

                <!-- Filter Kecamatan -->
                <div class="w-full sm:w-48">
                    <select name="kecamatan_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatanList as $kec)
                            <option value="{{ $kec->id_kecamatan }}" {{ request('kecamatan_id') == $kec->id_kecamatan ? 'selected' : '' }}>
                                {{ $kec->nama_kecamatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl bg-slate-800 text-white hover:bg-slate-900 transition cursor-pointer">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'status', 'kecamatan_id']))
                    <a href="{{ route('admin.logistik.distribusi') }}" 
                       class="px-3 py-2 text-xs sm:text-sm font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. TABEL DAFTAR DISTRIBUSI BANTUAN -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Riwayat Penyaluran Logistik Kedaruratan</h2>
                <p class="text-xs text-slate-500">Menampilkan {{ $distribusiList->total() }} tugas penyaluran bantuan.</p>
            </div>
            <a href="{{ route('admin.logistik.stok') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#ea5921] hover:underline">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Kelola Gudang & Stok</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[200px]">Kejadian & Wilayah Tujuan</th>
                        <th class="py-3 px-4 min-w-[180px]">Barang & Kuantitas</th>
                        <th class="py-3 px-4 min-w-[180px]">Penerima & PJ Lapangan</th>
                        <th class="py-3 px-4 min-w-[130px] text-center">Status Misi</th>
                        <th class="py-3 px-4 min-w-[130px]">Jadwal Kirim / Terima</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($distribusiList as $dist)
                    @php
                        $laporan = $dist->laporanBencana;
                        $kel = $dist->kelurahan ?? ($laporan ? $laporan->kelurahan : null);
                        $kec = $kel ? $kel->kecamatan : ($laporan ? $laporan->kecamatan : null);
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- ID -->
                        <td class="py-3 px-4 text-center text-slate-400 font-mono text-xs">
                            #{{ $dist->id_distribusi }}
                        </td>

                        <!-- Kejadian & Wilayah -->
                        <td class="py-3 px-4">
                            @if($laporan)
                                <div class="font-bold text-slate-900 hover:text-[#ea5921] transition-colors leading-snug">
                                    {{ $laporan->judul }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-800">
                                        {{ $laporan->bencana->nama_bencana ?? 'Bencana' }}
                                    </span>
                                    <span class="text-[11px] text-slate-500">
                                        {{ $kel ? $kel->nama_kelurahan : '-' }}, Kec. {{ $kec ? $kec->nama_kecamatan : '-' }}
                                    </span>
                                </div>
                            @else
                                <div class="font-bold text-slate-800">
                                    Distribusi Reguler / Posko
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $kel ? $kel->nama_kelurahan : '-' }}, Kec. {{ $kec ? $kec->nama_kecamatan : '-' }}
                                </div>
                            @endif
                        </td>

                        <!-- Barang & Kuantitas -->
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 block">
                                {{ $dist->stokBantuan ? $dist->stokBantuan->nama_item : 'Bantuan Campuran' }}
                            </span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs font-black text-[#ea5921]">
                                    {{ number_format($dist->jumlah) }} {{ $dist->satuan }}
                                </span>
                                @if($dist->stokBantuan && $dist->stokBantuan->kategoriStok)
                                    <span class="text-[10px] text-slate-400">
                                        ({{ $dist->stokBantuan->kategoriStok->nama_kategori }})
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Penerima & PJ -->
                        <td class="py-3 px-4">
                            <div class="font-semibold text-slate-800">
                                {{ $dist->penerima }}
                            </div>
                            @if($dist->keterangan)
                                <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1" title="{{ $dist->keterangan }}">
                                    {{ $dist->keterangan }}
                                </div>
                            @endif
                        </td>

                        <!-- Status Misi -->
                        <td class="py-3 px-4 text-center">
                            @if($dist->status === 'Selesai')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Selesai
                                </span>
                            @elseif($dist->status === 'Tiba di Lokasi')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    Tiba di Lokasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Proses Kirim
                                </span>
                            @endif
                        </td>

                        <!-- Tanggal Kirim / Terima -->
                        <td class="py-3 px-4">
                            <div class="text-xs text-slate-700">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Kirim:</span>
                                {{ $dist->tanggal_kirim ? \Carbon\Carbon::parse($dist->tanggal_kirim)->translatedFormat('d M Y') : '-' }}
                            </div>
                            @if($dist->tanggal_terima)
                                <div class="text-[11px] text-emerald-700 mt-0.5">
                                    <span class="text-[10px] font-bold text-emerald-500 uppercase">Diterima:</span>
                                    {{ \Carbon\Carbon::parse($dist->tanggal_terima)->translatedFormat('d M Y') }}
                                </div>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <!-- Tombol Edit -->
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode([
                                            'id_distribusi' => $dist->id_distribusi,
                                            'id_laporan' => $dist->id_laporan,
                                            'id_kelurahan' => $dist->id_kelurahan,
                                            'id_stok' => $dist->id_stok,
                                            'jumlah' => $dist->jumlah,
                                            'satuan' => $dist->satuan,
                                            'tanggal_kirim' => $dist->tanggal_kirim ? \Carbon\Carbon::parse($dist->tanggal_kirim)->format('Y-m-d') : '',
                                            'tanggal_terima' => $dist->tanggal_terima ? \Carbon\Carbon::parse($dist->tanggal_terima)->format('Y-m-d') : '',
                                            'status' => $dist->status,
                                            'penerima' => $dist->penerima,
                                            'keterangan' => $dist->keterangan,
                                        ]) }})"
                                        title="Edit Data Distribusi" 
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <!-- Tombol Hapus -->
                                <button type="button" 
                                        onclick="confirmDelete({{ $dist->id_distribusi }}, '{{ addslashes($dist->penerima) }}')"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <p class="font-bold text-slate-800 text-sm">Belum ada riwayat distribusi logistik</p>
                            <p class="text-xs text-slate-500 mt-1">Gunakan tombol "Catat Distribusi Baru" untuk memulai penugasan penyaluran bantuan.</p>
                            @if(request()->hasAny(['q', 'status', 'kecamatan_id']))
                                <a href="{{ route('admin.logistik.distribusi') }}" class="inline-block mt-3 px-3 py-1.5 text-xs font-semibold text-[#ea5921] bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                                    Bersihkan Filter
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($distribusiList->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $distribusiList->links() }}
        </div>
        @endif
    </div>

</div>

<!-- 5. MODAL FORM: TAMBAH DISTRIBUSI BANTUAN -->
<div id="modal-create" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Catat Penyaluran Bantuan Baru</h3>
                <p class="text-xs text-slate-500">Kirim logistik darurat ke posko atau wilayah terdampak.</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.logistik.distribusi.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <!-- Tautkan ke Laporan Kejadian (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tautkan ke Laporan Kejadian Bencana (Opsional)
                </label>
                <select name="id_laporan" 
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                    <option value="">-- Distribusi Mandiri / Tanpa Tautan Aduan --</option>
                    @foreach($laporanList as $lap)
                        <option value="{{ $lap->id_laporan }}">
                            #{{ $lap->id_laporan }} - {{ $lap->judul }} ({{ $lap->bencana->nama_bencana ?? 'Bencana' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kelurahan Tujuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kelurahan / Desa Tujuan
                    </label>
                    <select name="id_kelurahan" 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="">Pilih Kelurahan Tujuan</option>
                        @foreach($kelurahanList as $kel)
                            <option value="{{ $kel->id_kelurahan }}">{{ $kel->nama_kelurahan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ambil dari Stok Gudang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Item Stok Logistik
                    </label>
                    <select name="id_stok" id="create_id_stok" onchange="autoFillSatuan(this)"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="">Pilih Item dari Gudang</option>
                        @foreach($stokList as $st)
                            <option value="{{ $st->id_stok }}" data-satuan="{{ $st->satuan }}">
                                {{ $st->nama_item }} ({{ $st->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Jumlah -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah Penyaluran <span class="text-rose-600">*</span>
                    </label>
                    <input type="number" name="jumlah" min="1" value="1" required 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <!-- Satuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Satuan <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" id="create_satuan" name="satuan" required placeholder="Dus, Paket, Lembar, Box" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Tanggal Kirim -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Kirim <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" name="tanggal_kirim" value="{{ date('Y-m-d') }}" required 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <!-- Tanggal Terima -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Diterima
                    </label>
                    <input type="date" name="tanggal_terima" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Penyaluran <span class="text-rose-600">*</span>
                    </label>
                    <select name="status" required 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="Proses Kirim">Proses Kirim</option>
                        <option value="Tiba di Lokasi">Tiba di Lokasi</option>
                        <option value="Selesai">Selesai / Diterima</option>
                    </select>
                </div>
            </div>

            <!-- Penerima / PJ -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Penerima / PJ Posko Lapangan <span class="text-rose-600">*</span>
                </label>
                <input type="text" name="penerima" required placeholder="Contoh: Pak RT 02 / Koordinator Posko Balai Desa" 
                       class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
            </div>

            <!-- Keterangan Tambahan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan / Armada Pengangkut
                </label>
                <textarea name="keterangan" rows="2" placeholder="Nomor polisi armada, nomor telepon PJ posko, rincian serah terima..." 
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
                    Simpan Data Distribusi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 6. MODAL FORM: EDIT DISTRIBUSI BANTUAN -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Edit Data Penyaluran Bantuan</h3>
                <p class="text-xs text-slate-500">Perbarui status atau rincian distribusi bantuan.</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-edit-distribusi" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tautkan ke Laporan Kejadian Bencana (Opsional)
                </label>
                <select id="edit_id_laporan" name="id_laporan" 
                        class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                    <option value="">-- Distribusi Mandiri / Tanpa Tautan Aduan --</option>
                    @foreach($laporanList as $lap)
                        <option value="{{ $lap->id_laporan }}">
                            #{{ $lap->id_laporan }} - {{ $lap->judul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kelurahan / Desa Tujuan
                    </label>
                    <select id="edit_id_kelurahan" name="id_kelurahan" 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="">Pilih Kelurahan Tujuan</option>
                        @foreach($kelurahanList as $kel)
                            <option value="{{ $kel->id_kelurahan }}">{{ $kel->nama_kelurahan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Item Stok Logistik
                    </label>
                    <select id="edit_id_stok" name="id_stok" 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="">Pilih Item dari Gudang</option>
                        @foreach($stokList as $st)
                            <option value="{{ $st->id_stok }}">{{ $st->nama_item }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah Penyaluran <span class="text-rose-600">*</span>
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
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Kirim <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" id="edit_tanggal_kirim" name="tanggal_kirim" required 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Diterima
                    </label>
                    <input type="date" id="edit_tanggal_terima" name="tanggal_terima" 
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Penyaluran <span class="text-rose-600">*</span>
                    </label>
                    <select id="edit_status" name="status" required 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
                        <option value="Proses Kirim">Proses Kirim</option>
                        <option value="Tiba di Lokasi">Tiba di Lokasi</option>
                        <option value="Selesai">Selesai / Diterima</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Penerima / PJ Posko Lapangan <span class="text-rose-600">*</span>
                </label>
                <input type="text" id="edit_penerima" name="penerima" required 
                       class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#ea5921] transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan / Armada Pengangkut
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
            <h3 class="text-base font-bold text-slate-900">Konfirmasi Hapus Distribusi</h3>
            <p id="delete-message" class="text-xs text-slate-600 mt-1 leading-relaxed">
                Apakah Anda yakin ingin menghapus data distribusi logistik ini?
            </p>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-center gap-2.5">
            <button type="button" onclick="closeDeleteModal()" 
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-100 transition cursor-pointer">
                Batal
            </button>
            <form id="form-delete-distribusi" method="POST" class="inline">
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
    function autoFillSatuan(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        const satuan = opt.getAttribute('data-satuan');
        const inputSatuan = document.getElementById('create_satuan');
        if (satuan && inputSatuan) {
            inputSatuan.value = satuan;
        }
    }

    function openCreateModal() {
        document.getElementById('modal-create').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeCreateModal() {
        document.getElementById('modal-create').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openEditModal(data) {
        const form = document.getElementById('form-edit-distribusi');
        form.action = "{{ url('admin/logistik/distribusi') }}/" + data.id_distribusi;

        document.getElementById('edit_id_laporan').value = data.id_laporan || '';
        document.getElementById('edit_id_kelurahan').value = data.id_kelurahan || '';
        document.getElementById('edit_id_stok').value = data.id_stok || '';
        document.getElementById('edit_jumlah').value = data.jumlah || 1;
        document.getElementById('edit_satuan').value = data.satuan || '';
        document.getElementById('edit_tanggal_kirim').value = data.tanggal_kirim || '';
        document.getElementById('edit_tanggal_terima').value = data.tanggal_terima || '';
        document.getElementById('edit_status').value = data.status || 'Proses Kirim';
        document.getElementById('edit_penerima').value = data.penerima || '';
        document.getElementById('edit_keterangan').value = data.keterangan || '';

        document.getElementById('modal-edit').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditModal() {
        document.getElementById('modal-edit').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function confirmDelete(id, penerima) {
        const form = document.getElementById('form-delete-distribusi');
        form.action = "{{ url('admin/logistik/distribusi') }}/" + id;

        document.getElementById('delete-message').innerHTML = `Apakah Anda yakin ingin menghapus data penyaluran ke <strong>"${penerima}"</strong>? Tindakan ini tidak dapat dibatalkan.`;

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

