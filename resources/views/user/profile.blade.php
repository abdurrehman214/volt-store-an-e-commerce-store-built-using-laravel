@extends('layouts.app')
@section('title','Profile — VOLT Store')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <h1 class="font-display text-3xl font-700 text-white mb-7">My Profile</h1>
  <div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-1">
      <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5 text-center">
        <div class="w-16 h-16 bg-brand-900 rounded-full flex items-center justify-center font-display font-700 text-brand-400 text-2xl mx-auto mb-3">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
        <p class="font-display font-700 text-white">{{ auth()->user()->name }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
        <span class="mt-2 inline-block text-[10px] px-2 py-0.5 rounded-full bg-brand-950 border border-brand-900 text-brand-400">{{ ucfirst(auth()->user()->role) }}</span>
        <div class="mt-4 pt-4 border-t border-dark-600 text-sm text-gray-500">
          <p>Member since {{ auth()->user()->created_at->format('M Y') }}</p>
          <p class="mt-1">{{ $recentOrders->count() }} recent orders</p>
        </div>
      </div>
    </div>
    <div class="md:col-span-2 space-y-5">
      <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
        <h2 class="font-display font-700 text-white text-base mb-4">Update Profile</h2>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-3">
          @csrf @method('PUT')
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Full Name</label>
            <input type="text" name="name" value="{{ old('name',auth()->user()->name) }}" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email',auth()->user()->email) }}" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white">
          </div>
          <button type="submit" class="btn-green text-white font-medium px-5 py-2.5 rounded-xl text-sm">Save Changes</button>
        </form>
      </div>
      <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
        <h2 class="font-display font-700 text-white text-base mb-4">Change Password</h2>
        <form action="{{ route('profile.password') }}" method="POST" class="space-y-3">
          @csrf @method('PUT')
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Current Password</label>
            <input type="password" name="current_password" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">New Password</label>
            <input type="password" name="password" required minlength="8" class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white" placeholder="Min. 8 characters">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Confirm New Password</label>
            <input type="password" name="password_confirmation" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white">
          </div>
          <button type="submit" class="bg-dark-700 hover:bg-dark-600 border border-dark-500 text-gray-300 hover:text-white font-medium px-5 py-2.5 rounded-xl text-sm transition-colors">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection