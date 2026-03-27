@extends('layouts.app')
@section('title', 'Add Employee — Admin')
@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.employees') }}" class="text-sm text-gray-500 hover:text-brand-400 transition-colors flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Employees
        </a>
        <h1 class="font-display text-3xl font-700 text-white">Add Employee</h1>
    </div>

    <form action="{{ route('admin.employees.store') }}" method="POST"
          class="bg-dark-800 border border-dark-600 rounded-2xl p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-400 mb-1.5">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-white text-sm focus:border-brand-500 transition-colors">
            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-white text-sm focus:border-brand-500 transition-colors">
            @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-1.5">Password</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-white text-sm focus:border-brand-500 transition-colors">
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-1.5">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-white text-sm focus:border-brand-500 transition-colors">
        </div>
        <div class="flex gap-3 pt-2 border-t border-dark-700">
            <button type="submit"
                    class="btn-glow bg-brand-600 hover:bg-brand-500 text-white font-medium px-6 py-2.5 rounded-xl transition-all">
                Create Employee
            </button>
            <a href="{{ route('admin.employees') }}"
               class="bg-dark-700 hover:bg-dark-600 text-gray-400 hover:text-white font-medium px-6 py-2.5 rounded-xl transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection