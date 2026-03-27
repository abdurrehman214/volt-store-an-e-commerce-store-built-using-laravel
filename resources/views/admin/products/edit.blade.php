@extends('layouts.app')
@section('title','Edit Product — Admin')
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <a href="{{ route('admin.products') }}" class="text-xs text-gray-600 hover:text-gray-400 mb-4 block">← Products</a>
  <h1 class="font-display text-2xl font-700 text-white mb-6">Edit Product</h1>
  <form action="{{ route('admin.products.update',$product->id) }}" method="POST" enctype="multipart/form-data" class="bg-dark-800 border border-dark-600 rounded-2xl p-6 space-y-4">
    @csrf @method('PUT')
    <div class="grid sm:grid-cols-2 gap-4">
      <div class="sm:col-span-2">
        <label class="block text-xs text-gray-500 mb-1.5">Product Name *</label>
        <input type="text" name="name" value="{{ old('name',$product->name) }}" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1.5">Price *</label>
        <input type="number" name="price" value="{{ old('price',$product->price) }}" step="0.01" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1.5">Stock Quantity *</label>
        <input type="number" name="stock_quantity" value="{{ old('stock_quantity',$product->stock_quantity) }}" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1.5">Category *</label>
        <select name="category" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-gray-300">
          @foreach(['electronics','clothing','books','home','sports'] as $c)
          <option value="{{ $c }}" {{ old('category',$product->category)==$c?'selected':'' }}>{{ ucfirst($c) }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1.5">New Image (leave blank to keep current)</label>
        @if($product->image_url)
        <div class="mb-2"><img src="{{ $product->image_url }}" class="w-16 h-16 rounded-lg object-cover"></div>
        @endif
        <input type="file" name="image" accept="image/*" class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2 text-sm text-gray-400 file:mr-3 file:text-xs file:bg-dark-600 file:text-gray-300 file:border-0 file:rounded-lg file:px-2 file:py-1">
      </div>
      <div class="sm:col-span-2">
        <label class="block text-xs text-gray-500 mb-1.5">Description *</label>
        <textarea name="description" rows="4" required class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white resize-none">{{ old('description',$product->description) }}</textarea>
      </div>
      <div class="flex items-center gap-6">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="warranty_applicable" value="1" {{ $product->warranty_applicable?'checked':'' }} class="accent-green-500">
          <span class="text-sm text-gray-400">Warranty</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" {{ $product->is_active?'checked':'' }} class="accent-green-500">
          <span class="text-sm text-gray-400">Active</span>
        </label>
      </div>
    </div>
    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn-green text-white font-medium px-6 py-2.5 rounded-xl text-sm">Update Product</button>
      <a href="{{ route('admin.products') }}" class="px-6 py-2.5 bg-dark-700 border border-dark-500 text-gray-400 rounded-xl text-sm hover:bg-dark-600 transition-colors">Cancel</a>
    </div>
  </form>
</div>
@endsection