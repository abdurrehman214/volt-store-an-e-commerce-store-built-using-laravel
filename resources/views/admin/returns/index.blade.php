@extends('layouts.app')
@section('title', 'Returns — Admin')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-brand-500 text-sm font-medium uppercase tracking-widest mb-1">Admin</p>
        <h1 class="font-display text-3xl font-700 text-white">Return Requests</h1>
    </div>
    @include('admin._nav')

    <div class="bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-600 uppercase tracking-wider border-b border-dark-700 bg-dark-900/50">
                        <th class="text-left px-5 py-3">Customer</th>
                        <th class="text-left px-5 py-3">Order #</th>
                        <th class="text-left px-5 py-3">Type</th>
                        <th class="text-left px-5 py-3">Requested</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700">
                    @forelse($returns as $return)
                    <tr class="hover:bg-dark-700/50 transition-colors">
                        <td class="px-5 py-3.5 text-white">{{ $return->order?->user?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-mono text-xs text-gray-400">{{ Str::limit($return->order?->order_number,14) }}</td>
                        <td class="px-5 py-3.5 text-gray-300">{{ $return->typeLabel() }}</td>
                        <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $return->requested_date }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $return->statusColor() }}">
                                {{ ucfirst($return->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <a href="{{ route('admin.returns.show', $return->id) }}"
                               class="text-xs text-brand-400 hover:text-brand-300 transition-colors">Review →</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-600">No return requests</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
        <div class="px-5 py-4 border-t border-dark-700 flex justify-end gap-2">
            @if(!$returns->onFirstPage())<a href="{{ $returns->previousPageUrl() }}" class="text-sm text-gray-400 hover:text-white bg-dark-700 px-3 py-1.5 rounded-lg transition-colors">← Prev</a>@endif
            @if($returns->hasMorePages())<a href="{{ $returns->nextPageUrl() }}" class="text-sm text-gray-400 hover:text-white bg-dark-700 px-3 py-1.5 rounded-lg transition-colors">Next →</a>@endif
        </div>
        @endif
    </div>
</div>
@endsection