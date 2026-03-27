@extends('layouts.app')
@section('title','My Orders — VOLT Store')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <h1 class="font-display text-3xl font-700 text-white mb-6">My Orders</h1>
  @if($orders->isEmpty())
  <div class="text-center py-20 bg-dark-800 border border-dark-600 rounded-2xl">
    <p class="text-4xl mb-3">📦</p>
    <p class="text-gray-400 mb-4">No orders yet.</p>
    <a href="{{ route('products.index') }}" class="btn-green inline-flex items-center gap-1 text-white text-sm font-medium px-5 py-2.5 rounded-xl">Start Shopping →</a>
  </div>
  @else
  <div class="space-y-3">
    @foreach($orders as $order)
    <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5 hover:border-dark-500 transition-colors">
      <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
        <div>
          <p class="font-display font-700 text-white text-sm">Order #{{ $order->order_number }}</p>
          <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('M j, Y') }} · {{ $order->orderItems->count() }} item(s)</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $order->statusColor() }}">{{ ucfirst($order->status) }}</span>
          @if($order->payment)
          <span class="text-xs px-2.5 py-1 rounded-full {{ $order->payment->statusColor() }}">Payment: {{ ucfirst($order->payment->clearance_status) }}</span>
          @endif
        </div>
      </div>
      <div class="flex gap-2 mb-3 overflow-x-auto pb-1">
        @foreach($order->orderItems->take(4) as $item)
        <div class="w-10 h-10 rounded-lg overflow-hidden bg-dark-700 flex-shrink-0">
          <img src="{{ $item->product->getImage() }}" alt="" class="w-full h-full object-cover">
        </div>
        @endforeach
        @if($order->orderItems->count()>4)
        <div class="w-10 h-10 rounded-lg bg-dark-700 flex-shrink-0 flex items-center justify-center text-xs text-gray-500">+{{ $order->orderItems->count()-4 }}</div>
        @endif
      </div>
      <div class="flex items-center justify-between">
        <span class="font-700 text-brand-400">${{ number_format($order->total_amount,2) }}</span>
        <div class="flex items-center gap-2">
          @if($order->isCancellable())
          <form action="{{ route('orders.cancel',$order->id) }}" method="POST" onsubmit="return confirm('Cancel this order?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs text-red-500 hover:text-red-400 transition-colors">Cancel</button>
          </form>
          @endif
          <a href="{{ route('orders.show',$order->id) }}" class="text-xs bg-dark-700 hover:bg-dark-600 border border-dark-500 text-gray-300 hover:text-white px-3 py-1.5 rounded-lg transition-colors">View Details →</a>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  {{ $orders->links() }}
  @endif
</div>
@endsection