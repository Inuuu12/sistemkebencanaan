@extends('layouts.admin')

@section('title', 'Zonasi Titik Rawan Bencana - GIS Layer')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Zonasi Titik Rawan Bencana</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Layer pemetaan spasial wilayah berpotensi rawan banjir, longsor, dan bahaya lainnya di Kabupaten Bogor.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <button type="button" onclick="openCreateModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Titik Rawan</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi Flash Message -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between animate-in fade-in duration-200">
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
            <span>Terdapat kendala:</span>
        </div>
        <ul class="list-disc pl-7 space-y-0.5 text-xs">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. REKAPITULASI METRIK ZONASI -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <!-- Total Titik -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Titik Rawan</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalTitik) }}</span>
                <span class="text-xs text-slate-500 font-semibold">Titik Terpetakan</span>
            </div>
        </div>

        <!-- Zona Tinggi (Merah) -->
        <div class="p-4 rounded-2xl bg-white border border-rose-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-800 block">Zona Bahaya Tinggi</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-rose-600">{{ number_format($zonaTinggi) }}</span>
                <span class="text-xs text-rose-800 font-semibold">Titik Merah</span>
            </div>
        </div>

        <!-- Zona Sedang (Kuning) -->
        <div class="p-4 rounded-2xl bg-white border border-amber-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 block">Zona Waspada Sedang</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-amber-700">{{ number_format($zonaSedang) }}</span>
                <span class="text-xs text-amber-800 font-semibold">Titik Kuning</span>
            </div>
        </div>

        <!-- Zona Rendah (Hijau) -->
        <div class="p-4 rounded-2xl bg-white border border-emerald-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block">Zona Risiko Rendah</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-emerald-600">{{ number_format($zonaRendah) }}</span>
                <span class="text-xs text-emerald-800 font-semibold">Titik Hijau</span>
            </div>
        </div>
    </div>

    <!-- 3. FILTER & PENCARIAN -->
    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.rawan.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari keterangan, kelurahan, atau kecamatan..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#ea5921] focus:border-transparent">
            </div>

            <div class="w-full sm:w-48">
                <select name="bencana_id" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#ea5921] focus:border-transparent">
                    <option value="">Semua Potensi Bencana</option>
                    @foreach($bencanaList as $b)
                        <option value="{{ $b->id_bencana }}" {{ request('bencana_id') == $b->id_bencana ? 'selected' : '' }}>
                            {{ $b->nama_bencana }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-40">
                <select name="zona" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#ea5921] focus:border-transparent">
                    <option value="">Semua Zona</option>
                    <option value="Tinggi" {{ request('zona') == 'Tinggi' ? 'selected' : '' }}>Tinggi (Merah)</option>
                    <option value="Sedang" {{ request('zona') == 'Sedang' ? 'selected' : '' }}>Sedang (Kuning)</option>
                    <option value="Rendah" {{ request('zona') == 'Rendah' ? 'selected' : '' }}>Rendah (Hijau)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white rounded-xl shadow-xs transition-all">
                    Filter
                </button>
                @if(request('q') || request('bencana_id') || request('zona'))
                <a href="{{ route('admin.rawan.index') }}" class="px-3.5 py-2 text-xs font-semibold border border-slate-300 text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. TABEL TITIK RAWAN BENCANA -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Potensi Bencana</th>
                        <th class="py-3 px-4">Lokasi Wilayah</th>
                        <th class="py-3 px-4 text-center">Tingkat Zona</th>
                        <th class="py-3 px-4">Koordinat & Radius</th>
                        <th class="py-3 px-4">Keterangan Wilayah</th>
                        <th class="py-3 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rawanList as $index => $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 px-4 text-center font-bold text-slate-400">
                            {{ $rawanList->firstItem() + $index }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-extrabold text-slate-900 text-xs sm:text-sm block">
                                {{ $item->bencana->nama_bencana ?? 'Bencana Umum' }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">ID: #RAWAN-{{ $item->id_daerah_rawan }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-800 block">
                                {{ $item->kelurahan->nama_kelurahan ?? '-' }}
                            </span>
                            <span class="text-[11px] text-slate-500">
                                Kec. {{ $item->kelurahan->kecamatan->nama_kecamatan ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if(in_array($item->zona, ['Tinggi', 'Merah']))
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-100 text-rose-700 inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-rose-600"></span> Zona Tinggi
                                </span>
                            @elseif(in_array($item->zona, ['Sedang', 'Kuning']))
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Zona Sedang
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Zona Rendah
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($item->latitude && $item->longitude)
                                <span class="font-mono text-[11px] text-slate-700 block font-semibold">
                                    {{ $item->latitude }}, {{ $item->longitude }}
                                </span>
                                <span class="text-[10px] text-slate-400">Radius dampak: ±{{ $item->radius_meter ?: 100 }} m</span>
                            @else
                                <span class="text-slate-400 text-[11px] italic">Koordinat belum diplot</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 max-w-xs">
                            <p class="text-slate-600 text-xs line-clamp-2">
                                {{ $item->keterangan ?: '-' }}
                            </p>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Tombol Edit -->
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode([
                                            'id' => $item->id_daerah_rawan,
                                            'id_bencana' => $item->id_bencana,
                                            'id_kelurahan' => $item->id_kelurahan,
                                            'zona' => $item->zona,
                                            'keterangan' => $item->keterangan,
                                            'latitude' => $item->latitude,
                                            'longitude' => $item->longitude,
                                            'radius_meter' => $item->radius_meter,
                                            'is_active' => $item->is_active,
                                        ]) }})"
                                        class="p-1.5 rounded-lg border border-slate-200 hover:border-slate-300 text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.rawan.destroy', $item->id_daerah_rawan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus titik rawan ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-1.5 rounded-lg border border-red-200 hover:border-red-300 text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <span class="font-bold text-xs">Belum ada titik rawan bencana yang tercatat.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rawanList->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $rawanList->links() }}
        </div>
        @endif
    </div>

</div>

<!-- MODAL TAMBAH TITIK RAWAN -->
<div id="create-modal" class="hidden fixed inset-0 z-[600] bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-slate-900">Tambah Titik Zonasi Rawan Bencana</h3>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.rawan.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Potensi Bencana <span class="text-red-500">*</span>
                    </label>
                    <select name="id_bencana" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#ea5921]">
                        <option value="">-- Pilih Bencana --</option>
                        @foreach($bencanaList as $b)
                            <option value="{{ $b->id_bencana }}">{{ $b->nama_bencana }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tingkat Zonasi <span class="text-red-500">*</span>
                    </label>
                    <select name="zona" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#ea5921]">
                        <option value="Tinggi">Tinggi (Merah - Sangat Rawan)</option>
                        <option value="Sedang">Sedang (Kuning - Waspada)</option>
                        <option value="Rendah">Rendah (Hijau - Kurang Rawan)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Kelurahan / Desa <span class="text-red-500">*</span>
                </label>
                <select name="id_kelurahan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#ea5921]">
                    <option value="">-- Pilih Kelurahan / Desa --</option>
                    @foreach($kelurahanList as $kel)
                        <option value="{{ $kel->id_kelurahan }}">{{ $kel->nama_kelurahan }} (Kec. {{ $kel->kecamatan->nama_kecamatan ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">Latitude</label>
                    <input type="text" name="latitude" placeholder="-6.48" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">Longitude</label>
                    <input type="text" name="longitude" placeholder="106.84" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">Radius (Meter)</label>
                    <input type="number" name="radius_meter" value="250" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Wilayah / Potensi Bahaya</label>
                <textarea name="keterangan" rows="2" placeholder="Contoh: Lereng bukit rawan pergeseran tanah saat hujan deras lebih dari 3 jam..."
                          class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#ea5921]"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold bg-[#ea5921] hover:bg-[#d94812] text-white rounded-xl shadow-sm">
                    Simpan Titik Rawan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT TITIK RAWAN -->
<div id="edit-modal" class="hidden fixed inset-0 z-[600] bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-slate-900">Perbarui Titik Zonasi Rawan</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Potensi Bencana <span class="text-red-500">*</span>
                    </label>
                    <select id="edit-bencana" name="id_bencana" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#ea5921]">
                        <option value="">-- Pilih Bencana --</option>
                        @foreach($bencanaList as $b)
                            <option value="{{ $b->id_bencana }}">{{ $b->nama_bencana }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tingkat Zonasi <span class="text-red-500">*</span>
                    </label>
                    <select id="edit-zona" name="zona" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#ea5921]">
                        <option value="Tinggi">Tinggi (Merah - Sangat Rawan)</option>
                        <option value="Sedang">Sedang (Kuning - Waspada)</option>
                        <option value="Rendah">Rendah (Hijau - Kurang Rawan)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Kelurahan / Desa <span class="text-red-500">*</span>
                </label>
                <select id="edit-kelurahan" name="id_kelurahan" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#ea5921]">
                    <option value="">-- Pilih Kelurahan / Desa --</option>
                    @foreach($kelurahanList as $kel)
                        <option value="{{ $kel->id_kelurahan }}">{{ $kel->nama_kelurahan }} (Kec. {{ $kel->kecamatan->nama_kecamatan ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">Latitude</label>
                    <input type="text" id="edit-lat" name="latitude" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">Longitude</label>
                    <input type="text" id="edit-lng" name="longitude" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">Radius (Meter)</label>
                    <input type="number" id="edit-radius" name="radius_meter" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan Wilayah / Potensi Bahaya</label>
                <textarea id="edit-keterangan" name="keterangan" rows="2"
                          class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#ea5921]"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold bg-[#ea5921] hover:bg-[#d94812] text-white rounded-xl shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }

    function openEditModal(data) {
        const form = document.getElementById('edit-form');
        form.action = `/admin/rawan/${data.id}`;
        document.getElementById('edit-bencana').value = data.id_bencana;
        document.getElementById('edit-kelurahan').value = data.id_kelurahan;
        document.getElementById('edit-zona').value = data.zona;
        document.getElementById('edit-lat').value = data.latitude || '';
        document.getElementById('edit-lng').value = data.longitude || '';
        document.getElementById('edit-radius').value = data.radius_meter || '250';
        document.getElementById('edit-keterangan').value = data.keterangan || '';
        document.getElementById('edit-modal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }
</script>
@endsection
