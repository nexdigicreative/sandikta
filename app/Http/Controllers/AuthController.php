<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FailedLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $throttleKey = 'login_' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            FailedLogin::recordFailure($request->username, 'rate_limited');
            ActivityLog::log('rate_limited', "Login rate limited untuk IP: {$request->ip()}", null, null, 'warning');
            
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Find user by NIS or email
        $user = User::where('nis', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 300); // 5 minute decay
            
            FailedLogin::recordFailure($request->username);
            ActivityLog::log('failed_login', "Percobaan login gagal: {$request->username}", null, null, 'warning', [
                'username' => $request->username,
            ]);

            throw ValidationException::withMessages([
                'username' => 'NIS/Email atau password salah.',
            ]);
        }

        if (!$user->is_active) {
            FailedLogin::recordFailure($request->username, 'account_disabled');
            
            throw ValidationException::withMessages([
                'username' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        
        RateLimiter::clear($throttleKey);

        // Update login info
        $userAgent = $request->header('User-Agent', 'Unknown');
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'last_login_browser' => $this->parseBrowser($userAgent),
        ]);

        session(['last_activity_time' => time()]);

        ActivityLog::log('login', "User {$user->name} berhasil login", User::class, $user->id);

        if ($user->must_change_password) {
            return redirect()->route('password.force-change');
        }

        return $this->redirectToDashboard();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::log('logout', "User {$user->name} logout", User::class, $user->id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    public function showForceChangePassword()
    {
        return view('auth.force-change-password');
    }

    public function forceChangePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => $request->password,
            'must_change_password' => false,
        ]);

        ActivityLog::log('change_password', "User {$user->name} mengubah password pertama kali", User::class, $user->id);

        return $this->redirectToDashboard()->with('success', 'Password berhasil diubah!');
    }

    private function redirectToDashboard()
    {
        $user = Auth::user();
        
        return match ($user->role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }

    private function parseBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Edg/')) return 'Edge';
        if (str_contains($userAgent, 'Chrome/')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox/')) return 'Firefox';
        if (str_contains($userAgent, 'Safari/')) return 'Safari';
        return 'Other';
    }
}
