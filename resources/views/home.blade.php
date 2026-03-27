@extends('layouts.app')
@section('title','VOLT Store — Quality Products')

@push('styles')
<style>
/* Hero slider */
.hero-slide{position:absolute;inset:0;opacity:0;transition:opacity 1.2s cubic-bezier(0.4,0,0.2,1);}
.hero-slide.active{opacity:1;z-index:1;}
.hero-slide .content{opacity:0;transform:translateY(20px);transition:opacity 0.7s 0.4s ease,transform 0.7s 0.4s ease;}
.hero-slide.active .content{opacity:1;transform:translateY(0);}

/* Product card */
.pc{transition:all 0.35s cubic-bezier(0.25,0.46,0.45,0.94);animation:fadeUp 0.5s ease forwards;opacity:0;}
.pc:nth-child(1){animation-delay:0.05s} .pc:nth-child(2){animation-delay:0.1s}
.pc:nth-child(3){animation-delay:0.15s} .pc:nth-child(4){animation-delay:0.2s}
.pc:nth-child(5){animation-delay:0.25s} .pc:nth-child(6){animation-delay:0.3s}
.pc:nth-child(7){animation-delay:0.35s} .pc:nth-child(8){animation-delay:0.4s}
.pc:hover{transform:translateY(-6px);box-shadow:0 20px 60px rgba(0,0,0,0.5),0 0 0 1px rgba(34,197,94,0.12);}
.pc .add-overlay{transform:translateY(100%);transition:transform 0.3s ease;}
.pc:hover .add-overlay{transform:translateY(0);}
.pc .img-wrap img{transition:transform 0.6s cubic-bezier(0.25,0.46,0.45,0.94);}
.pc:hover .img-wrap img{transform:scale(1.08);}

/* Variants dot */
.var-dot{width:16px;height:16px;border-radius:50%;border:2px solid transparent;cursor:pointer;transition:all 0.2s;}
.var-dot.active,.var-dot:hover{border-color:#22c55e;transform:scale(1.2);}

/* Category cards */
.cat-card{transition:all 0.3s ease;overflow:hidden;}
.cat-card:hover{transform:scale(1.03);}
.cat-card .cat-img{transition:transform 0.5s ease;}
.cat-card:hover .cat-img{transform:scale(1.08);}

/* Marquee */
.mq-track{animation:marquee 28s linear infinite;display:flex;width:max-content;}

/* Fade up keyframe */
@keyframes fadeUp{to{opacity:1;transform:translateY(0)}}

/* Dot nav */
.dot-btn{width:6px;height:6px;border-radius:3px;background:#333;transition:all 0.3s ease;cursor:pointer;}
.dot-btn.active{width:20px;background:#22c55e;}
</style>
@endpush

@section('content')

{{-- ═══════════════ HERO SLIDER ═══════════════ --}}
<section class="relative h-[88vh] min-h-[560px] max-h-[780px] overflow-hidden bg-dark-900">

    @php
    $slides = [
        ['img'=>'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1600&q=80','tag'=>'Electronics','title'=>'Power Your World','sub'=>'Premium gadgets and tech essentials for every lifestyle.','cat'=>'electronics'],
        ['img'=>'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=1600&q=80','tag'=>'Clothing','title'=>'Dress Differently','sub'=>'Curated fashion that stands out from the ordinary.','cat'=>'clothing'],
        ['img'=>'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1600&q=80','tag'=>'Books','title'=>'Knowledge is Power','sub'=>'Expand your mind with our hand-picked collection.','cat'=>'books'],
        ['img'=>'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1600&q=80','tag'=>'Home','title'=>'Elevate Your Space','sub'=>'Beautiful essentials for the modern home.','cat'=>'home'],
        ['img'=>'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1600&q=80','tag'=>'Sports','title'=>'Push Your Limits','sub'=>'Professional gear for serious athletes.','cat'=>'sports'],
    ];
    @endphp

    @foreach($slides as $i => $slide)
    <div class="hero-slide {{ $i===0 ? 'active' : '' }}" data-slide="{{ $i }}">
        {{-- BG image --}}
        <img src="{{ $slide['img'] }}" alt="{{ $slide['tag'] }}" class="absolute inset-0 w-full h-full object-cover">
        {{-- Gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-r from-dark-900 via-dark-900/75 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-dark-900/60 via-transparent to-transparent"></div>

        {{-- Content --}}
        <div class="content relative z-10 h-full flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="max-w-xl">
                    <span class="inline-flex items-center gap-2 text-xs font-medium text-brand-400 bg-brand-950 border border-brand-900 px-3 py-1.5 rounded-full mb-5">
                        <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-pulse"></span>
                        {{ $slide['tag'] }}
                    </span>
                    <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl font-800 text-white leading-[1.02] mb-4">
                        {{ $slide['title'] }}
                    </h1>
                    <p class="text-lg text-gray-300 mb-8 leading-relaxed">{{ $slide['sub'] }}</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('products.index',['category'=>$slide['cat']]) }}"
                           class="btn-green inline-flex items-center gap-2 text-white font-medium px-7 py-3 rounded-2xl text-sm">
                            Shop {{ $slide['tag'] }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 text-white font-medium px-7 py-3 rounded-2xl text-sm border border-white/20 hover:border-white/40 hover:bg-white/5 transition-all">
                            View All
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Dot navigation --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20" id="slide-dots">
        @foreach($slides as $i=>$_)
        <button class="dot-btn {{ $i===0 ? 'active' : '' }}" data-goto="{{ $i }}"></button>
        @endforeach
    </div>

    {{-- Arrow navigation --}}
    <button id="prev-slide" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/40 hover:bg-black/70 border border-white/10 rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button id="next-slide" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/40 hover:bg-black/70 border border-white/10 rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    {{-- Progress bar --}}
    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-dark-600 z-20">
        <div id="progress-bar" class="h-full bg-brand-500 transition-none" style="width:0%"></div>
    </div>

    {{-- Stats overlay bottom right --}}
    <div class="absolute bottom-12 right-6 lg:right-12 z-20 hidden md:flex items-center gap-6">
        @foreach([['5K+','Customers'],['4.8★','Rating'],['20+','Products']] as [$n,$l])
        <div class="text-right">
            <p class="font-display font-700 text-white text-xl">{{ $n }}</p>
            <p class="text-xs text-gray-500">{{ $l }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ═══════════════ MARQUEE ═══════════════ --}}
<div class="bg-brand-600 py-2.5 overflow-hidden">
    <div class="mq-track">
        @for($i=0;$i<2;$i++)
        <span class="inline-flex items-center gap-5 px-3 text-sm font-medium text-white/90 whitespace-nowrap">
            @foreach(['Free Shipping $75+','Secure Checkout','7-Day Returns','Quality Guaranteed','Fast Dispatch','5K+ Happy Customers','New Products Weekly','Free Shipping $75+','Secure Checkout','7-Day Returns'] as $t)
            <span>{{ $t }}</span><span class="text-white/40">·</span>
            @endforeach
        </span>
        @endfor
    </div>
</div>

{{-- ═══════════════ CATEGORIES with REAL IMAGES ═══════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-brand-500 text-xs uppercase tracking-widest font-medium mb-1">Collections</p>
            <h2 class="font-display text-3xl font-700 text-white">Shop by Category</h2>
        </div>
        <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-brand-400 transition-colors flex items-center gap-1">
            All products <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    @php
    $cats = [
        ['electronics','Electronics','https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80','bg-yellow-900/70'],
        ['clothing','Clothing','https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=800&q=80','bg-blue-900/70'],
        ['books','Books','https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=800&q=80','bg-purple-900/70'],
        ['home','Home & Kitchen','https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&q=80','bg-orange-900/70'],
        ['sports','Sports','https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&q=80','bg-brand-900/70'],
    ];
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach($cats as [$slug,$label,$img,$overlay])
        <a href="{{ route('products.index',['category'=>$slug]) }}"
           class="cat-card group relative rounded-2xl overflow-hidden aspect-[3/4] border border-dark-500 hover:border-brand-800 hover:shadow-xl hover:shadow-brand-950/30">
            <img src="{{ $img }}" alt="{{ $label }}" class="cat-img absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 {{ $overlay }} bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
            <div class="absolute inset-0 flex flex-col justify-end p-4">
                <p class="font-display font-700 text-white text-base leading-tight">{{ $label }}</p>
                <p class="text-xs text-brand-400 flex items-center gap-1 mt-1 group-hover:gap-2 transition-all">
                    Shop now <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </p>
            </div>
        </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════ NEW ARRIVALS ═══════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-brand-500 text-xs uppercase tracking-widest font-medium mb-1">Just In</p>
            <h2 class="font-display text-3xl font-700 text-white">New Arrivals</h2>
        </div>
        <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-brand-400 transition-colors flex items-center gap-1">
            View all <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($products as $product)
        <div class="pc group bg-dark-800 border border-dark-600 rounded-2xl overflow-hidden">

            {{-- Image + overlay --}}
            <div class="img-wrap relative aspect-square overflow-hidden bg-dark-700">
                <img src="{{ $product->getImage() }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">

                {{-- Stock badges --}}
                <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5">
                    @if(!$product->inStock())
                        <span class="text-[10px] font-medium bg-red-700 text-white px-2 py-0.5 rounded-full">Out of Stock</span>
                    @elseif($product->isLowStock())
                        <span class="text-[10px] font-medium bg-yellow-700 text-white px-2 py-0.5 rounded-full">Only {{ $product->stock_quantity }} left</span>
                    @endif
                    @if($product->warranty_applicable)
                        <span class="text-[10px] font-medium bg-brand-800 text-brand-300 px-2 py-0.5 rounded-full">Warranty</span>
                    @endif
                </div>

                {{-- Wishlist --}}
                <button class="absolute top-2.5 right-2.5 w-7 h-7 bg-black/50 rounded-full flex items-center justify-center text-gray-400 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>

                {{-- Quick add overlay --}}
                @if($product->inStock())
                <div class="add-overlay absolute inset-x-0 bottom-0">
                    <button data-add-id="{{ $product->id }}" onclick="addToCart({{ $product->id }})"
                            class="w-full bg-brand-600 hover:bg-brand-500 text-white text-xs font-medium py-2.5 flex items-center justify-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Add to Cart
                    </button>
                </div>
                @endif
            </div>

            {{-- Card body --}}
            <div class="p-3.5">
                <p class="text-[10px] text-dark-300 font-mono tracking-wider mb-1">{{ $product->product_code }}</p>
                <a href="{{ route('products.show',$product->id) }}"
                   class="font-display font-600 text-sm text-white hover:text-brand-400 transition-colors line-clamp-2 leading-snug block mb-2">
                    {{ $product->name }}
                </a>

                {{-- Fake colour variants --}}
                <div class="flex items-center gap-1.5 mb-3">
                    @foreach(['#1a1a1a','#2d5a3d','#1e3a5f','#5a2d2d'] as $col)
                    <div class="var-dot {{ $loop->first ? 'active' : '' }}" style="background:{{ $col }};border-color:{{ $loop->first ? '#22c55e' : 'transparent' }}"></div>
                    @endforeach
                    <span class="text-xs text-gray-600 ml-1">+2</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="font-display font-700 text-brand-400 text-base">${{ number_format($product->price,2) }}</span>
                    <a href="{{ route('products.show',$product->id) }}" class="text-xs text-gray-600 hover:text-gray-300 transition-colors">Details →</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center py-20 text-gray-600">
            <p class="text-4xl mb-3">📦</p>
            <p>No products yet — run <code class="text-brand-400 text-sm">php artisan migrate:fresh --seed</code></p>
        </div>
        @endforelse
    </div>
</section>

{{-- ═══════════════ VALUE PROPS ═══════════════ --}}
<section class="bg-dark-800 border-y border-dark-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['🚚','Free Shipping','Orders over $75 ship free.'],
                ['🔒','Secure Payment','256-bit SSL encrypted.'],
                ['↩️','7-Day Returns','Hassle-free return policy.'],
                ['💬','Fast Support','Reply within 2 hours.'],
            ] as [$icon,$title,$desc])
            <div class="flex gap-3 items-start">
                <span class="text-2xl flex-shrink-0">{{ $icon }}</span>
                <div>
                    <p class="font-display font-700 text-white text-sm">{{ $title }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ TESTIMONIALS ═══════════════ --}}
@if(!empty($testimonials) && count($testimonials) > 0)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-10">
        <p class="text-brand-500 text-xs uppercase tracking-widest font-medium mb-1">Reviews</p>
        <h2 class="font-display text-3xl font-700 text-white">What Customers Say</h2>
    </div>
    <div class="grid md:grid-cols-3 gap-4">
        @foreach($testimonials as $r)
        <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5 hover:border-brand-900 transition-colors">
            <div class="flex gap-0.5 mb-3">
                @for($s=1;$s<=5;$s++)<svg class="w-3.5 h-3.5 {{ $s<=($r->rating??5) ? 'text-yellow-400' : 'text-dark-400' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
            </div>
            <p class="text-sm text-gray-400 leading-relaxed mb-4 italic">"{{ $r->comment }}"</p>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-brand-900 rounded-full flex items-center justify-center font-display font-700 text-brand-400 text-xs">{{ strtoupper(substr($r->user->name??'A',0,1)) }}</div>
                <span class="text-sm text-gray-500">{{ $r->user->name??'Customer' }}</span>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════ BOTTOM CTA ═══════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="relative bg-dark-800 border border-dark-600 rounded-3xl px-8 py-14 text-center overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-80 h-px bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>
        <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-60 h-60 bg-brand-600 rounded-full opacity-[0.04] blur-3xl pointer-events-none"></div>
        <p class="text-brand-500 text-xs uppercase tracking-widest font-medium mb-2">Ready?</p>
        <h2 class="font-display text-4xl font-700 text-white mb-3">Start Shopping Today</h2>
        <p class="text-gray-500 mb-7 max-w-sm mx-auto text-sm leading-relaxed">Browse our full catalogue. Free shipping on orders over $75.</p>
        <a href="{{ route('products.index') }}" class="btn-green inline-flex items-center gap-2 text-white font-medium px-8 py-3.5 rounded-2xl text-sm">
            Browse All Products
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script>
// ── Hero Auto-Slider ──────────────────────────────────────────────────────
(function(){
    const slides = document.querySelectorAll('.hero-slide');
    const dots   = document.querySelectorAll('.dot-btn');
    const bar    = document.getElementById('progress-bar');
    let current  = 0;
    let timer, barTimer, barVal = 0;
    const DURATION = 5000;

    function goTo(n) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');
        current = (n + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current].classList.add('active');
        resetBar();
    }

    function resetBar() {
        clearInterval(barTimer);
        barVal = 0;
        bar.style.transition = 'none';
        bar.style.width = '0%';
        requestAnimationFrame(() => {
            bar.style.transition = `width ${DURATION}ms linear`;
            bar.style.width = '100%';
        });
    }

    function startAuto() {
        clearInterval(timer);
        timer = setInterval(() => goTo(current + 1), DURATION);
    }

    document.getElementById('next-slide').addEventListener('click', () => { clearInterval(timer); goTo(current + 1); startAuto(); });
    document.getElementById('prev-slide').addEventListener('click', () => { clearInterval(timer); goTo(current - 1); startAuto(); });
    dots.forEach(d => d.addEventListener('click', () => { clearInterval(timer); goTo(+d.dataset.goto); startAuto(); }));

    // Pause on hover
    const hero = document.querySelector('.hero-slide').parentElement;
    hero.addEventListener('mouseenter', () => clearInterval(timer));
    hero.addEventListener('mouseleave', startAuto);

    goTo(0);
    startAuto();
})();

// ── Variant dot switcher ─────────────────────────────────────────────────
document.querySelectorAll('.var-dot').forEach(dot => {
    dot.addEventListener('click', function() {
        const card = this.closest('.pc');
        card.querySelectorAll('.var-dot').forEach(d => {
            d.classList.remove('active');
            d.style.borderColor = 'transparent';
        });
        this.classList.add('active');
        this.style.borderColor = '#22c55e';
    });
});
</script>
@endpush