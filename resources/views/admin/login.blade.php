<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AKSARA BOGOR</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/logobpbd.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen w-full">
        
        <!-- SISI KIRI: INFORMASI & BRANDING -->
        <div class="hidden lg:flex flex-col justify-between bg-slate-900 p-12 xl:p-16 text-white relative overflow-hidden">
            
            <!-- Background Image dengan Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('icon/backlogin.png') }}" alt="Gedung BPBD Kabupaten Bogor" class="w-full h-full object-cover">
                <!-- Overlay gradient multi-layer untuk kontras teks yang optimal dan estetika elegan -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-600/85 via-slate-900/60 to-slate-950/70"></div>
                <div class="absolute inset-0 bg-[#ea5921]/30 mix-blend-multiply"></div>
            </div>

            <!-- Logo & Nama Aplikasi -->
            <div class="relative z-10 flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-md p-1.5 flex items-center justify-center border border-white/25 shadow-lg">
                    <img src="{{ asset('images/logobpbd.png') }}" alt="Logo AKSARA" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="font-extrabold text-lg leading-tight tracking-tight drop-shadow-sm">AKSARA BOGOR</h1>
                    <p class="text-xs text-orange-200 drop-shadow-sm">Kabupaten Bogor</p>
                </div>
            </div>

            <!-- Teks Utama -->
            <div class="relative z-10 my-auto max-w-md py-8">
                <h2 class="text-3xl xl:text-4xl font-extrabold leading-snug mb-3 drop-shadow-md">
                    Sistem Informasi Kebencanaan Kabupaten Bogor
                </h2>
            </div>
        </div>

        <!-- SISI KANAN: FORM LOGIN -->
        <div class="flex flex-col justify-between bg-white p-6 sm:p-12 lg:p-16 min-h-screen">
            
            <!-- Navigasi Atas -->
            <div class="flex items-center justify-between mb-8">
                <!-- Tampil hanya di HP -->
                <div class="flex lg:hidden items-center gap-2.5">
                    <img src="{{ asset('images/logobpbd.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                    <div>
                        <span class="font-bold text-sm text-slate-900 block leading-tight">AKSARA BOGOR</span>
                        <span class="text-[10px] text-slate-500">Kabupaten Bogor</span>
                    </div>
                </div>

                <a href="{{ route('public.home') }}" 
                    class="ml-auto inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#ea5921] transition-colors py-1.5 px-3 rounded-lg hover:bg-slate-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

            <!-- Container Form -->
            <div class="w-full max-w-md mx-auto my-auto py-2">
                
                <div class="mb-5">
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-1">Masuk Portal Admin</h2>
                    <p id="login-subtitle" class="text-xs sm:text-sm text-slate-500">
                        Pilih kelurahan dan masukkan password akun kelurahan Anda.
                    </p>
                </div>

                <!-- SWITCH TAB ROLE LOGIN -->
                <div class="mb-5 p-1 bg-slate-100 rounded-xl grid grid-cols-2 gap-1 text-xs font-bold">
                    <button type="button" id="tab-btn-kelurahan" onclick="switchLoginTab('kelurahan')"
                            class="py-2.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all bg-white text-orange-600 shadow-xs cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Admin Kelurahan</span>
                    </button>
                    <button type="button" id="tab-btn-bpbd" onclick="switchLoginTab('bpbd')"
                            class="py-2.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all text-slate-500 hover:text-slate-800 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Petugas BPBD Pusat</span>
                    </button>
                </div>

                <!-- Notifikasi Error jika login gagal -->
                @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <!-- 1. FORM LOGIN: ADMIN KELURAHAN / DESA -->
                <form id="form-kelurahan" action="{{ route('public.login.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="login_type" value="kelurahan">

                    <!-- 1. Pilih Kecamatan Terlebih Dahulu -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            1. Pilih Kecamatan <span class="text-rose-500">*</span>
                        </label>
                        <select id="login_kecamatan" onchange="filterLoginKelurahan()" 
                                class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-800 focus:border-[#ea5921] focus:ring-1 focus:ring-[#ea5921] outline-none transition">
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id_kecamatan }}">Kec. {{ $d->nama_kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Pilih Kelurahan / Desa -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            2. Pilih Kelurahan / Desa <span class="text-rose-500">*</span>
                        </label>
                        <select name="id_kelurahan" id="login_kelurahan" required
                                class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-800 focus:border-[#ea5921] focus:ring-1 focus:ring-[#ea5921] outline-none transition">
                            <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                        </select>
                    </div>

                    <!-- 3. Password Kelurahan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Password Akun Kelurahan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_kelurahan" name="password" required
                                placeholder="Masukkan password kelurahan"
                                class="w-full text-xs sm:text-sm px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 bg-white text-slate-800 placeholder:text-slate-400 focus:border-[#ea5921] focus:ring-1 focus:ring-[#ea5921] outline-none transition">
                            
                            <button type="button" onclick="togglePasswordVisibility('password_kelurahan', 'eye-kel', 'eye-off-kel')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                title="Lihat password">
                                <svg id="eye-kel" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-off-kel" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                       
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-slate-600">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-[#ea5921] focus:ring-[#ea5921] cursor-pointer">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk Kelurahan -->
                    <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl bg-[#ea5921] hover:bg-[#d94812] text-white font-bold text-xs sm:text-sm transition shadow-sm active:scale-[0.99] mt-2 cursor-pointer">
                        Masuk sebagai Admin Kelurahan
                    </button>
                </form>

                <!-- 2. FORM LOGIN: PETUGAS BPBD / PUSAT -->
                <form id="form-bpbd" action="{{ route('public.login.store') }}" method="POST" class="space-y-3.5 hidden">
                    @csrf
                    <input type="hidden" name="login_type" value="bpbd">

                    <!-- Field Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1">
                            Email Petugas BPBD <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', 'admin@bencana.com') }}" required
                            placeholder="admin@bencana.com"
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-800 placeholder:text-slate-400 focus:border-[#ea5921] focus:ring-1 focus:ring-[#ea5921] outline-none transition">
                    </div>

                    <!-- Field Password -->
                    <div>
                        <label for="password_bpbd" class="block text-xs font-bold text-slate-700 mb-1">
                            Password Akun BPBD <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_bpbd" name="password" required
                                placeholder="••••••••"
                                class="w-full text-xs sm:text-sm px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 bg-white text-slate-800 placeholder:text-slate-400 focus:border-[#ea5921] focus:ring-1 focus:ring-[#ea5921] outline-none transition">
                            
                            <button type="button" onclick="togglePasswordVisibility('password_bpbd', 'eye-bpbd', 'eye-off-bpbd')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                title="Lihat password">
                                <svg id="eye-bpbd" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-off-bpbd" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-slate-600">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-[#ea5921] focus:ring-[#ea5921] cursor-pointer">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk BPBD -->
                    <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm transition shadow-sm active:scale-[0.99] mt-2 cursor-pointer">
                        Masuk sebagai Petugas BPBD
                    </button>
                </form>

            </div>
        </div>

    </div>

    <script>
        const districtsData = {!! json_encode($districts ?? []) !!};

        function filterLoginKelurahan(selectedKelId = null) {
            const kecSelect = document.getElementById('login_kecamatan');
            const kelSelect = document.getElementById('login_kelurahan');
            if (!kecSelect || !kelSelect) return;

            const kecId = kecSelect.value;
            kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';

            if (!kecId) {
                kelSelect.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
                return;
            }

            const district = districtsData.find(d => d.id_kecamatan == kecId);
            if (district && district.kelurahan && district.kelurahan.length > 0) {
                district.kelurahan.forEach(kel => {
                    const opt = document.createElement('option');
                    opt.value = kel.id_kelurahan;
                    opt.textContent = kel.nama_kelurahan;
                    if (selectedKelId && kel.id_kelurahan == selectedKelId) {
                        opt.selected = true;
                    }
                    kelSelect.appendChild(opt);
                });
            } else {
                kelSelect.innerHTML = '<option value="">(Belum ada data kelurahan)</option>';
            }
        }

        function switchLoginTab(type) {
            const btnKel = document.getElementById('tab-btn-kelurahan');
            const btnBpbd = document.getElementById('tab-btn-bpbd');
            const formKel = document.getElementById('form-kelurahan');
            const formBpbd = document.getElementById('form-bpbd');
            const subtitle = document.getElementById('login-subtitle');

            if (type === 'kelurahan') {
                btnKel.className = 'py-2.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all bg-white text-orange-600 shadow-xs cursor-pointer';
                btnBpbd.className = 'py-2.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all text-slate-500 hover:text-slate-800 cursor-pointer';
                formKel.classList.remove('hidden');
                formBpbd.classList.add('hidden');
                subtitle.innerText = 'Pilih kelurahan dan masukkan password akun kelurahan Anda.';
            } else {
                btnBpbd.className = 'py-2.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all bg-white text-slate-900 shadow-xs cursor-pointer';
                btnKel.className = 'py-2.5 px-3 rounded-lg flex items-center justify-center gap-1.5 transition-all text-slate-500 hover:text-slate-800 cursor-pointer';
                formBpbd.classList.remove('hidden');
                formKel.classList.add('hidden');
                subtitle.innerText = 'Masukkan email dan password akun admin BPBD Pusat.';
            }
        }

        function togglePasswordVisibility(inputId, eyeId, eyeOffId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const eyeOff = document.getElementById(eyeOffId);

            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.add('hidden');
                eyeOff.classList.remove('hidden');
            } else {
                input.type = 'password';
                eye.classList.remove('hidden');
                eyeOff.classList.add('hidden');
            }
        }

        // Cek jika sesi redirect minta aktifkan tab BPBD
        @if(session('active_tab') === 'bpbd' || old('email'))
            switchLoginTab('bpbd');
        @endif
    </script>
</body>
</html>