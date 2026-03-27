{{-- THIS FILE IS: admin/stock.blade.php --}}
@extends('layouts.app')
@section('title', 'Stock — Admin')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-brand-500 text-sm font-medium uppercase tracking-widest mb-1">Admin</p>
        <h1 class="font-display text-3xl font-700 text-white">Stock Management</h1>
    </div>
    @include('admin._nav')

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Out of stock --}}
        <div>
            <h2 class="font-display font-700 text-white mb-3 flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                Out of Stock ({{ $outOfStock->total() }})
            </h2>
            <div class="bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
                <div class="divide-y divide-dark-700">
                    @forelse($outOfStock as $product)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->getImage() }}" class="w-10 h-10 rounded-lg object-cover bg-dark-700">
                            <div>
                                <p class="text-sm text-white">{{ $product->name }}</p>
                                <p class="text-xs text-gray-600 font-mono">{{ $product->product_code }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.products.edit', $product->id) }}"
                           class="text-xs text-brand-400 hover:text-brand-300 transition-colors">Restock →</a>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-600">All products in stock ✓</div>
                    @endforelse
                </div>
                @if($outOfStock->hasPages())
                <div class="px-5 py-3 border-t border-dark-700 flex justify-end gap-2">
                    @if(!$outOfStock->onFirstPage())<a href="{{ $outOfStock->previousPageUrl() }}" class="text-xs text-gray-500 hover:text-white bg-dark-700 px-2.5 py-1.5 rounded-lg transition-colors">← Prev</a>@endif
                    @if($outOfStock->hasMorePages())<a href="{{ $outOfStock->nextPageUrl() }}" class="text-xs text-gray-500 hover:text-white bg-dark-700 px-2.5 py-1.5 rounded-lg transition-colors">Next →</a>@endif
                </div>
                @endif
            </div>
        </div>

        {{-- Low stock --}}
        <div>
            <h2 class="font-display font-700 text-white mb-3 flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></span>
                Low Stock — Under 10 ({{ $lowStock->total() }})
            </h2>
            <div class="bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
                <div class="divide-y divide-dark-700">
                    @forelse($lowStock as $product)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->getImage() }}" class="w-10 h-10 rounded-lg object-cover bg-dark-700">
                            <div>
                                <p class="text-sm text-white">{{ $product->name }}</p>
                                <p class="text-xs text-gray-600 font-mono">{{ $product->product_code }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs bg-yellow-900 text-yellow-300 px-2 py-0.5 rounded-full">{{ $product->stock_quantity }} left</span>
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                               class="text-xs text-brand-400 hover:text-brand-300 transition-colors">Edit →</a>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-600">No low stock items ✓</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection