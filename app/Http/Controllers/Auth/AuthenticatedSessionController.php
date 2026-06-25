<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Validasi input username dan password terlebih dahulu
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Cari data admin berdasarkan username di database
        $admin = \App\Models\Admin::where('username', $request->username)->first();

        // 3. Cek apakah admin ada dan password-nya cocok
        if ($admin && Hash::check($request->password, $admin->password)) {
            
            // 4. CEK SYSTEM 1 AKUN 1 DEVICE
            // PERBAIKAN: Menggunakan $admin->id_admin agar sesuai dengan database
            $isLoggedIn = DB::table('sessions')
                ->where('user_id', $admin->id_admin)
                ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->getTimestamp())
                ->exists();

            if ($isLoggedIn) {
                // Jika terdeteksi sedang aktif di device lain, kunci akses masuknya!
                throw ValidationException::withMessages([
                    'username' => 'Akun admin ini sedang login di perangkat lain. Silahkan logout terlebih dahulu dari perangkat tersebut.',
                ]);
            }

            // 5. Jika aman, jalankan otentikasi login default bawaan laravel
            try {
                $request->authenticate();
            } catch (\Illuminate\Validation\ValidationException $e) {
                throw ValidationException::withMessages([
                    'username' => 'Username atau password yang Anda masukkan salah.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, Administrator!');
        }

        // Gagal login standar jika username/password salah sejak awal
        throw ValidationException::withMessages([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}