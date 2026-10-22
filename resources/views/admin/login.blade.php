<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AKSARA BOGOR</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/aksara-logo-32.png') }}">

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
        <div class="hidden lg:flex flex-col justify-between bg-[#ea5921] p-12 xl:p-16 text-white relative">
            
            <!-- Logo & Nama Aplikasi -->
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white/10 p-1.5 flex items-center justify-center border border-white/20">
                    <img src="{{ asset('images/aksara-logo.png') }}" alt="Logo AKSARA" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="font-extrabold text-lg leading-tight tracking-tight">AKSARA BOGOR</h1>
                    <p class="text-xs text-orange-100">Kabupaten Bogor</p>
                </div>
            </div>

            <!-- Teks Utama -->
            <div class="my-auto max-w-md py-8">
                <span class="inline-block text-xs font-semibold uppercase tracking-wider text-orange-200 mb-2">
                    Portal Internal Petugas
                </span>
                <h2 class="text-3xl font-extrabold leading-snug mb-3">
                    Sistem Informasi Kebencanaan Kabupaten Bogor
                </h2>
                <p class="text-sm text-orange-100 leading-relaxed">
                    Sistem pemantauan, verifikasi laporan masyarakat, dan koordinasi penanganan kejadian bencana di seluruh wilayah Kabupaten Bogor.
                </p>
            </div>

            <!-- Catatan Bawah -->
           
        </div>

        <!-- SISI KANAN: FORM LOGIN -->
        <div class="flex flex-col justify-between bg-white p-6 sm:p-12 lg:p-16 min-h-screen">
            
            <!-- Navigasi Atas -->
            <div class="flex items-center justify-between mb-8">
                <!-- Tampil hanya di HP -->
                <div class="flex lg:hidden items-center gap-2.5">
                    <img src="{{ asset('images/aksara-logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
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
            <div class="w-full max-w-sm mx-auto my-auto py-2">
                
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-900 mb-1">Masuk</h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Masukkan email dan password akun Anda untuk melanjutkan.
                    </p>
                </div>

                <!-- Notifikasi Error jika login gagal -->
                @if($errors->any())
                <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <form action="{{ route('public.login.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Field Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                            Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@email.com"
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder:text-slate-400 focus:border-[#ea5921] focus:ring-1 focus:ring-[#ea5921] outline-none transition">
                    </div>

                    <!-- Field Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                placeholder="••••••••"
                                class="w-full text-sm px-3.5 py-2.5 pr-10 rounded-lg border border-slate-300 bg-white text-slate-800 placeholder:text-slate-400 focus:border-[#ea5921] focus:ring-1 focus:ring-[#ea5921] outline-none transition">
                            
                            <button type="button" onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                title="Lihat password">
                                <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-off-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-slate-600">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-[#ea5921] focus:ring-[#ea5921] cursor-pointer">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk -->
                    <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-lg bg-[#ea5921] hover:bg-[#d94812] text-white font-semibold text-sm transition shadow-sm active:scale-[0.99] mt-2">
                        Masuk
                    </button>
                </form>

            </div>

            <!-- Footer Bawah -->
            <div class="text-center text-xs text-slate-400 pt-6">
                &copy; {{ date('Y') }} BPBD Kabupaten Bogor
            </div>

        </div>

    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const eye = document.getElementById('eye-icon');
            const eyeOff = document.getElementById('eye-off-icon');

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
    </script>
</body>
</html>