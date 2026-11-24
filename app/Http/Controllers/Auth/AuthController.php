<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilan Halaman Login Admin / Petugas
     */
    public function login()
    {
        $districts = Kecamatan::with('kelurahan')->orderBy('nama_kecamatan')->get();

        return view('admin.login', compact('districts'));
    }

    /**
     * Proses Autentikasi Login (Mendukung Login Admin Kelurahan & Admin BPBD Pusat)
     */
    public function authenticate(Request $request)
    {
        $loginType = $request->input('login_type', 'kelurahan');

        // 1. ALUR LOGIN: ADMIN KELURAHAN / DESA (Pilih Kelurahan + Password)
        if ($loginType === 'kelurahan') {
            $request->validate([
                'id_kelurahan' => ['required', 'exists:app_md_kelurahan,id_kelurahan'],
                'password' => ['required', 'string'],
            ], [
                'id_kelurahan.required' => 'Silakan pilih Kelurahan / Desa terlebih dahulu.',
                'id_kelurahan.exists' => 'Pilihan kelurahan tidak valid.',
                'password.required' => 'Password kelurahan wajib diisi.',
            ]);

            $user = User::where('id_kelurahan', $request->id_kelurahan)
                ->whereIn('role', ['ADMIN_KELURAHAN', 'KELURAHAN'])
                ->first();

            // Jika akun kelurahan belum ada, auto-provisioning dengan password default
            if (!$user && ($request->password === 'kelurahan123' || $request->password === 'aksarabogor')) {
                $kel = Kelurahan::with('kecamatan')->find($request->id_kelurahan);
                if ($kel) {
                    $user = User::create([
                        'nama' => 'Admin ' . $kel->nama_kelurahan,
                        'email' => 'kel_' . $kel->id_kelurahan . '@bencana.bogorkab.go.id',
                        'password' => Hash::make($request->password),
                        'role' => 'ADMIN_KELURAHAN',
                        'id_kecamatan' => $kel->id_kecamatan,
                        'id_kelurahan' => $kel->id_kelurahan,
                        'is_active' => true,
                    ]);
                }
            }

            if ($user && Hash::check($request->password, $user->password)) {
                if (!$user->is_active) {
                    return back()->withErrors(['login_error' => 'Akun admin kelurahan ini berstatus non-aktif. Silakan hubungi BPBD.'])
                        ->withInput($request->except('password'))
                        ->with('active_tab', 'kelurahan');
                }

                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                // Admin Kelurahan langsung diarahkan ke halaman validasi aduan kelurahannya
                return redirect()->intended(route('admin.laporan.validasi'))
                    ->with('success', 'Selamat datang, ' . $user->nama . '!');
            }

            return back()->withErrors([
                'login_error' => 'Password akun kelurahan salah. (Gunakan password default: kelurahan123)',
            ])->withInput($request->except('password'))->with('active_tab', 'kelurahan');
        }

        // 2. ALUR LOGIN: PETUGAS BPBD / PUSAT (Email + Password)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'login_error' => 'Email atau password akun BPBD tidak sesuai.',
        ])->withInput($request->only('email'))->with('active_tab', 'bpbd');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.home');
    }
}
