@extends('layouts.app')
@section('title', 'Employees — Admin')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-brand-500 text-sm font-medium uppercase tracking-widest mb-1">Admin</p>
            <h1 class="font-display text-3xl font-700 text-white">Employees</h1>
        </div>
        <a href="{{ route('admin.employees.create') }}"
           class="btn-glow bg-brand-600 hover:bg-brand-500 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Employee
        </a>
    </div>
    @include('admin._nav')

    <div class="bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-600 uppercase tracking-wider border-b border-dark-700 bg-dark-900/50">
                        <th class="text-left px-5 py-3">Name</th>
                        <th class="text-left px-5 py-3">Email</th>
                        <th class="text-left px-5 py-3">Joined</th>
                        <th class="text-left px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-dark-700/50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-brand-900 rounded-full flex items-center justify-center text-brand-300 font-display font-700 text-xs">
                                    {{ strtoupper(substr($employee->name,0,1)) }}
                                </div>
                                <span class="text-white">{{ $employee->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-400">{{ $employee->email }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $employee->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST"
                                  onsubmit="return confirm('Remove employee {{ $employee->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-gray-600">No employees yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection