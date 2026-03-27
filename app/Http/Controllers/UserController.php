<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /profile
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIX: your original UserController pointed to view('user') and
    //    view('welcome') — neither of those are real views in this project.
    //    This now shows the logged-in user's profile page.
    public function profile()
    {
        $user = auth()->user();

        // Recent orders for the profile summary
        $recentOrders = $user->orders()
            ->with(['orderItems.product', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        return view('user.profile', compact('user', 'recentOrders'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUT /profile
    // ─────────────────────────────────────────────────────────────────────────
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUT /profile/password
    // ─────────────────────────────────────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}