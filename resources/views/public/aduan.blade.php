@extends('layouts.public')

@section('title', 'Lapor Kejadian Bencana - AKSARA BOGOR')
@section('meta_description', 'Formulir Pelaporan dan Aduan Bencana Masyarakat Kabupaten Bogor. Terhubung langsung dengan Pusdalops PB BPBD Kabupaten Bogor.')

@section('content')
<div class="bg-gradient-to-b from-orange-50/70 to-transparent dark:from-orange-950/20 dark:to-transparent py-10 sm:py-14 border-b border-orange-100 dark:border-slate-800">
    <div class="container mx-auto px-4 sm:px-6 max-w-4xl">
        <div class="flex items-center gap-2 text-[#ea5921] dark:text-orange-400 font-extrabold text-xs uppercase tracking-wider mb-3">
            Layanan Pengaduan & Tanggap Darurat Warga
        </div>
        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-3">
            Lapor Kejadian Bencana
        </h1>
        <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">
            Laporkan segera kejadian darurat atau bencana alam di sekitar Anda agar tim BPBD Kabupaten Bogor dapat segera memverifikasi dan mengerahkan tim bantuan.
        </p>
    </div>
</div>

<div class="container mx-auto px-4 sm:px-6 py-10 max-w-4xl">
    
    @if(session('success'))
    <div class="mb-8 p-6 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center animate-in fade-in duration-300">
        <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="text-lg font-black text-emerald-900 dark:text-emerald-200 mb-2">Laporan Berhasil Terkirim!</h3>
        <p class="text-xs sm:text-sm text-emerald-700 dark:text-emerald-300 max-w-lg mx-auto mb-6">
            {{ session('success') }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('public.home') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md">
                Buka Peta Kebencanaan
            </a>
            <a href="{{ route('public.aduan') }}" class="px-5 py-2.5 rounded-xl border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 text-xs font-bold hover:bg-emerald-100/50">
                Kirim Laporan Lainnya
            </a>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-8 p-5 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-xs">
        <strong class="font-black block mb-1">Mohon periksa kesalahan input berikut:</strong>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="form-lapor-aduan" action="{{ route('public.aduan.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200/90 dark:border-slate-800 shadow-xl space-y-8">
        @csrf

        <!-- Bagian 1: Data Identitas Pelapor -->
        <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-[#ea5921] flex items-center justify-center text-xs font-black">1</span>
                Data Pelapor
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Nama Lengkap
                    </label>
                    <input type="text" name="nama_pelapor" value="{{ old('nama_pelapor') }}" placeholder="Contoh: Ahmad Fadilah"
                        class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:bg-white dark:focus:bg-slate-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        No. HP / WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="nomor_telepon" value="{{ old('nomor_telepon') }}" placeholder="Contoh: 081234567890" required
                        class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:bg-white dark:focus:bg-slate-900 focus:outline-none">
                </div>
            </div>

            <!-- Email Pelapor (Wajib) & OTP Verification -->
            @php
                $verifiedEmailSession = session('verified_aduan_email');
                $isPreVerified = !empty($verifiedEmailSession) && (old('email_pelapor') === $verifiedEmailSession || !old('email_pelapor'));
                $initialEmail = old('email_pelapor', $verifiedEmailSession ?? '');
            @endphp
            <div id="email-otp-container" class="mt-4 p-5 rounded-2xl bg-orange-50/40 dark:bg-slate-800/50 border border-orange-100 dark:border-slate-800 transition-all">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Email Pelapor (Wajib) <span class="text-red-500">*</span>
                    </label>
                    <div id="email-verification-badge" class="{{ $isPreVerified ? '' : 'hidden' }} inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>Email Terverifikasi</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <input type="email" id="email_pelapor" name="email_pelapor" value="{{ $initialEmail }}" placeholder="nama@email.com" required
                            {{ $isPreVerified ? 'readonly' : '' }}
                            class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 {{ $isPreVerified ? 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 cursor-not-allowed' : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white' }} focus:ring-2 focus:ring-[#ea5921] focus:outline-none"
                            oninput="onEmailInputChanged()">
                    </div>
                    <button type="button" id="btn-request-otp" onclick="requestOtp()"
                        class="{{ $isPreVerified ? 'hidden' : '' }} px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-orange-600 dark:hover:bg-orange-700 text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2 shrink-0">
                        <span id="btn-request-otp-text">Kirim Kode OTP</span>
                        <svg id="btn-request-otp-spinner" class="hidden animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5">
                    Email wajib aktif dan sah. Kode OTP akan dikirimkan untuk memastikan email Anda terdaftar dan bukan email palsu.
                </p>

                <!-- Box Input OTP (Dibawah Email) -->
                <div id="otp-section" class="{{ $isPreVerified ? 'hidden' : 'mt-4 pt-4 border-t border-orange-100 dark:border-slate-700/60' }}">
                    <div id="otp-status-message" class="hidden mb-3 text-xs p-3 rounded-xl"></div>

                    <div id="otp-input-container" class="hidden space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Masukkan 6 Digit Kode OTP <span class="text-red-500">*</span>
                            </label>
                            <span id="otp-countdown-text" class="text-xs text-orange-600 dark:text-orange-400 font-bold"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
                            <input type="text" id="otp_code" maxlength="6" inputmode="numeric" placeholder="123456"
                                class="w-full sm:w-48 text-center tracking-[0.3em] font-mono text-base font-extrabold px-4 py-2.5 rounded-xl border border-orange-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none"
                                onkeyup="if(this.value.length === 6) verifyOtp()">
                            <button type="button" id="btn-verify-otp" onclick="verifyOtp()"
                                class="px-6 py-2.5 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white text-xs font-black shadow-md shadow-orange-500/20 transition-all flex items-center justify-center gap-2">
                                <span id="btn-verify-otp-text">Verifikasi OTP</span>
                                <svg id="btn-verify-otp-spinner" class="hidden animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Silakan periksa kotak masuk atau spam di email Anda. Kode berlaku 10 menit.
                        </p>
                    </div>
                </div>

                <!-- Box Sukses Terverifikasi -->
                <div id="otp-verified-success-box" class="{{ $isPreVerified ? '' : 'hidden' }} mt-3 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>Email terverifikasi sah (<strong id="verified-email-label">{{ $initialEmail }}</strong>). Anda dapat melanjutkan pengisian laporan.</span>
                    </div>
                    <button type="button" onclick="changeEmail()" class="text-[11px] font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white underline shrink-0">
                        Ganti Email
                    </button>
                </div>

                <input type="hidden" id="is_email_verified" value="{{ $isPreVerified ? '1' : '0' }}">
            </div>
        </div>

        <!-- Bagian 2: Lokasi Kejadian di Kabupaten Bogor -->
        <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-[#ea5921] flex items-center justify-center text-xs font-black">2</span>
                Lokasi Kejadian (Kabupaten Bogor)
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Kecamatan <span class="text-red-500">*</span>
                    </label>
                    <select name="id_kecamatan" id="aduan-kecamatan" onchange="loadAduanKelurahan(this.value)" required
                        class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:bg-white dark:focus:bg-slate-900 focus:outline-none">
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatanList as $kec)
                        <option value="{{ $kec->id_kecamatan }}" {{ old('id_kecamatan') == $kec->id_kecamatan ? 'selected' : '' }}>
                            {{ $kec->nama_kecamatan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Desa / Kelurahan
                    </label>
                    <select name="id_kelurahan" id="aduan-kelurahan"
                        class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:bg-white dark:focus:bg-slate-900 focus:outline-none">
                        <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            RT
                        </label>
                        <input type="text" name="rt" value="{{ old('rt') }}" placeholder="01"
                            class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            RW
                        </label>
                        <input type="text" name="rw" value="{{ old('rw') }}" placeholder="05"
                            class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Alamat Lengkap / Patokan
                    </label>
                    <input type="text" name="alamat_detail" value="{{ old('alamat_detail') }}" placeholder="Contoh: Jl. Raya Bojonggede No. 12 dekat jembatan"
                        class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                </div>
            </div>

            <!-- GPS Coordinates Auto Detection -->
            <div class="mt-4 p-4 rounded-2xl bg-cyan-50/70 dark:bg-cyan-950/30 border border-cyan-200 dark:border-cyan-900/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs font-black text-cyan-900 dark:text-cyan-300 block">Koordinat Titik Lokasi Bencana (GPS)</span>
                        <p class="text-[11px] text-cyan-700 dark:text-cyan-400">Gunakan GPS ponsel/laptop Anda untuk akurasi peta pemantauan BPBD</p>
                    </div>
                    <button type="button" onclick="detectGPS()" 
                        class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-black text-xs inline-flex items-center gap-1.5 shrink-0 shadow-sm transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                        <span>Deteksi Lokasi GPS Saya</span>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="text-[10px] text-cyan-800 dark:text-cyan-400 uppercase font-bold block mb-1">Latitude</label>
                        <input type="text" id="gps-lat" name="latitude" value="{{ old('latitude', '-6.4855') }}" readonly
                            class="w-full text-xs font-mono font-bold px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-cyan-300 dark:border-cyan-800 text-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="text-[10px] text-cyan-800 dark:text-cyan-400 uppercase font-bold block mb-1">Longitude</label>
                        <input type="text" id="gps-lng" name="longitude" value="{{ old('longitude', '106.8450') }}" readonly
                            class="w-full text-xs font-mono font-bold px-3 py-2 rounded-lg bg-white dark:bg-slate-900 border border-cyan-300 dark:border-cyan-800 text-slate-800 dark:text-slate-100">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian 3: Rincian Kejadian Bencana -->
        <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-orange-100 dark:bg-orange-950 text-[#ea5921] flex items-center justify-center text-xs font-black">3</span>
                Rincian Bencana
            </h3>

            <div class="space-y-4 mt-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Jenis Kejadian Bencana <span class="text-red-500">*</span>
                        </label>
                        <select name="id_bencana" required
                            class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:bg-white dark:focus:bg-slate-900 focus:outline-none">
                            <option value="">-- Pilih Jenis Bencana --</option>
                            @foreach($bencanaList as $b)
                            <option value="{{ $b->id_bencana }}" {{ old('id_bencana') == $b->id_bencana ? 'selected' : '' }}>
                                {{ $b->nama_bencana }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Judul Singkat Kejadian <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Tanah Longsor Menutup Akses Jalan Utama" required
                            class="w-full text-xs font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:bg-white dark:focus:bg-slate-900 focus:outline-none">
                    </div>
                </div>

                {{-- Jumlah Korban & Kerusakan Bangunan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Jumlah Korban --}}
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">
                            Jumlah Korban (Jiwa)
                        </label>
                        <div class="space-y-2.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shrink-0"></span>
                                <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-24 shrink-0">Total Jiwa</label>
                                <input type="number" name="jumlah_korban_jiwa" value="{{ old('jumlah_korban_jiwa', 0) }}" min="0" placeholder="0"
                                    class="flex-1 text-xs font-medium px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                                <span class="text-[10px] text-slate-500 shrink-0">jiwa</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                                <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-24 shrink-0">Luka Ringan</label>
                                <input type="number" name="jumlah_korban_luka_ringan" value="{{ old('jumlah_korban_luka_ringan', 0) }}" min="0" placeholder="0"
                                    class="flex-1 text-xs font-medium px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                                <span class="text-[10px] text-slate-500 shrink-0">jiwa</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-500 shrink-0"></span>
                                <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-24 shrink-0">Luka Sedang</label>
                                <input type="number" name="jumlah_korban_luka_sedang" value="{{ old('jumlah_korban_luka_sedang', 0) }}" min="0" placeholder="0"
                                    class="flex-1 text-xs font-medium px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                                <span class="text-[10px] text-slate-500 shrink-0">jiwa</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-600 shrink-0"></span>
                                <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-24 shrink-0">Luka Berat</label>
                                <input type="number" name="jumlah_korban_luka_berat" value="{{ old('jumlah_korban_luka_berat', 0) }}" min="0" placeholder="0"
                                    class="flex-1 text-xs font-medium px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                                <span class="text-[10px] text-slate-500 shrink-0">jiwa</span>
                            </div>
                        </div>
                    </div>

                    {{-- Kerusakan Bangunan --}}
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">
                            Kerusakan Bangunan (Rumah)
                        </label>
                        <div class="space-y-2.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500 shrink-0"></span>
                                <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-24 shrink-0">Rusak Berat</label>
                                <input type="number" name="jumlah_rumah_rusak_berat" value="{{ old('jumlah_rumah_rusak_berat', 0) }}" min="0" placeholder="0"
                                    class="flex-1 text-xs font-medium px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                                <span class="text-[10px] text-slate-500 shrink-0">rumah</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                                <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-24 shrink-0">Rusak Sedang</label>
                                <input type="number" name="jumlah_rumah_rusak_sedang" value="{{ old('jumlah_rumah_rusak_sedang', 0) }}" min="0" placeholder="0"
                                    class="flex-1 text-xs font-medium px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                                <span class="text-[10px] text-slate-500 shrink-0">rumah</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <label class="text-[10px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider w-24 shrink-0">Rusak Ringan</label>
                                <input type="number" name="jumlah_rumah_rusak_ringan" value="{{ old('jumlah_rumah_rusak_ringan', 0) }}" min="0" placeholder="0"
                                    class="flex-1 text-xs font-medium px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:outline-none">
                                <span class="text-[10px] text-slate-500 shrink-0">rumah</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Kronologi / Deskripsi Kejadian <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" rows="4" placeholder="Ceritakan kondisi terkini, dampak, tinggi air/tinggi longsoran, dan kebutuhan mendesak di lokasi..." required
                        class="w-full text-xs sm:text-sm font-medium px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-[#ea5921] focus:bg-white dark:focus:bg-slate-900 focus:outline-none">{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Foto Upload with Preview -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Unggah Foto Kejadian Lapangan (Maksimal 5MB)
                    </label>
                    <div class="flex items-center gap-4">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors">
                            <svg class="w-4 h-4 text-[#ea5921]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Pilih Foto dari Galeri / Kamera</span>
                            <input type="file" name="foto" accept="image/*" class="hidden" onchange="previewPhoto(event)">
                        </label>
                        <span id="file-name-label" class="text-xs text-slate-400">Belum ada foto yang dipilih</span>
                    </div>

                    <div id="image-preview-container" class="hidden mt-3 max-w-xs rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
                        <img id="image-preview" src="" alt="Preview" class="w-full h-40 object-cover">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
            <a href="{{ route('public.home') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
                Batal
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-[#ea5921] to-orange-500 hover:from-[#d94812] hover:to-orange-600 text-white font-black text-sm shadow-xl shadow-orange-500/25 transition-all hover:scale-[1.02] active:scale-[0.98]">
                Kirim Laporan Bencana Sekarang
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    function loadAduanKelurahan(kecamatanId) {
        const select = document.getElementById('aduan-kelurahan');
        select.innerHTML = '<option value="">Memuat data kelurahan...</option>';

        if (!kecamatanId) {
            select.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
            return;
        }

        fetch(`{{ route('public.api.kelurahan') }}?kecamatan_id=${kecamatanId}`)
            .then(res => res.json())
            .then(data => {
                select.innerHTML = '<option value="">-- Pilih Desa / Kelurahan --</option>';
                data.forEach(item => {
                    select.innerHTML += `<option value="${item.id_kelurahan}">${item.nama_kelurahan}</option>`;
                });
            })
            .catch(() => {
                select.innerHTML = '<option value="">Gagal memuat desa/kelurahan</option>';
            });
    }

    function detectGPS() {
        if (!navigator.geolocation) {
            alert('Perangkat Anda tidak mendukung fitur lokasi GPS.');
            return;
        }

        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('gps-lat').value = pos.coords.latitude.toFixed(7);
            document.getElementById('gps-lng').value = pos.coords.longitude.toFixed(7);
            alert('Titik GPS berhasil terdeteksi: ' + pos.coords.latitude.toFixed(5) + ', ' + pos.coords.longitude.toFixed(5));
        }, function(err) {
            alert('Gagal mendeteksi lokasi GPS. Pastikan izin lokasi aktif di browser Anda.');
        }, { enableHighAccuracy: true });
    }

    function previewPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('file-name-label').innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-container').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // ==========================================
    // OTP VERIFIKASI EMAIL PELAPOR
    // ==========================================
    let otpCooldownInterval = null;

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showOtpStatus(message, isSuccess = false, isWarning = false) {
        const el = document.getElementById('otp-status-message');
        el.className = 'mb-3 text-xs p-3 rounded-xl border leading-relaxed';
        
        if (isSuccess) {
            el.classList.add('bg-emerald-50', 'dark:bg-emerald-950/40', 'text-emerald-800', 'dark:text-emerald-300', 'border-emerald-200', 'dark:border-emerald-800');
        } else if (isWarning) {
            el.classList.add('bg-amber-50', 'dark:bg-amber-950/40', 'text-amber-800', 'dark:text-amber-300', 'border-amber-200', 'dark:border-amber-800');
        } else {
            el.classList.add('bg-red-50', 'dark:bg-red-950/40', 'text-red-700', 'dark:text-red-300', 'border-red-200', 'dark:border-red-800');
        }
        el.innerHTML = message;
        el.classList.remove('hidden');
    }

    function hideOtpStatus() {
        const el = document.getElementById('otp-status-message');
        el.classList.add('hidden');
        el.innerHTML = '';
    }

    function onEmailInputChanged() {
        const isVerified = document.getElementById('is_email_verified').value === '1';
        if (isVerified) {
            document.getElementById('is_email_verified').value = '0';
            document.getElementById('email-verification-badge').classList.add('hidden');
            document.getElementById('otp-verified-success-box').classList.add('hidden');
            document.getElementById('btn-request-otp').classList.remove('hidden');
            document.getElementById('otp-section').classList.remove('hidden');
        }
    }

    function requestOtp() {
        const emailInput = document.getElementById('email_pelapor');
        const email = emailInput.value.trim();

        if (!email) {
            emailInput.focus();
            showOtpStatus('Harap masukkan alamat email Anda terlebih dahulu.');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            emailInput.focus();
            showOtpStatus('Format alamat email tidak valid.');
            return;
        }

        const btn = document.getElementById('btn-request-otp');
        const btnText = document.getElementById('btn-request-otp-text');
        const btnSpinner = document.getElementById('btn-request-otp-spinner');

        btn.disabled = true;
        btnText.innerText = 'Mengirim...';
        btnSpinner.classList.remove('hidden');
        hideOtpStatus();

        fetch("{{ route('public.aduan.otp.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || 'Gagal mengirim kode OTP.');
            }
            return data;
        })
        .then(data => {
            let msg = data.message;
            if (data.dev_otp) {
                msg += `<div class="mt-2 p-2.5 rounded-lg bg-orange-100/70 dark:bg-slate-900 border border-orange-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-semibold">
                    <span class="text-orange-700 dark:text-orange-400 uppercase text-[10px] tracking-wider block font-extrabold mb-0.5">DEV / LOCAL TEST:</span>
                    Kode OTP: <span class="font-mono text-base font-black text-[#ea5921] tracking-widest">${data.dev_otp}</span>
                </div>`;
            }
            showOtpStatus(msg, true);

            document.getElementById('otp-section').classList.remove('hidden');
            document.getElementById('otp-input-container').classList.remove('hidden');
            const otpCodeInput = document.getElementById('otp_code');
            otpCodeInput.value = '';
            otpCodeInput.focus();

            startOtpCountdown(data.cooldown || 60);
        })
        .catch(err => {
            showOtpStatus(err.message || 'Terjadi kesalahan saat mengirim OTP.');
            btn.disabled = false;
            btnText.innerText = 'Kirim Kode OTP';
            btnSpinner.classList.add('hidden');
        });
    }

    function startOtpCountdown(seconds) {
        const btn = document.getElementById('btn-request-otp');
        const btnText = document.getElementById('btn-request-otp-text');
        const btnSpinner = document.getElementById('btn-request-otp-spinner');
        const countdownText = document.getElementById('otp-countdown-text');

        if (otpCooldownInterval) clearInterval(otpCooldownInterval);

        btn.disabled = true;
        btnSpinner.classList.add('hidden');

        let remaining = seconds;
        countdownText.innerText = `Kirim ulang (${remaining}s)`;
        btnText.innerText = `Tunggu (${remaining}s)`;

        otpCooldownInterval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(otpCooldownInterval);
                btn.disabled = false;
                btnText.innerText = 'Kirim Ulang OTP';
                countdownText.innerText = '';
            } else {
                countdownText.innerText = `Kirim ulang (${remaining}s)`;
                btnText.innerText = `Tunggu (${remaining}s)`;
            }
        }, 1000);
    }

    function verifyOtp() {
        const email = document.getElementById('email_pelapor').value.trim();
        const otpInput = document.getElementById('otp_code');
        const otp = otpInput.value.trim();

        if (!otp || otp.length !== 6) {
            showOtpStatus('Masukkan 6 digit angka kode OTP yang diterima.');
            otpInput.focus();
            return;
        }

        const btn = document.getElementById('btn-verify-otp');
        const btnText = document.getElementById('btn-verify-otp-text');
        const btnSpinner = document.getElementById('btn-verify-otp-spinner');

        btn.disabled = true;
        btnText.innerText = 'Memverifikasi...';
        btnSpinner.classList.remove('hidden');
        hideOtpStatus();

        fetch("{{ route('public.aduan.otp.verify') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email, otp: otp })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || 'Kode OTP tidak valid.');
            }
            return data;
        })
        .then(data => {
            document.getElementById('is_email_verified').value = '1';

            // Tampilkan badge terverifikasi dan box sukses
            document.getElementById('email-verification-badge').classList.remove('hidden');
            document.getElementById('otp-verified-success-box').classList.remove('hidden');
            document.getElementById('verified-email-label').innerText = email;

            const emailInput = document.getElementById('email_pelapor');
            emailInput.readOnly = true;
            emailInput.classList.remove('bg-white', 'dark:bg-slate-800');
            emailInput.classList.add('bg-slate-100', 'dark:bg-slate-700/60', 'text-slate-600', 'dark:text-slate-300', 'cursor-not-allowed');

            document.getElementById('btn-request-otp').classList.add('hidden');
            document.getElementById('otp-section').classList.add('hidden');
            document.getElementById('otp-input-container').classList.add('hidden');

            if (otpCooldownInterval) clearInterval(otpCooldownInterval);
        })
        .catch(err => {
            showOtpStatus(err.message || 'Gagal memverifikasi OTP.');
        })
        .finally(() => {
            btn.disabled = false;
            btnText.innerText = 'Verifikasi OTP';
            btnSpinner.classList.add('hidden');
        });
    }

    function changeEmail() {
        document.getElementById('is_email_verified').value = '0';
        const emailInput = document.getElementById('email_pelapor');
        emailInput.readOnly = false;
        emailInput.classList.remove('bg-slate-100', 'dark:bg-slate-700/60', 'text-slate-600', 'dark:text-slate-300', 'cursor-not-allowed');
        emailInput.classList.add('bg-white', 'dark:bg-slate-800');
        emailInput.focus();

        document.getElementById('email-verification-badge').classList.add('hidden');
        document.getElementById('otp-verified-success-box').classList.add('hidden');
        const reqBtn = document.getElementById('btn-request-otp');
        reqBtn.classList.remove('hidden');
        reqBtn.disabled = false;
        document.getElementById('btn-request-otp-text').innerText = 'Kirim Kode OTP';
        document.getElementById('otp-section').classList.remove('hidden');
        document.getElementById('otp-input-container').classList.add('hidden');
        hideOtpStatus();
    }

    // Intersep submit form untuk validasi OTP
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-lapor-aduan');
        if (form) {
            form.addEventListener('submit', function(e) {
                const isVerified = document.getElementById('is_email_verified').value === '1';
                if (!isVerified) {
                    e.preventDefault();
                    const container = document.getElementById('email-otp-container');
                    container.classList.add('ring-4', 'ring-red-400', 'dark:ring-red-800');
                    setTimeout(() => container.classList.remove('ring-4', 'ring-red-400', 'dark:ring-red-800'), 3500);
                    
                    document.getElementById('otp-section').classList.remove('hidden');
                    showOtpStatus('<strong>Perhatian:</strong> Email pelapor wajib diverifikasi dengan kode OTP sebelum mengirimkan laporan bencana. Silakan klik <strong>Kirim Kode OTP</strong>.', false, true);
                    container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    });
</script>
@endpush
