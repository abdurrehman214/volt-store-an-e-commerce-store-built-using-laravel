@extends('layouts.app')
@section('title', 'Return Request — Admin')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.returns') }}" class="text-sm text-gray-500 hover:text-brand-400 transition-colors flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Returns
        </a>
        <h1 class="font-display text-3xl font-700 text-white">Return Request</h1>
    </div>

    <div class="space-y-4">
        {{-- Return details --}}
        <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
            <div class="grid sm:grid-cols-2 gap-4 text-sm mb-4">
                <div><p class="text-gray-500 mb-0.5">Customer</p><p class="text-white">{{ $return->order?->user?->name }}</p></div>
                <div><p class="text-gray-500 mb-0.5">Order #</p><p class="text-white font-mono text-xs">{{ $return->order?->order_number }}</p></div>
                <div><p class="text-gray-500 mb-0.5">Type</p><p class="text-white">{{ $return->typeLabel() }}</p></div>
                <div><p class="text-gray-500 mb-0.5">Requested</p><p class="text-white">{{ $return->requested_date }}</p></div>
                <div><p class="text-gray-500 mb-0.5">Status</p>
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $return->statusColor() }}">{{ ucfirst($return->status) }}</span>
                </div>
                <div>
                    <p class="text-gray-500 mb-0.5">Return Policy</p>
                    @if($isWithinPolicy)
                        <span class="text-xs bg-brand-900 text-brand-400 px-2.5 py-1 rounded-full">Within 7-day window</span>
                    @else
                        <span class="text-xs bg-red-900 text-red-400 px-2.5 py-1 rounded-full">Outside policy ({{ $daysSinceDelivery }} days)</span>
                    @endif
                </div>
            </div>
            <div>
                <p class="text-gray-500 text-sm mb-1">Reason</p>
                <p class="text-white text-sm bg-dark-700 rounded-xl p-3">{{ $return->reason }}</p>
            </div>
        </div>

        {{-- Process return --}}
        @if($return->status === 'requested')
        <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
            <h2 class="font-display font-700 text-white mb-4">Process Request</h2>
            <form action="{{ route('admin.returns.process', $return->id) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="submit" name="action" value="approve"
                            class="flex-1 bg-brand-600 hover:bg-brand-500 text-white font-medium py-2.5 rounded-xl transition-colors">
                        ✓ Approve Return
                    </button>
                    <button type="submit" name="action" value="reject"
                            onclick="return confirm('Reject this return request?')"
                            class="flex-1 bg-red-900 hover:bg-red-800 text-red-300 font-medium py-2.5 rounded-xl transition-colors">
                        ✕ Reject
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- Order items --}}
        <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
            <h2 class="font-display font-700 text-white mb-4">Items in Order</h2>
            <div class="space-y-3">
                @foreach($return->order?->orderItems ?? [] as $item)
                <div class="flex items-center gap-3">
                    <img src="{{ $item->product?->getImage() }}" class="w-12 h-12 rounded-lg object-cover bg-dark-700">
                    <div>
                        <p class="text-sm text-white">{{ $item->product?->name }}</p>
                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} · ${{ number_format($item->price_at_purchase,2) }} each</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection