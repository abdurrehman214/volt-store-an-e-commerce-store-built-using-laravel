@extends('layouts.app')
@section('title', $product->name . ' — VOLT Store')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-xs text-gray-600 mb-6">
    <a href="{{ route('home') }}" class="hover:text-gray-400">Home</a><span>/</span>
    <a href="{{ route('products.index',['category'=>$product->category]) }}" class="hover:text-gray-400 capitalize">{{ $product->category }}</a><span>/</span>
    <span class="text-gray-400 truncate max-w-xs">{{ $product->name }}</span>
  </div>

  <div class="grid lg:grid-cols-2 gap-12 mb-16">
    {{-- Image --}}
    <div class="space-y-3">
      <div class="aspect-square rounded-2xl overflow-hidden bg-dark-800 border border-dark-600">
        <img src="{{ $product->getImage() }}" alt="{{ $product->name }}" class="w-full h-full object-cover" id="main-img">
      </div>
    </div>

    {{-- Info --}}
    <div>
      <div class="flex items-start justify-between gap-4 mb-2">
        <div>
          <p class="text-xs text-gray-600 font-mono mb-1">{{ $product->product_code }} · <span class="capitalize">{{ $product->category }}</span></p>
          <h1 class="font-display text-3xl font-700 text-white leading-tight">{{ $product->name }}</h1>
        </div>
        @if($product->warranty_applicable)
        <span class="flex-shrink-0 text-xs bg-brand-950 border border-brand-900 text-brand-400 px-2.5 py-1 rounded-full">✓ Warranty</span>
        @endif
      </div>

      {{-- Rating --}}
      @if(isset($avgRating) && $avgRating > 0)
      <div class="flex items-center gap-2 mb-4">
        <div class="flex gap-0.5">
          @for($s=1;$s<=5;$s++)<svg class="w-4 h-4 {{ $s<=$avgRating?'text-yellow-400':'text-dark-400' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
        </div>
        <span class="text-sm text-gray-400">{{ number_format($avgRating,1) }} ({{ $reviews->count() }} reviews)</span>
      </div>
      @endif

      <div class="flex items-baseline gap-3 mb-5">
        <span class="font-display font-800 text-4xl text-brand-400">${{ number_format($product->price,2) }}</span>
        @if($product->inStock())
        <span class="text-xs text-brand-500 bg-brand-950 border border-brand-900 px-2 py-1 rounded-full">{{ $product->stock_quantity }} in stock</span>
        @else
        <span class="text-xs text-red-400 bg-red-950 border border-red-900 px-2 py-1 rounded-full">Out of Stock</span>
        @endif
      </div>

      <p class="text-gray-400 text-sm leading-relaxed mb-6">{{ $product->description }}</p>

      {{-- Colour variants --}}
      <div class="mb-4">
        <p class="text-xs text-gray-600 uppercase tracking-widest mb-2">Colour</p>
        <div class="flex gap-2">
          @foreach([['#111827','Midnight'],['#1f2d1e','Forest'],['#1c1b2e','Navy'],['#2d1b1b','Wine']] as [$col,$name])
          <button onclick="selectColor(this,'{{ $name }}')"
                  class="color-btn w-8 h-8 rounded-full border-2 border-transparent hover:border-brand-500 transition-all hover:scale-110 {{ $loop->first?'border-brand-500':''}}"
                  style="background:{{ $col }}" data-color="{{ $name }}" title="{{ $name }}">
          </button>
          @endforeach
        </div>
        <p class="text-xs text-gray-500 mt-1.5">Selected: <span id="selected-color" class="text-white">Midnight</span></p>
      </div>

      {{-- Qty + Add --}}
      @if($product->inStock())
      <div class="flex gap-3 mb-4">
        <div class="flex items-center bg-dark-700 border border-dark-500 rounded-xl overflow-hidden">
          <button onclick="changeQty(-1)" class="px-3 py-2.5 text-gray-400 hover:text-white hover:bg-dark-600 transition-colors text-lg leading-none">−</button>
          <input type="number" id="qty" value="1" min="1" max="{{ $product->stock_quantity }}" class="w-12 text-center bg-transparent text-white text-sm border-none py-2.5">
          <button onclick="changeQty(1)" class="px-3 py-2.5 text-gray-400 hover:text-white hover:bg-dark-600 transition-colors text-lg leading-none">+</button>
        </div>
        <button data-add-id="{{ $product->id }}"
                onclick="addToCart({{ $product->id }},+document.getElementById('qty').value,document.getElementById('selected-color').textContent)"
                class="btn-green flex-1 flex items-center justify-center gap-2 text-white font-medium py-2.5 rounded-xl text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          Add to Cart
        </button>
      </div>
      <a href="{{ route('checkout.index') }}" class="block w-full text-center border border-brand-700 text-brand-400 hover:bg-brand-950 font-medium py-2.5 rounded-xl text-sm transition-colors">Buy Now →</a>
      @else
      <button disabled class="w-full bg-dark-700 text-gray-600 font-medium py-2.5 rounded-xl text-sm cursor-not-allowed">Out of Stock</button>
      @endif

      {{-- Meta --}}
      <div class="mt-5 pt-5 border-t border-dark-600 grid grid-cols-2 gap-3 text-xs text-gray-600">
        <div><span class="text-gray-500">Code:</span> {{ $product->product_code }}</div>
        <div><span class="text-gray-500">Category:</span> <span class="capitalize">{{ $product->category }}</span></div>
        <div><span class="text-gray-500">Warranty:</span> {{ $product->warranty_applicable ? 'Included' : 'Not included' }}</div>
        <div><span class="text-gray-500">Returns:</span> 7-day policy</div>
      </div>
    </div>
  </div>

  {{-- Reviews --}}
  @if($reviews->isNotEmpty())
  <div class="mb-16">
    <h2 class="font-display text-xl font-700 text-white mb-5">Customer Reviews</h2>
    <div class="grid md:grid-cols-2 gap-4">
      @foreach($reviews as $r)
      <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
        <div class="flex items-start justify-between mb-2">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-brand-900 rounded-full flex items-center justify-center font-display font-700 text-brand-400 text-xs">{{ strtoupper(substr($r->user->name??'A',0,1)) }}</div>
            <span class="text-sm text-white">{{ $r->user->name??'Customer' }}</span>
          </div>
          <div class="flex gap-0.5">
            @for($s=1;$s<=5;$s++)<svg class="w-3.5 h-3.5 {{ $s<=$r->rating?'text-yellow-400':'text-dark-400' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
          </div>
        </div>
        <p class="text-sm text-gray-400 leading-relaxed italic">"{{ $r->comment }}"</p>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Related --}}
  @if($relatedProducts->isNotEmpty())
  <div>
    <h2 class="font-display text-xl font-700 text-white mb-5">You Might Also Like</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      @foreach($relatedProducts as $rp)
      <a href="{{ route('products.show',$rp->id) }}" class="group bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden hover:border-brand-800 transition-all hover:-translate-y-1">
        <div class="aspect-square overflow-hidden bg-dark-700">
          <img src="{{ $rp->getImage() }}" alt="{{ $rp->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        </div>
        <div class="p-3">
          <p class="font-display font-600 text-sm text-white group-hover:text-brand-400 transition-colors line-clamp-1">{{ $rp->name }}</p>
          <p class="text-brand-400 font-700 text-sm mt-1">${{ number_format($rp->price,2) }}</p>
        </div>
      </a>
      @endforeach
    </div>
  </div>
  @endif
</div>
@endsection
@push('scripts')
<script>
function changeQty(d){const i=document.getElementById('qty');i.value=Math.max(1,Math.min(+i.max,+i.value+d));}
function selectColor(btn,name){
    document.querySelectorAll('.color-btn').forEach(b=>b.classList.remove('border-brand-500'));
    btn.classList.add('border-brand-500');
    document.getElementById('selected-color').textContent=name;
}
</script>
@endpush