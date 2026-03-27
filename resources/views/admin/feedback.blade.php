@extends('layouts.app')
@section('title', 'Feedback — Admin')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-brand-500 text-sm font-medium uppercase tracking-widest mb-1">Admin</p>
        <h1 class="font-display text-3xl font-700 text-white">Customer Feedback</h1>
    </div>
    @include('admin._nav')

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($feedbacks as $feedback)
        <div class="bg-dark-800 border {{ $feedback->is_visible ? 'border-dark-600' : 'border-dark-700 opacity-60' }} rounded-2xl p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-brand-900 rounded-full flex items-center justify-center text-brand-300 font-display font-700 text-xs">
                        {{ strtoupper(substr($feedback->user?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm text-white font-medium">{{ $feedback->user?->name ?? 'Customer' }}</p>
                        <p class="text-xs text-gray-600">{{ $feedback->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $feedback->ratingColor() }}">
                    {{ $feedback->stars() }}
                </span>
            </div>
            <p class="text-sm text-gray-400 leading-relaxed mb-4">{{ $feedback->comment }}</p>
            <div class="flex items-center justify-between">
                <span class="text-xs {{ $feedback->is_visible ? 'text-brand-400' : 'text-gray-600' }}">
                    {{ $feedback->is_visible ? '● Visible' : '○ Hidden' }}
                </span>
                <form action="{{ route('admin.feedback.toggle', $feedback->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-white border border-dark-500 hover:border-dark-400 px-3 py-1 rounded-lg transition-all">
                        {{ $feedback->is_visible ? 'Hide' : 'Show' }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 text-gray-600">No customer feedback yet</div>
        @endforelse
    </div>

    @if($feedbacks->hasPages())
    <div class="mt-6 flex justify-center gap-2">
        @if(!$feedbacks->onFirstPage())<a href="{{ $feedbacks->previousPageUrl() }}" class="px-4 py-2 text-sm bg-dark-700 hover:bg-dark-600 text-gray-400 hover:text-white rounded-lg transition-colors">← Prev</a>@endif
        @if($feedbacks->hasMorePages())<a href="{{ $feedbacks->nextPageUrl() }}" class="px-4 py-2 text-sm bg-dark-700 hover:bg-dark-600 text-gray-400 hover:text-white rounded-lg transition-colors">Next →</a>@endif
    </div>
    @endif
</div>
@endsection