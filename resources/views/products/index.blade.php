@extends('layouts.app')
@section('title','Shop — VOLT Store')
@push('styles')
<style>
.pc{animation:fadeUp 0.45s ease forwards;opacity:0;transition:all 0.3s cubic-bezier(0.25,0.46,0.45,0.94);}
.pc:nth-child(1){animation-delay:0.05s}.pc:nth-child(2){animation-delay:0.1s}.pc:nth-child(3){animation-delay:0.15s}
.pc:nth-child(4){animation-delay:0.2s}.pc:nth-child(5){animation-delay:0.25s}.pc:nth-child(6){animation-delay:0.3s}
.pc:nth-child(7){animation-delay:0.35s}.pc:nth-child(8){animation-delay:0.4s}.pc:nth-child(9){animation-delay:0.45s}
.pc:nth-child(10){animation-delay:0.5s}.pc:nth-child(11){animation-delay:0.55s}.pc:nth-child(12){animation-delay:0.6s}
.pc:hover{transform:translateY(-5px);box-shadow:0 20px 50px rgba(0,0,0,0.45),0 0 0 1px rgba(34,197,94,0.1);}
.add-bar{transform:translateY(100%);transition:transform 0.28s ease;}
.pc:hover .add-bar{transform:translateY(0);}
.zimg img{transition:transform 0.55s ease;}
.pc:hover .zimg img{transform:scale(1.08);}
.vdot{width:14px;height:14px;border-radius:50%;border:2px solid transparent;cursor:pointer;transition:all 0.18s;}
.vdot.on,.vdot:hover{border-color:#22c55e;transform:scale(1.25);}
@keyframes fadeUp{to{opacity:1;transform:translateY(0)}}
</style>
@endpush
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
      <div class="flex items-center gap-2 text-xs text-gray-600 mb-1.5">
        <a href="{{ route('home') }}" class="hover:text-gray-400">Home</a><span>/</span>
        <span class="text-gray-400">{{ request('category') ? ucfirst(request('category')) : 'Shop' }}</span>
      </div>
      <h1 class="font-display text-3xl font-700 text-white">{{ request('category') ? ucfirst(request('category')) : 'All Products' }}</h1>
      @if(request('search'))<p class="text-gray-500 text-sm mt-0.5">Results for "<span class="text-white">{{ request('search') }}</span>"</p>@endif
    </div>
    <div class="flex items-center gap-3">
      <form method="GET" id="sf" action="{{ route('products.index') }}">
        @foreach(request()->except('sort') as $k=>$v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
        <select name="sort" onchange="document.getElementById('sf').submit()" class="bg-dark-700 border border-dark-500 text-sm text-gray-300 rounded-xl px-3 py-2">
          <option value="" {{ !request('sort')?'selected':'' }}>Sort: A–Z</option>
          <option value="price_asc"  {{ request('sort')=='price_asc'?'selected':'' }}>Price ↑</option>
          <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Price ↓</option>
          <option value="newest"     {{ request('sort')=='newest'?'selected':'' }}>Newest</option>
        </select>
      </form>
      <span class="text-xs text-gray-600">{{ $products->total() }} items</span>
      <button id="ftog" class="lg:hidden flex items-center gap-1.5 bg-dark-700 border border-dark-500 text-sm text-gray-300 px-3 py-2 rounded-xl">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
        Filters
      </button>
    </div>
  </div>

  {{-- Active chips --}}
  @if(request()->hasAny(['category','min_price','max_price','search']))
  <div class="flex flex-wrap gap-2 mb-5">
    @if(request('category'))
    <a href="{{ route('products.index',request()->except('category')) }}" class="flex items-center gap-1 bg-brand-950 border border-brand-900 text-brand-400 text-xs px-3 py-1 rounded-full">{{ ucfirst(request('category')) }} ✕</a>
    @endif
    @if(request('search'))
    <a href="{{ route('products.index',request()->except('search')) }}" class="flex items-center gap-1 bg-dark-700 border border-dark-500 text-gray-400 text-xs px-3 py-1 rounded-full">"{{ request('search') }}" ✕</a>
    @endif
    <a href="{{ route('products.index') }}" class="text-xs text-gray-600 hover:text-red-400 px-2 py-1">Clear all</a>
  </div>
  @endif

  <div class="flex gap-7">
    {{-- Sidebar --}}
    <aside id="fsidebar" class="hidden lg:block w-52 flex-shrink-0 space-y-5">
      <form method="GET" action="{{ route('products.index') }}">
        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
        <div>
          <p class="font-display text-[10px] font-700 text-gray-600 uppercase tracking-widest mb-3">Category</p>
          <div class="space-y-1">
            <label class="flex items-center gap-2.5 cursor-pointer py-1">
              <input type="radio" name="category" value="" {{ !request('category')?'checked':'' }} onchange="this.form.submit()" class="accent-green-500">
              <span class="text-sm text-gray-400 hover:text-white transition-colors">All</span>
            </label>
            @foreach([['⚡','electronics'],['👕','clothing'],['📚','books'],['🏠','home'],['🏃','sports']] as [$ic,$c])
            <label class="flex items-center gap-2.5 cursor-pointer py-1">
              <input type="radio" name="category" value="{{ $c }}" {{ request('category')==$c?'checked':'' }} onchange="this.form.submit()" class="accent-green-500">
              <span class="text-sm text-gray-400 hover:text-white transition-colors">{{ $ic }} {{ ucfirst($c) }}</span>
            </label>
            @endforeach
          </div>
        </div>
        <div class="border-t border-dark-600 pt-4">
          <p class="font-display text-[10px] font-700 text-gray-600 uppercase tracking-widest mb-3">Price Range</p>
          <div class="flex gap-2 mb-2.5">
            <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" class="w-full bg-dark-700 border border-dark-500 rounded-lg px-2 py-1.5 text-xs text-white placeholder-gray-600">
            <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" class="w-full bg-dark-700 border border-dark-500 rounded-lg px-2 py-1.5 text-xs text-white placeholder-gray-600">
          </div>
          <button type="submit" class="w-full bg-dark-700 hover:bg-dark-600 border border-dark-500 text-xs text-gray-400 py-1.5 rounded-lg transition-colors">Apply</button>
        </div>
      </form>
    </aside>

    {{-- Grid --}}
    <div class="flex-1 min-w-0">
      @if($products->isEmpty())
      <div class="text-center py-24">
        <p class="text-5xl mb-4">🔍</p>
        <p class="text-gray-400 text-lg mb-2">No products found</p>
        <a href="{{ route('products.index') }}" class="text-brand-400 text-sm hover:text-brand-300">Clear filters →</a>
      </div>
      @else
      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($products as $p)
        <div class="pc group bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">
          <div class="zimg relative aspect-square overflow-hidden bg-dark-700">
            <img src="{{ $p->getImage() }}" alt="{{ $p->name }}" class="w-full h-full object-cover" loading="lazy">
            <div class="absolute top-2.5 left-2.5">
              @if(!$p->inStock())<span class="text-[10px] bg-red-700 text-white px-2 py-0.5 rounded-full font-medium">Out of Stock</span>
              @elseif($p->isLowStock())<span class="text-[10px] bg-yellow-700 text-white px-2 py-0.5 rounded-full font-medium">{{ $p->stock_quantity }} left</span>
              @endif
            </div>
            @if($p->inStock())
            <div class="add-bar absolute inset-x-0 bottom-0">
              <button data-add-id="{{ $p->id }}" onclick="addToCart({{ $p->id }})" class="w-full bg-brand-600 hover:bg-brand-500 text-white text-xs font-medium py-2.5 flex items-center justify-center gap-1.5 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Quick Add
              </button>
            </div>
            @endif
          </div>
          <div class="p-3.5">
            <p class="text-[10px] text-gray-700 font-mono mb-1">{{ $p->product_code }} · {{ ucfirst($p->category) }}</p>
            <a href="{{ route('products.show',$p->id) }}" class="font-display font-600 text-sm text-white hover:text-brand-400 transition-colors line-clamp-2 leading-snug block mb-2.5">{{ $p->name }}</a>
            <div class="flex gap-1.5 mb-3">
              @foreach(['#111827','#1f2d1e','#1c1b2e','#2d1b1b'] as $col)
              <div class="vdot {{ $loop->first?'on':'' }}" style="background:{{ $col }};{{ $loop->first?'border-color:#22c55e':'' }}"></div>
              @endforeach
            </div>
            <div class="flex items-center justify-between">
              <span class="font-display font-700 text-brand-400 text-base">${{ number_format($p->price,2) }}</span>
              <a href="{{ route('products.show',$p->id) }}" class="p-1.5 bg-dark-700 hover:bg-dark-600 rounded-lg text-gray-500 hover:text-white transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </a>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @if($products->hasPages())
      <div class="mt-8 flex justify-center">
        <div class="inline-flex items-center gap-1 bg-dark-800 border border-dark-600 rounded-2xl p-1">
          @if($products->onFirstPage())
          <span class="px-3 py-1.5 text-sm text-gray-700 cursor-not-allowed">← Prev</span>
          @else
          <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-400 hover:text-white hover:bg-dark-700 rounded-xl transition-colors">← Prev</a>
          @endif
          @foreach($products->getUrlRange(max(1,$products->currentPage()-2),min($products->lastPage(),$products->currentPage()+2)) as $page=>$url)
            @if($page==$products->currentPage())
            <span class="px-3 py-1.5 text-sm bg-brand-600 text-white rounded-xl font-medium">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-400 hover:text-white hover:bg-dark-700 rounded-xl transition-colors">{{ $page }}</a>
            @endif
          @endforeach
          @if($products->hasMorePages())
          <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-400 hover:text-white hover:bg-dark-700 rounded-xl transition-colors">Next →</a>
          @else
          <span class="px-3 py-1.5 text-sm text-gray-700 cursor-not-allowed">Next →</span>
          @endif
        </div>
      </div>
      @endif
      @endif
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.querySelectorAll('.vdot').forEach(d=>d.addEventListener('click',function(){
    this.closest('.pc').querySelectorAll('.vdot').forEach(x=>{x.classList.remove('on');x.style.borderColor='transparent';});
    this.classList.add('on');this.style.borderColor='#22c55e';
}));
document.getElementById('ftog')?.addEventListener('click',()=>{
    const s=document.getElementById('fsidebar');s.classList.toggle('hidden');s.classList.toggle('block');
});
</script>
@endpush