@extends('layouts.app')
@section('title','Products — Admin')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-center justify-between mb-6">
    <div>
      <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-600 hover:text-gray-400 mb-1 block">← Dashboard</a>
      <h1 class="font-display text-2xl font-700 text-white">Products</h1>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn-green flex items-center gap-2 text-white text-sm font-medium px-4 py-2.5 rounded-xl">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Add Product
    </a>
  </div>
  <div class="bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
      <thead><tr class="border-b border-dark-600 text-xs text-gray-600 uppercase tracking-wider">
        <th class="px-4 py-3 text-left font-medium">Product</th>
        <th class="px-4 py-3 text-left font-medium hidden md:table-cell">Code</th>
        <th class="px-4 py-3 text-left font-medium hidden lg:table-cell">Category</th>
        <th class="px-4 py-3 text-right font-medium">Price</th>
        <th class="px-4 py-3 text-right font-medium">Stock</th>
        <th class="px-4 py-3 text-left font-medium hidden md:table-cell">Status</th>
        <th class="px-4 py-3 text-right font-medium">Actions</th>
      </tr></thead>
      <tbody class="divide-y divide-dark-700">
        @foreach($products as $p)
        <tr class="hover:bg-dark-700 transition-colors">
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-lg overflow-hidden bg-dark-600 flex-shrink-0">
                <img src="{{ $p->getImage() }}" alt="" class="w-full h-full object-cover">
              </div>
              <span class="text-white text-xs line-clamp-2 max-w-[160px]">{{ $p->name }}</span>
            </div>
          </td>
          <td class="px-4 py-3 hidden md:table-cell"><span class="font-mono text-xs text-gray-500">{{ $p->product_code }}</span></td>
          <td class="px-4 py-3 hidden lg:table-cell"><span class="text-xs text-gray-400 capitalize">{{ $p->category }}</span></td>
          <td class="px-4 py-3 text-right"><span class="text-brand-400 font-700 text-sm">${{ number_format($p->price,2) }}</span></td>
          <td class="px-4 py-3 text-right"><span class="text-sm {{ $p->stock_quantity==0?'text-red-400':($p->isLowStock()?'text-yellow-400':'text-white') }} font-700">{{ $p->stock_quantity }}</span></td>
          <td class="px-4 py-3 hidden md:table-cell">
            <span class="text-[10px] px-2 py-0.5 rounded-full {{ $p->is_active?'bg-brand-950 border border-brand-900 text-brand-400':'bg-dark-600 text-gray-500' }}">{{ $p->is_active?'Active':'Inactive' }}</span>
          </td>
          <td class="px-4 py-3 text-right">
            <a href="{{ route('admin.products.edit',$p->id) }}" class="text-xs bg-dark-700 border border-dark-500 text-gray-300 px-2.5 py-1 rounded-lg hover:bg-dark-600 hover:text-white transition-colors">Edit</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-5">{{ $products->links() }}</div>
</div>
@endsection