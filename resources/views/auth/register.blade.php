@extends('layouts.app')
@section('title','Register — VOLT Store')
@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12">
  <div class="w-full max-w-sm">
    <div class="text-center mb-8">
      <a href="{{ route('home') }}" class="font-display font-800 text-3xl text-white inline-block">VOLT<span class="text-brand-500">.</span></a>
      <h1 class="font-display text-2xl font-700 text-white mt-4 mb-1">Create account</h1>
      <p class="text-gray-500 text-sm">Join thousands of happy customers</p>
    </div>
    <div class="bg-dark-800 border border-dark-600 rounded-2xl p-6">
      <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs text-gray-500 mb-1.5">Full Name</label>
          <input type="text" name="name" value="{{ old('name') }}" required
                 class="w-full bg-dark-700 border {{ $errors->has('name')?'border-red-700':'border-dark-500' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
          @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1.5">Email Address</label>
          <input type="email" name="email" value="{{ old('email') }}" required
                 class="w-full bg-dark-700 border {{ $errors->has('email')?'border-red-700':'border-dark-500' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
          @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1.5">Password</label>
          <input type="password" name="password" required minlength="8"
                 class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600" placeholder="Min. 8 characters">
          @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1.5">Confirm Password</label>
          <input type="password" name="password_confirmation" required
                 class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
        </div>
        <label class="flex items-start gap-2.5 cursor-pointer">
          <input type="checkbox" name="terms" class="accent-green-500 mt-0.5 flex-shrink-0" required>
          <span class="text-xs text-gray-500 leading-relaxed">
            I agree to the <a href="{{ route('page.terms') }}" class="text-brand-400 hover:underline">Terms of Service</a> and <a href="{{ route('page.privacy') }}" class="text-brand-400 hover:underline">Privacy Policy</a>
          </span>
        </label>
        @error('terms')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
        <button type="submit" class="btn-green w-full text-white font-medium py-2.5 rounded-xl text-sm">Create Account</button>
      </form>
    </div>
    <p class="text-center text-sm text-gray-600 mt-5">
      Already have an account?
      <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 transition-colors ml-1">Sign in</a>
    </p>
  </div>
</div>
@endsection