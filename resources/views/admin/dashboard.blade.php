@extends('layouts.app')
@section('title','Admin Dashboard — VOLT Store')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  {{-- Header --}}
  <div class="flex items-center justify-between mb-8">
    <div>
      <p class="text-brand-500 text-xs uppercase tracking-widest font-medium mb-1">Admin Panel</p>
      <h1 class="font-display text-3xl font-700 text-white">Dashboard</h1>
    </div>
    <div class="flex items-center gap-2">
      <span class="text-xs text-gray-600">{{ now()->format('D, M j Y') }}</span>
      @if(auth()->user()->isAdmin())
      <span class="text-xs bg-red-950 border border-red-900 text-red-400 px-2.5 py-1 rounded-full">Admin</span>
      @else
      <span class="text-xs bg-yellow-950 border border-yellow-900 text-yellow-400 px-2.5 py-1 rounded-full">Employee</span>
      @endif
    </div>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    @php
    $statCards = [
        ['Total Orders',      $stats['total_orders'],      'text-blue-400',   'bg-blue-950 border-blue-900'],
        ['Today\'s Orders',   $stats['orders_today'],      'text-brand-400',  'bg-brand-950 border-brand-900'],
        ['Pending Payments',  $stats['pending_payments'],  'text-yellow-400', 'bg-yellow-950 border-yellow-900'],
        ['Ready to Dispatch', $stats['ready_to_dispatch'], 'text-purple-400', 'bg-purple-950 border-purple-900'],
        ['Revenue',           '$'.number_format($stats['total_revenue'],0), 'text-brand-400', 'bg-brand-950 border-brand-900'],
    ];
    @endphp
    @foreach($statCards as [$label,$val,$tc,$bg])
    <div class="bg-dark-800 border border-dark-600 rounded-2xl p-4">
      <p class="text-xs text-gray-600 mb-2">{{ $label }}</p>
      <p class="font-display font-700 text-2xl {{ $tc }}">{{ $val }}</p>
    </div>
    @endforeach
  </div>

  {{-- Nav --}}
  <div class="flex flex-wrap gap-2 mb-7">
    @foreach([
      ['admin.orders','Orders','📋'],
      ['admin.products','Products','📦'],
      ['admin.stock','Stock','📊'],
      ['admin.returns','Returns','↩️'],
      ['admin.feedback','Reviews','⭐'],
    ] as [$r,$l,$i])
    <a href="{{ route($r) }}" class="flex items-center gap-1.5 px-4 py-2 bg-dark-700 hover:bg-dark-600 border border-dark-500 hover:border-dark-400 text-sm text-gray-300 hover:text-white rounded-xl transition-colors {{ request()->routeIs($r)?'bg-dark-600 border-dark-400 text-white':'' }}">
      {{ $i }} {{ $l }}
    </a>
    @endforeach
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.employees') }}" class="flex items-center gap-1.5 px-4 py-2 bg-dark-700 hover:bg-dark-600 border border-dark-500 hover:border-dark-400 text-sm text-gray-300 hover:text-white rounded-xl transition-colors">👥 Employees</a>
    @endif
  </div>

  <div class="grid lg:grid-cols-3 gap-6">
    {{-- Recent orders --}}
    <div class="lg:col-span-2 bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-dark-600">
        <h2 class="font-display font-700 text-white">Recent Orders</h2>
        <a href="{{ route('admin.orders') }}" class="text-xs text-brand-400 hover:text-brand-300">View all →</a>
      </div>
      <div class="divide-y divide-dark-600">
        @forelse($recentOrders as $o)
        <div class="flex items-center justify-between px-5 py-3 hover:bg-dark-700 transition-colors">
          <div class="min-w-0">
            <p class="text-sm text-white font-medium font-mono">{{ $o->order_number }}</p>
            <p class="text-xs text-gray-500">{{ $o->user->name }} · {{ $o->created_at->diffForHumans() }}</p>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs px-2 py-0.5 rounded-full {{ $o->statusColor() }}">{{ ucfirst($o->status) }}</span>
            <span class="text-sm font-700 text-brand-400">${{ number_format($o->total_amount,2) }}</span>
            <a href="{{ route('admin.orders.show',$o->id) }}" class="text-xs text-gray-600 hover:text-gray-300">→</a>
          </div>
        </div>
        @empty
        <p class="text-gray-600 text-sm text-center py-6">No orders yet.</p>
        @endforelse
      </div>
    </div>

    {{-- Side panels --}}
    <div class="space-y-4">
      {{-- Low stock --}}
      <div class="bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-dark-600">
          <h2 class="font-display font-700 text-white text-sm">⚠️ Low Stock</h2>
          <a href="{{ route('admin.stock') }}" class="text-xs text-brand-400">View all →</a>
        </div>
        <div class="divide-y divide-dark-600">
          @forelse($lowStockProducts as $p)
          <div class="flex items-center justify-between px-4 py-2.5">
            <p class="text-sm text-white truncate max-w-[140px]">{{ $p->name }}</p>
            <span class="text-xs {{ $p->stock_quantity==0?'text-red-400':'text-yellow-400' }} font-700">{{ $p->stock_quantity }}</span>
          </div>
          @empty
          <p class="text-xs text-gray-600 text-center py-4">All stocked ✓</p>
          @endforelse
        </div>
      </div>
      {{-- Return requests --}}
      <div class="bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-dark-600">
          <h2 class="font-display font-700 text-white text-sm">↩️ Return Requests</h2>
          <a href="{{ route('admin.returns') }}" class="text-xs text-brand-400">View all →</a>
        </div>
        <div class="divide-y divide-dark-600">
          @forelse($returnRequests as $r)
          <div class="flex items-center justify-between px-4 py-2.5">
            <p class="text-xs text-white">{{ $r->order->user->name ?? 'N/A' }}</p>
            <a href="{{ route('admin.returns.show',$r->id) }}" class="text-xs text-brand-400 hover:text-brand-300">Review →</a>
          </div>
          @empty
          <p class="text-xs text-gray-600 text-center py-4">No pending returns ✓</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection