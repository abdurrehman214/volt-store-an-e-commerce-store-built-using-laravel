@extends('layouts.app')
@section('title','Login — VOLT Store')
@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12">
  <div class="w-full max-w-sm">
    <div class="text-center mb-8">
      <a href="{{ route('home') }}" class="font-display font-800 text-3xl text-white inline-block">VOLT<span class="text-brand-500">.</span></a>
      <h1 class="font-display text-2xl font-700 text-white mt-4 mb-1">Welcome back</h1>
      <p class="text-gray-500 text-sm">Sign in to your account</p>
    </div>
    <div class="bg-dark-800 border border-dark-600 rounded-2xl p-6">
      @if(session('status'))
      <div class="bg-brand-950 border border-brand-900 text-brand-400 text-xs px-3 py-2.5 rounded-xl mb-4">{{ session('status') }}</div>
      @endif
      <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs text-gray-500 mb-1.5">Email Address</label>
          <input type="email" name="email" value="{{ old('email') }}" required autofocus
                 class="w-full bg-dark-700 border {{ $errors->has('email')?'border-red-700':'border-dark-500' }} rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
          @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="text-xs text-gray-500">Password</label>
            <a href="{{ route('password.request') }}" class="text-xs text-brand-500 hover:text-brand-400 transition-colors">Forgot?</a>
          </div>
          <input type="password" name="password" required
                 class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
        </div>
        <label class="flex items-center gap-2.5 cursor-pointer">
          <input type="checkbox" name="remember" class="accent-green-500 rounded">
          <span class="text-sm text-gray-400">Remember me</span>
        </label>
        <button type="submit" class="btn-green w-full text-white font-medium py-2.5 rounded-xl text-sm">Sign In</button>
      </form>
    </div>
    <p class="text-center text-sm text-gray-600 mt-5">
      Don't have an account?
      <a href="{{ route('register') }}" class="text-brand-400 hover:text-brand-300 transition-colors ml-1">Create one</a>
    </p>
    {{-- Admin hint --}}
    <p class="text-center text-xs text-gray-800 mt-2 hover:text-gray-600 transition-colors cursor-default" title="Admin access">
      Admin: ar@gmail.com
    </p>
  </div>
</div>
@endsection