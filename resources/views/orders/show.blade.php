@extends('layouts.app')
@section('title','Order Details — VOLT Store')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('orders.my') }}" class="text-gray-500 hover:text-white transition-colors">← My Orders</a>
    <span class="text-gray-700">/</span>
    <span class="text-gray-400 text-sm">{{ $order->order_number }}</span>
  </div>
  <div class="bg-dark-800 border border-dark-600 rounded-2xl p-6 mb-4">
    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
      <div>
        <h1 class="font-display text-2xl font-700 text-white">Order Details</h1>
        <p class="text-xs text-gray-500 mt-1">Placed {{ $order->created_at->format('F j, Y') }}</p>
      </div>
      <span class="text-sm px-3 py-1.5 rounded-full font-medium {{ $order->statusColor() }}">{{ ucfirst($order->status) }}</span>
    </div>
    <div class="grid sm:grid-cols-2 gap-4 mb-5 text-sm">
      <div class="bg-dark-700 rounded-xl p-3.5">
        <p class="text-xs text-gray-600 mb-1">Order Number</p>
        <p class="text-white font-mono text-xs">{{ $order->order_number }}</p>
      </div>
      @if($order->payment)
      <div class="bg-dark-700 rounded-xl p-3.5">
        <p class="text-xs text-gray-600 mb-1">Payment</p>
        <p class="text-white text-xs">{{ $order->payment->methodLabel() }}</p>
        <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $order->payment->statusColor() }}">{{ ucfirst($order->payment->clearance_status) }}</span>
      </div>
      @endif
      @if($order->delivery)
      <div class="bg-dark-700 rounded-xl p-3.5">
        <p class="text-xs text-gray-600 mb-1">Delivery</p>
        <p class="text-white text-xs">{{ $order->delivery->statusLabel() }}</p>
        @if($order->delivery->tracking_number)<p class="text-gray-500 text-[10px] font-mono">{{ $order->delivery->tracking_number }}</p>@endif
      </div>
      @endif
      <div class="bg-dark-700 rounded-xl p-3.5">
        <p class="text-xs text-gray-600 mb-1">Total</p>
        <p class="text-brand-400 font-700 text-lg">${{ number_format($order->total_amount,2) }}</p>
      </div>
    </div>
    <h2 class="font-display font-700 text-white text-base mb-3">Items</h2>
    <div class="space-y-3">
      @foreach($order->orderItems as $item)
      <div class="flex gap-3 items-center p-3 bg-dark-700 rounded-xl">
        <div class="w-12 h-12 rounded-lg overflow-hidden bg-dark-600 flex-shrink-0">
          <img src="{{ $item->product->getImage() }}" alt="" class="w-full h-full object-cover">
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm text-white font-medium truncate">{{ $item->product->name }}</p>
          <p class="text-xs text-gray-500">× {{ $item->quantity }} · ${{ number_format($item->price_at_purchase,2) }} each</p>
        </div>
        <span class="text-brand-400 font-700 text-sm">${{ number_format($item->lineTotal(),2) }}</span>
      </div>
      @endforeach
    </div>
    <div class="mt-4 pt-4 border-t border-dark-600 flex justify-end">
      <p class="font-700 text-white text-base">Total: <span class="text-brand-400">${{ number_format($order->total_amount,2) }}</span></p>
    </div>
  </div>
  <div class="flex flex-wrap gap-3">
    @if($order->isCancellable())
    <form action="{{ route('orders.cancel',$order->id) }}" method="POST" onsubmit="return confirm('Cancel this order?')">
      @csrf @method('DELETE')
      <button type="submit" class="px-5 py-2.5 bg-red-950 border border-red-900 text-red-400 hover:bg-red-900 rounded-xl text-sm transition-colors">Cancel Order</button>
    </form>
    @endif
    @if($order->delivery && $order->delivery->isDelivered())
    <a href="{{ route('orders.return.create',$order->id) }}" class="px-5 py-2.5 bg-dark-700 border border-dark-500 text-gray-300 hover:text-white hover:bg-dark-600 rounded-xl text-sm transition-colors">Request Return</a>
    @endif
    <a href="{{ route('orders.my') }}" class="px-5 py-2.5 bg-dark-700 border border-dark-500 text-gray-300 hover:text-white hover:bg-dark-600 rounded-xl text-sm transition-colors">← Back to Orders</a>
  </div>
</div>
@endsection