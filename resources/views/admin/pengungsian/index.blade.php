@extends('layouts.admin')

@section('title', 'Data Pengungsian & Posko Evakuasi')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-[#ea5921]">Data Pengungsian</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Data Pengungsian & Posko Evakuasi</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                Pemantauan daya tampung, logistik hunian, dan sebaran warga mengungsi di titik posko darurat Kabupaten Bogor.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <button type="button" onclick="openCreateModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Posko Pengungsian</span>
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
            <span>Periksa kembali data form pengungsian:</span>
        </div>
        <ul class="list-disc pl-7 space-y-0.5 text-xs">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. REKAPITULASI METRIK PENGUNGSIAN -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total Titik Posko -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Posko</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalPosko) }}</span>
                <span class="text-xs text-slate-500">Titik</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Gedung / Aula / Tenda</p>
        </div>

        <!-- Posko Aktif -->
        <div class="p-4 rounded-2xl bg-white border border-emerald-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block">Posko Aktif</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-emerald-700">{{ number_format($poskoAktif) }}</span>
                <span class="text-xs text-emerald-800 font-semibold">Aktif</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Sedang menampung warga</p>
        </div>

        <!-- Total Jiwa Mengungsi -->
        <div class="p-4 rounded-2xl bg-white border border-blue-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800 block">Jiwa Mengungsi</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-blue-700">{{ number_format($totalPengungsi) }}</span>
                <span class="text-xs text-blue-800 font-semibold">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Dalam naungan posko</p>
        </div>

        <!-- Total Daya Tampung -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Kapasitas Total</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalKapasitas) }}</span>
                <span class="text-xs text-slate-500">Jiwa</span>
            </div>
            @php
                $persenIsi = $totalKapasitas > 0 ? round(($totalPengungsi / $totalKapasitas) * 100) : 0;
            @endphp
            <p class="text-[11px] font-medium text-slate-600 mt-1">Keterisian: {{ $persenIsi }}%</p>
        </div>

        <!-- Balita & Anak -->
        <div class="p-4 rounded-2xl bg-white border border-amber-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 block">Kelompok Balita</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-amber-700">{{ number_format($totalBalita) }}</span>
                <span class="text-xs text-amber-800 font-semibold">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Prioritas susu & nutrisi</p>
        </div>

        <!-- Lansia -->
        <div class="p-4 rounded-2xl bg-white border border-purple-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-800 block">Kelompok Lansia</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-purple-700">{{ number_format($totalLansia) }}</span>
                <span class="text-xs text-purple-800 font-semibold">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Prioritas medis rutin</p>
        </div>
    </div>

    <!-- 3. FILTER & PENCARIAN -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.pengungsian.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Pencarian Teks -->
            <div class="lg:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama posko, alamat, atau penanggung jawab..." 
                       class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <!-- Filter Kecamatan -->
            <div class="lg:col-span-4">
                <select name="kecamatan_id" class="w-full py-2 px-3 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Kecamatan</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id_kecamatan }}" {{ request('kecamatan_id') == $d->id_kecamatan ? 'selected' : '' }}>
                            Kec. {{ $d->nama_kecamatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Posko -->
            <div class="lg:col-span-2">
                <select name="status" class="w-full py-2 px-3 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Penuh" {{ request('status') == 'Penuh' ? 'selected' : '' }}>Penuh</option>
                    <option value="Standby" {{ request('status') == 'Standby' ? 'selected' : '' }}>Standby / Siaga</option>
                    <option value="Ditutup" {{ request('status') == 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="lg:col-span-1 flex items-center gap-1.5">
                <button type="submit" title="Terapkan Filter" class="w-full py-2 px-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold flex items-center justify-center transition">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'kecamatan_id', 'status']))
                    <a href="{{ route('admin.pengungsian.index') }}" title="Reset Filter" class="p-2 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- 4. TABEL UTAMA DATA POSKO PENGUNGSIAN -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-sm text-slate-900">Daftar Titik Posko & Pengungsian Darurat</h2>
            <span class="text-xs text-slate-500">Total: <strong>{{ $poskoList->total() }}</strong> titik</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[220px]">Nama Posko & Jenis</th>
                        <th class="py-3 px-4 min-w-[190px]">Lokasi Wilayah</th>
                        <th class="py-3 px-4 min-w-[180px]">Keterisian Kapasitas</th>
                        <th class="py-3 px-4 min-w-[150px]">Kelompok Rentan</th>
                        <th class="py-3 px-4 min-w-[200px]">Penanggung Jawab & Fasilitas</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($poskoList as $p)
                    @php
                        $persen = $p->kapasitas_maksimal > 0 ? round(($p->jumlah_pengungsi / $p->kapasitas_maksimal) * 100) : 0;
                        $colorClass = $persen >= 90 ? 'bg-rose-500' : ($persen >= 60 ? 'bg-amber-500' : 'bg-emerald-500');
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- ID -->
                        <td class="py-3 px-4 text-center text-slate-400 font-mono text-xs">
                            #{{ $p->id_posko }}
                        </td>

                        <!-- Nama Posko & Jenis -->
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 leading-snug">
                                {{ $p->nama_posko }}
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $p->jenis_posko }}
                                </span>
                                @if($p->status == 'Aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        ● Aktif
                                    </span>
                                @elseif($p->status == 'Penuh')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800">
                                        ● Penuh
                                    </span>
                                @elseif($p->status == 'Standby')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">
                                        ● Siaga
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500">
                                        Ditutup
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Lokasi Wilayah -->
                        <td class="py-3 px-4">
                            <div class="font-semibold text-slate-800">
                                Kec. {{ $p->kecamatan->nama_kecamatan ?? '-' }}
                            </div>
                            @if($p->kelurahan)
                                <div class="text-xs text-slate-500">
                                    Kel. {{ $p->kelurahan->nama_kelurahan }}
                                </div>
                            @endif
                            @if($p->alamat_lengkap)
                                <div class="text-[11px] text-slate-400 mt-1 line-clamp-2" title="{{ $p->alamat_lengkap }}">
                                    {{ $p->alamat_lengkap }}
                                </div>
                            @endif
                        </td>

                        <!-- Keterisian Kapasitas -->
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-900">{{ number_format($p->jumlah_pengungsi) }} Jiwa</span>
                                <span class="text-slate-500 text-[11px]">Kapasitas: {{ number_format($p->kapasitas_maksimal) }}</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full {{ $colorClass }} rounded-full" style="width: {{ min(100, $persen) }}%;"></div>
                            </div>
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Keterisian: {{ $persen }}%</span>
                        </td>

                        <!-- Kelompok Rentan -->
                        <td class="py-3 px-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span class="text-slate-600">Balita:</span>
                                    <strong class="text-slate-900">{{ $p->jumlah_balita }} Jiwa</strong>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    <span class="text-slate-600">Lansia:</span>
                                    <strong class="text-slate-900">{{ $p->jumlah_lansia }} Jiwa</strong>
                                </div>
                            </div>
                        </td>

                        <!-- Penanggung Jawab & Fasilitas -->
                        <td class="py-3 px-4">
                            @if($p->pj_nama)
                                <div class="font-medium text-slate-900 text-xs">
                                    {{ $p->pj_nama }}
                                </div>
                            @endif
                            @if($p->pj_kontak)
                                <div class="text-[11px] text-[#ea5921] font-semibold mt-0.5">
                                    📞 {{ $p->pj_kontak }}
                                </div>
                            @endif
                            @if($p->fasilitas)
                                <div class="text-[11px] text-slate-500 mt-1 line-clamp-1" title="{{ $p->fasilitas }}">
                                    Fasilitas: {{ $p->fasilitas }}
                                </div>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center gap-1 justify-center">
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode($p) }})"
                                        title="Edit Posko" 
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <button type="button" 
                                        onclick="confirmDelete({{ $p->id_posko }})"
                                        title="Hapus Posko" 
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
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <p class="font-bold text-slate-800 text-sm">Belum ada titik posko pengungsian</p>
                            <p class="text-xs text-slate-500 mt-1">Tambahkan data posko melalui tombol di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($poskoList->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $poskoList->links() }}
        </div>
        @endif
    </div>

</div>

<!-- 5. MODAL FORM: TAMBAH POSKO PENGUNGSIAN -->
<div id="modal-create" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tambah Titik Posko Pengungsian</h3>
                <p class="text-xs text-slate-500">Catat lokasi penampungan evakuasi warga terdampak bencana.</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.pengungsian.store') }}" class="p-6 space-y-4">
            @csrf

            <!-- Nama & Jenis Posko -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Posko <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="nama_posko" required placeholder="Contoh: GOR Cibinong / SDN 01" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jenis Posko <span class="text-rose-600">*</span>
                    </label>
                    <select name="jenis_posko" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="Posko Pengungsian & Dapur Umum">Posko Pengungsian & Dapur Umum</option>
                        <option value="Posko Evakuasi Darurat">Posko Evakuasi Darurat</option>
                        <option value="Posko Medis & Pengungsian">Posko Medis & Pengungsian</option>
                        <option value="Balai Warga / Shelter Sementara">Balai Warga / Shelter Sementara</option>
                    </select>
                </div>
            </div>

            <!-- Tautkan Laporan Bencana (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tautkan ke Laporan Bencana <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="id_laporan" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    <option value="">-- Tanpa Tautan Kejadian Spesifik --</option>
                    @foreach($laporanOptions as $lp)
                        <option value="{{ $lp->id_laporan }}">#{{ $lp->id_laporan }} - {{ $lp->judul }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kecamatan & Kelurahan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kecamatan <span class="text-rose-600">*</span>
                    </label>
                    <select name="id_kecamatan" id="create_kecamatan" onchange="filterKelurahanDropdown('create')" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kecamatan</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id_kecamatan }}">{{ $d->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kelurahan / Desa
                    </label>
                    <select name="id_kelurahan" id="create_kelurahan" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kelurahan</option>
                    </select>
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alamat Lengkap Titik Posko <span class="text-rose-600">*</span>
                </label>
                <textarea name="alamat_lengkap" rows="2" required placeholder="Nama jalan, patokan gedung, dsb..." class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500"></textarea>
            </div>

            <!-- Grid Angka Kapasitas & Pengungsi -->
            <div class="pt-2 border-t border-slate-100">
                <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kapasitas & Jumlah Jiwa
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Daya Tampung</label>
                        <input type="number" name="kapasitas_maksimal" value="100" min="1" required class="w-full text-xs sm:text-sm py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-blue-800 uppercase mb-1">Total Jiwa</label>
                        <input type="number" name="jumlah_pengungsi" value="0" min="0" required class="w-full text-xs sm:text-sm py-1.5 px-2.5 bg-blue-50/50 border border-blue-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 font-bold text-blue-950">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-amber-800 uppercase mb-1">Balita</label>
                        <input type="number" name="jumlah_balita" value="0" min="0" class="w-full text-xs sm:text-sm py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-purple-800 uppercase mb-1">Lansia</label>
                        <input type="number" name="jumlah_lansia" value="0" min="0" class="w-full text-xs sm:text-sm py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
            </div>

            <!-- Status & Kontak PJ -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Posko <span class="text-rose-600">*</span>
                    </label>
                    <select name="status" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500 font-semibold">
                        <option value="Aktif">Aktif</option>
                        <option value="Penuh">Penuh</option>
                        <option value="Standby">Standby</option>
                        <option value="Ditutup">Ditutup</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Penanggung Jawab
                    </label>
                    <input type="text" name="pj_nama" placeholder="Contoh: Bpk. Budi (Tagana)" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kontak / Telepon PJ
                    </label>
                    <input type="text" name="pj_kontak" placeholder="0812-xxxx-xxxx" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <!-- Fasilitas -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Fasilitas Tersedia
                </label>
                <input type="text" name="fasilitas" placeholder="Contoh: Dapur Umum, Tenda Peleton, MCK Darurat, Posko Kesehatan" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
            </div>

            <!-- Keterangan Tambahan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan Tambahan
                </label>
                <textarea name="keterangan" rows="2" placeholder="Catatan logistik atau kondisi darurat di lokasi posko..." class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500"></textarea>
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                    Simpan Titik Posko
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 6. MODAL FORM: EDIT POSKO PENGUNGSIAN -->
<div id="modal-edit" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full overflow-hidden shadow-2xl border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perbarui Data Posko Pengungsian</h3>
                <p class="text-xs text-slate-500" id="edit_modal_subtitle">Edit data titik penampungan.</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="edit_form" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Nama & Jenis Posko -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Posko <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="nama_posko" id="edit_nama_posko" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jenis Posko <span class="text-rose-600">*</span>
                    </label>
                    <select name="jenis_posko" id="edit_jenis_posko" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="Posko Pengungsian & Dapur Umum">Posko Pengungsian & Dapur Umum</option>
                        <option value="Posko Evakuasi Darurat">Posko Evakuasi Darurat</option>
                        <option value="Posko Medis & Pengungsian">Posko Medis & Pengungsian</option>
                        <option value="Balai Warga / Shelter Sementara">Balai Warga / Shelter Sementara</option>
                    </select>
                </div>
            </div>

            <!-- Tautkan Laporan Bencana (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tautkan ke Laporan Bencana <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="id_laporan" id="edit_id_laporan" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    <option value="">-- Tanpa Tautan Kejadian Spesifik --</option>
                    @foreach($laporanOptions as $lp)
                        <option value="{{ $lp->id_laporan }}">#{{ $lp->id_laporan }} - {{ $lp->judul }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kecamatan & Kelurahan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kecamatan <span class="text-rose-600">*</span>
                    </label>
                    <select name="id_kecamatan" id="edit_kecamatan" onchange="filterKelurahanDropdown('edit')" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kecamatan</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id_kecamatan }}">{{ $d->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kelurahan / Desa
                    </label>
                    <select name="id_kelurahan" id="edit_kelurahan" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih Kelurahan</option>
                    </select>
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alamat Lengkap Titik Posko <span class="text-rose-600">*</span>
                </label>
                <textarea name="alamat_lengkap" id="edit_alamat_lengkap" rows="2" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500"></textarea>
            </div>

            <!-- Grid Angka Kapasitas & Pengungsi -->
            <div class="pt-2 border-t border-slate-100">
                <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kapasitas & Jumlah Jiwa
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Daya Tampung</label>
                        <input type="number" name="kapasitas_maksimal" id="edit_kapasitas_maksimal" min="1" required class="w-full text-xs sm:text-sm py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-blue-800 uppercase mb-1">Total Jiwa</label>
                        <input type="number" name="jumlah_pengungsi" id="edit_jumlah_pengungsi" min="0" required class="w-full text-xs sm:text-sm py-1.5 px-2.5 bg-blue-50/50 border border-blue-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 font-bold text-blue-950">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-amber-800 uppercase mb-1">Balita</label>
                        <input type="number" name="jumlah_balita" id="edit_jumlah_balita" min="0" class="w-full text-xs sm:text-sm py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-purple-800 uppercase mb-1">Lansia</label>
                        <input type="number" name="jumlah_lansia" id="edit_jumlah_lansia" min="0" class="w-full text-xs sm:text-sm py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
            </div>

            <!-- Status & Kontak PJ -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Posko <span class="text-rose-600">*</span>
                    </label>
                    <select name="status" id="edit_status" required class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500 font-semibold">
                        <option value="Aktif">Aktif</option>
                        <option value="Penuh">Penuh</option>
                        <option value="Standby">Standby</option>
                        <option value="Ditutup">Ditutup</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Penanggung Jawab
                    </label>
                    <input type="text" name="pj_nama" id="edit_pj_nama" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kontak / Telepon PJ
                    </label>
                    <input type="text" name="pj_kontak" id="edit_pj_kontak" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <!-- Fasilitas -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Fasilitas Tersedia
                </label>
                <input type="text" name="fasilitas" id="edit_fasilitas" class="w-full text-xs sm:text-sm py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-orange-500">
            </div>

            <!-- Keterangan Tambahan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan Tambahan
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

    function openCreateModal() {
        document.getElementById('modal-create').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('modal-create').classList.add('hidden');
    }

    function openEditModal(data) {
        const form = document.getElementById('edit_form');
        form.action = `/admin/pengungsian/${data.id_posko}`;

        document.getElementById('edit_modal_subtitle').textContent = `Perbarui rincian posko #${data.id_posko} (${data.nama_posko})`;
        document.getElementById('edit_nama_posko').value = data.nama_posko || '';
        document.getElementById('edit_jenis_posko').value = data.jenis_posko || 'Posko Pengungsian & Dapur Umum';
        document.getElementById('edit_id_laporan').value = data.id_laporan || '';
        
        document.getElementById('edit_kecamatan').value = data.id_kecamatan || '';
        filterKelurahanDropdown('edit', data.id_kelurahan);

        document.getElementById('edit_alamat_lengkap').value = data.alamat_lengkap || '';
        document.getElementById('edit_kapasitas_maksimal').value = data.kapasitas_maksimal || 100;
        document.getElementById('edit_jumlah_pengungsi').value = data.jumlah_pengungsi || 0;
        document.getElementById('edit_jumlah_balita').value = data.jumlah_balita || 0;
        document.getElementById('edit_jumlah_lansia').value = data.jumlah_lansia || 0;
        document.getElementById('edit_status').value = data.status || 'Aktif';
        document.getElementById('edit_pj_nama').value = data.pj_nama || '';
        document.getElementById('edit_pj_kontak').value = data.pj_kontak || '';
        document.getElementById('edit_fasilitas').value = data.fasilitas || '';
        document.getElementById('edit_keterangan').value = data.keterangan || '';

        document.getElementById('modal-edit').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modal-edit').classList.add('hidden');
    }

    function confirmDelete(id) {
        if (confirm(`Yakin ingin menghapus data posko #${id}? Data yang dihapus tidak dapat dipulihkan.`)) {
            const form = document.getElementById('delete_form');
            form.action = `/admin/pengungsian/${id}`;
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
