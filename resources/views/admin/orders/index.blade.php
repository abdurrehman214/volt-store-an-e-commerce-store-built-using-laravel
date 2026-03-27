@extends('layouts.app')
@section('title','Orders — Admin')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-center justify-between mb-6">
    <div>
      <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-600 hover:text-gray-400 mb-1 block">← Dashboard</a>
      <h1 class="font-display text-2xl font-700 text-white">Orders</h1>
    </div>
  </div>
  {{-- Filters --}}
  <form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="search" placeholder="Search order number…" value="{{ request('search') }}"
           class="bg-dark-700 border border-dark-500 rounded-xl px-3 py-2 text-sm text-white placeholder-gray-600 w-52">
    <select name="status" class="bg-dark-700 border border-dark-500 rounded-xl px-3 py-2 text-sm text-gray-300">
      <option value="">All Status</option>
      @foreach(['pending','cleared','dispatched','completed','cancelled'] as $s)
      <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <select name="payment_status" class="bg-dark-700 border border-dark-500 rounded-xl px-3 py-2 text-sm text-gray-300">
      <option value="">All Payments</option>
      @foreach(['pending','cleared','refunded'] as $s)
      <option value="{{ $s }}" {{ request('payment_status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn-green text-white text-sm font-medium px-4 py-2 rounded-xl">Filter</button>
    @if(request()->hasAny(['search','status','payment_status']))
    <a href="{{ route('admin.orders') }}" class="px-4 py-2 bg-dark-700 border border-dark-500 text-gray-400 text-sm rounded-xl hover:bg-dark-600 transition-colors">Clear</a>
    @endif
  </form>
  <div class="bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-dark-600 text-xs text-gray-600 uppercase tracking-wider">
          <th class="px-4 py-3 text-left font-medium">Order</th>
          <th class="px-4 py-3 text-left font-medium hidden md:table-cell">Customer</th>
          <th class="px-4 py-3 text-left font-medium hidden lg:table-cell">Payment</th>
          <th class="px-4 py-3 text-left font-medium">Status</th>
          <th class="px-4 py-3 text-right font-medium">Total</th>
          <th class="px-4 py-3 text-right font-medium">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-dark-700">
        @forelse($orders as $o)
        <tr class="hover:bg-dark-700 transition-colors">
          <td class="px-4 py-3">
            <p class="font-mono text-xs text-white">{{ $o->order_number }}</p>
            <p class="text-xs text-gray-600">{{ $o->created_at->format('M j, Y') }}</p>
          </td>
          <td class="px-4 py-3 hidden md:table-cell">
            <p class="text-white text-xs">{{ $o->user->name }}</p>
            <p class="text-gray-600 text-[10px]">{{ $o->user->email }}</p>
          </td>
          <td class="px-4 py-3 hidden lg:table-cell">
            @if($o->payment)
            <span class="text-xs px-2 py-0.5 rounded-full {{ $o->payment->statusColor() }}">{{ ucfirst($o->payment->clearance_status) }}</span>
            <p class="text-[10px] text-gray-600 mt-0.5">{{ $o->payment->methodLabel() }}</p>
            @else<span class="text-xs text-gray-600">—</span>@endif
          </td>
          <td class="px-4 py-3">
            <span class="text-xs px-2 py-0.5 rounded-full {{ $o->statusColor() }}">{{ ucfirst($o->status) }}</span>
          </td>
          <td class="px-4 py-3 text-right">
            <span class="font-700 text-brand-400 text-sm">${{ number_format($o->total_amount,2) }}</span>
          </td>
          <td class="px-4 py-3 text-right">
            <div class="flex items-center justify-end gap-1.5">
              @if($o->payment && $o->payment->requiresClearance() && !$o->payment->isCleared() && $o->status=='pending')
              <form action="{{ route('admin.payment.clear',$o->id) }}" method="POST">
                @csrf<button type="submit" class="text-[10px] bg-blue-950 border border-blue-900 text-blue-400 px-2 py-1 rounded-lg hover:bg-blue-900 transition-colors">Clear Pay</button>
              </form>
              @endif
              @if($o->isDispatchable() && !in_array($o->status,['dispatched','completed']))
              <form action="{{ route('admin.order.dispatch',$o->id) }}" method="POST">
                @csrf<button type="submit" class="text-[10px] bg-brand-950 border border-brand-900 text-brand-400 px-2 py-1 rounded-lg hover:bg-brand-900 transition-colors">Dispatch</button>
              </form>
              @endif
              @if($o->isCancellable())
              <form action="{{ route('admin.order.cancel',$o->id) }}" method="POST" onsubmit="return confirm('Cancel?')">
                @csrf @method('DELETE')<button type="submit" class="text-[10px] bg-red-950 border border-red-900 text-red-400 px-2 py-1 rounded-lg hover:bg-red-900 transition-colors">Cancel</button>
              </form>
              @endif
              <a href="{{ route('admin.orders.show',$o->id) }}" class="text-[10px] bg-dark-700 border border-dark-500 text-gray-400 px-2 py-1 rounded-lg hover:bg-dark-600 transition-colors">View</a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-12 text-gray-600">No orders found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-5">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection