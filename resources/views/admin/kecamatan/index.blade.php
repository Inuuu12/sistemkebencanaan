@extends('layouts.admin')

@section('title', 'Master Data Kecamatan - GIS Layer')

@section('content')
<div class="space-y-6">

    <!-- 1. HEADER HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span>Master Data Kecamatan</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Data 40 Kecamatan induk administratif di seluruh wilayah Kabupaten Bogor.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <button type="button" onclick="openCreateModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-[#ea5921] text-white hover:bg-[#d94812] transition shadow-sm shadow-orange-500/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Kecamatan Baru</span>
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

    <!-- 2. REKAPITULASI METRIK -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <!-- Total Kecamatan -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Kecamatan</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalKecamatan) }}</span>
                <span class="text-xs text-slate-500 font-semibold">Kecamatan</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Wilayah administratif Kabupaten Bogor</p>
        </div>

        <!-- Total Desa Terdaftar -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Desa & Kelurahan Dinaungi</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalKelurahan) }}</span>
                <span class="text-xs text-slate-500 font-semibold">Desa / Kel</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Rata-rata ~11 desa per kecamatan</p>
        </div>

        <!-- Kecamatan Terdampak -->
        <div class="p-4 rounded-2xl bg-white border border-orange-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#ea5921] block">Kecamatan Pernah Terdampak</span>
            <div class="flex items-baseline gap-1.5 mt-1">
                <span class="text-2xl font-black text-[#ea5921]">{{ number_format($kecamatanTerdampak) }}</span>
                <span class="text-xs text-[#ea5921] font-semibold">Kecamatan</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Memiliki rekaman insiden kebencanaan</p>
        </div>
    </div>

    <!-- 3. FILTER & PENCARIAN -->
    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.kecamatan.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kecamatan..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#ea5921] focus:border-transparent">
            </div>

            <div class="w-full sm:w-56">
                <select name="sort" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#ea5921] focus:border-transparent">
                    <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Urut Nama (A-Z)</option>
                    <option value="terbanyak_kelurahan" {{ request('sort') == 'terbanyak_kelurahan' ? 'selected' : '' }}>Desa Terbanyak</option>
                    <option value="terbanyak_laporan" {{ request('sort') == 'terbanyak_laporan' ? 'selected' : '' }}>Laporan Bencana Terbanyak</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white rounded-xl shadow-xs transition-all">
                    Filter
                </button>
                @if(request('q') || request('sort'))
                <a href="{{ route('admin.kecamatan.index') }}" class="px-3.5 py-2 text-xs font-semibold border border-slate-300 text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. TABEL DATA KECAMATAN -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-extrabold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Kecamatan</th>
                        <th class="py-3 px-4 text-center">Jumlah Desa / Kelurahan</th>
                        <th class="py-3 px-4 text-center">Riwayat Laporan Bencana</th>
                        <th class="py-3 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kecamatanList as $index => $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 px-4 text-center font-bold text-slate-400">
                            {{ $kecamatanList->firstItem() + $index }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 text-xs sm:text-sm">Kecamatan {{ $item->nama_kecamatan }}</span>
                            <span class="text-[10px] text-slate-400 block font-mono">Kode Wilayah: #KEC-{{ str_pad($item->id_kecamatan, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('admin.kelurahan.index', ['kecamatan_id' => $item->id_kecamatan]) }}" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-[#ea5921] transition-colors" title="Lihat daftar desa">
                                🏘️ {{ $item->kelurahan_count }} Desa
                            </a>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($item->laporan_bencana_count > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-orange-100 text-[#ea5921]">
                                {{ $item->laporan_bencana_count }} Laporan
                            </span>
                            @else
                            <span class="text-slate-400 text-[11px]">0 Laporan</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Tombol Edit -->
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode(['id' => $item->id_kecamatan, 'nama' => $item->nama_kecamatan]) }})"
                                        class="p-1.5 rounded-lg border border-slate-200 hover:border-slate-300 text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Edit Kecamatan">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.kecamatan.destroy', $item->id_kecamatan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Kecamatan {{ $item->nama_kecamatan }}?');" class="inline">
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
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <span class="font-bold text-xs">Tidak ditemukan kecamatan.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kecamatanList->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $kecamatanList->links() }}
        </div>
        @endif
    </div>

</div>

<!-- MODAL TAMBAH KECAMATAN -->
<div id="create-modal" class="hidden fixed inset-0 z-[600] bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-slate-900">Tambah Kecamatan Baru</h3>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.kecamatan.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Nama Kecamatan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_kecamatan" required placeholder="Contoh: Cibinong"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#ea5921]">
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold bg-[#ea5921] hover:bg-[#d94812] text-white rounded-xl shadow-sm">
                    Simpan Kecamatan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KECAMATAN -->
<div id="edit-modal" class="hidden fixed inset-0 z-[600] bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-slate-900">Perbarui Nama Kecamatan</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Nama Kecamatan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="edit-nama" name="nama_kecamatan" required
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#ea5921]">
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
        form.action = `/admin/kecamatan/${data.id}`;
        document.getElementById('edit-nama').value = data.nama;
        document.getElementById('edit-modal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }
</script>
@endsection
