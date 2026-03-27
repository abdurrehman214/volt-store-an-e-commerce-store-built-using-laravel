<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Authentication Routes
| Laravel 8 — Manual auth (no Breeze)
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════════════════════════
// GUEST ONLY — redirect to home if already logged in
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {

    // ── Login ────────────────────────────────────────────────────────────────
    Route::get('login', fn() => view('auth.login'))->name('login');

    Route::post('login', function (Request $request) {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // ✅ FIX: after login, send admin/employee to dashboard
            //    instead of always going to home page
            $user = Auth::user();
            if ($user->isStaff()) {
                return redirect()->route('admin.dashboard');
            }

            // Customers go to wherever they were trying to go (e.g. checkout)
            // or fall back to home
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    });

    // ── Register ─────────────────────────────────────────────────────────────
    Route::get('register', fn() => view('auth.register'))->name('register');

    Route::post('register', function (Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms'    => 'accepted',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
        ]);

        Auth::login($user);

        // ✅ FIX: redirect to home with a welcome message
        //    instead of a plain redirect('/') with no feedback
        return redirect('/')->with('success', 'Welcome, ' . $user->name . '! Your account has been created.');
    });

    // ── Forgot Password ───────────────────────────────────────────────────────
    // ✅ KEPT your password reset flow exactly — it was correct
    Route::get('forgot-password', fn() => view('auth.forgot-password'))->name('password.request');

    Route::post('forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    // ── Reset Password ────────────────────────────────────────────────────────
    Route::get('reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('reset-password', function (Request $request) {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    })->name('password.update');
});

// ═══════════════════════════════════════════════════════════════════════════
// AUTHENTICATED ONLY
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // ── Logout ────────────────────────────────────────────────────────────────
    // ✅ KEPT your logout — correct session handling
    Route::post('logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});