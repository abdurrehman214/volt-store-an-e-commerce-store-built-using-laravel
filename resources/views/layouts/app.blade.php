<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VOLT Store')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: { extend: {
            fontFamily: { display: ['Syne','sans-serif'], body: ['DM Sans','sans-serif'] },
            colors: {
                brand:  { 50:'#f0fdf4',100:'#dcfce7',300:'#86efac',400:'#4ade80',500:'#22c55e',600:'#16a34a',700:'#15803d',800:'#166534',900:'#14532d',950:'#052e16' },
                dark:   { 900:'#080808',800:'#0f0f0f',700:'#161616',600:'#1e1e1e',500:'#272727',400:'#333333',300:'#444444' },
                volt:   { accent:'#22c55e', glow:'rgba(34,197,94,0.15)' }
            },
            animation: {
                'fade-up':    'fadeUp 0.5s ease forwards',
                'fade-in':    'fadeIn 0.4s ease forwards',
                'toast-in':   'toastIn 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards',
                'marquee':    'marquee 30s linear infinite',
                'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                'shimmer':    'shimmer 1.5s infinite',
            },
            keyframes: {
                fadeUp:     { from:{ opacity:0, transform:'translateY(16px)' }, to:{ opacity:1, transform:'translateY(0)' } },
                fadeIn:     { from:{ opacity:0 }, to:{ opacity:1 } },
                toastIn:    { from:{ opacity:0, transform:'translateX(110%) scale(0.9)' }, to:{ opacity:1, transform:'translateX(0) scale(1)' } },
                marquee:    { from:{ transform:'translateX(0)' }, to:{ transform:'translateX(-50%)' } },
                pulseSoft:  { '0%,100%':{ opacity:1 }, '50%':{ opacity:0.5 } },
                shimmer:    { '0%':{ backgroundPosition:'-200% 0' }, '100%':{ backgroundPosition:'200% 0' } },
            }
        }}
    }
    </script>
    <style>
        *{ font-family:'DM Sans',sans-serif; }
        h1,h2,h3,.font-display{ font-family:'Syne',sans-serif; }
        ::-webkit-scrollbar{ width:5px; }
        ::-webkit-scrollbar-track{ background:#0f0f0f; }
        ::-webkit-scrollbar-thumb{ background:#16a34a; border-radius:3px; }
        .nav-link{ position:relative; }
        .nav-link::after{ content:''; position:absolute; bottom:-2px; left:0; right:0; height:1.5px; background:#22c55e; transform:scaleX(0); transform-origin:left; transition:transform 0.25s ease; }
        .nav-link:hover::after,.nav-link.active::after{ transform:scaleX(1); }
        .btn-green{ background:#16a34a; transition:all 0.2s ease; }
        .btn-green:hover{ background:#15803d; box-shadow:0 0 24px rgba(34,197,94,0.3),0 0 48px rgba(34,197,94,0.1); }
        .card-hover{ transition:all 0.3s cubic-bezier(0.25,0.46,0.45,0.94); }
        .card-hover:hover{ transform:translateY(-4px); border-color:#166534; box-shadow:0 16px 40px rgba(0,0,0,0.4),0 0 0 1px rgba(34,197,94,0.1); }
        .img-zoom img{ transition:transform 0.6s cubic-bezier(0.25,0.46,0.45,0.94); }
        .img-zoom:hover img{ transform:scale(1.07); }
        input:focus,select:focus,textarea:focus{ outline:none; box-shadow:0 0 0 2px rgba(34,197,94,0.35); }
        .glass{ background:rgba(15,15,15,0.85); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); }
        .shimmer-bg{ background:linear-gradient(90deg,#161616 25%,#1e1e1e 50%,#161616 75%); background-size:200% 100%; animation:shimmer 1.5s infinite; }
        .seller-link{ opacity:0.25; font-size:0.65rem; transition:opacity 0.2s; }
        .seller-link:hover{ opacity:0.7; }
        @media(prefers-reduced-motion:reduce){ *{ animation-duration:0.01ms!important; } }
    </style>
    @stack('styles')
</head>
<body class="bg-dark-900 text-white min-h-screen flex flex-col antialiased">

{{-- NAVBAR --}}
<nav class="glass border-b border-dark-600 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="font-display font-800 text-xl tracking-tight flex items-center gap-1">
                <span class="text-white">VOLT</span><span class="text-brand-500 text-2xl leading-none">.</span>
            </a>

            {{-- Desktop links --}}
            <div class="hidden md:flex items-center gap-7">
                <a href="{{ route('home') }}" class="nav-link text-sm text-gray-400 hover:text-white transition-colors {{ request()->routeIs('home') ? 'active !text-white' : '' }}">Home</a>
                <a href="{{ route('products.index') }}" class="nav-link text-sm text-gray-400 hover:text-white transition-colors {{ request()->routeIs('products.*') ? 'active !text-white' : '' }}">Shop</a>
                <div class="relative group">
                    <button class="nav-link text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1">
                        Categories <svg class="w-3 h-3 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="absolute top-full left-0 mt-3 w-44 bg-dark-700 border border-dark-500 rounded-2xl shadow-2xl shadow-black/60 overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-1 group-hover:translate-y-0">
                        @foreach([['⚡','electronics'],['👕','clothing'],['📚','books'],['🏠','home'],['🏃','sports']] as [$icon,$cat])
                        <a href="{{ route('products.index', ['category'=>$cat]) }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-dark-600 transition-colors">
                            <span class="text-base">{{ $icon }}</span> {{ ucfirst($cat) }}
                        </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('contact') }}" class="nav-link text-sm text-gray-400 hover:text-white transition-colors">Contact</a>
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-2.5">
                {{-- Search --}}
                <form action="{{ route('products.search') }}" method="GET" class="hidden sm:block">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search…" value="{{ request('search') }}"
                               class="bg-dark-700 border border-dark-500 text-sm text-white placeholder-gray-600 rounded-xl pl-8 pr-3 py-1.5 w-36 focus:w-52 transition-all duration-300">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    </div>
                </form>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    @php $cartCount = session('cart_count', count(session('cart', []))); @endphp
                    <span id="cart-badge" class="{{ $cartCount > 0 ? '' : 'hidden' }} absolute -top-0.5 -right-0.5 bg-brand-500 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-display font-700 leading-none">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="hidden sm:block text-sm text-gray-400 hover:text-white transition-colors px-1">Login</a>
                    <a href="{{ route('register') }}" class="btn-green text-white text-sm font-medium px-4 py-1.5 rounded-xl">Sign Up</a>
                    {{-- Subtle seller link for guests --}}
                    <a href="{{ route('login') }}" class="seller-link text-gray-600 hover:text-gray-400 hidden lg:block ml-1" title="Sell on VOLT">Sell</a>
                @else
                    {{-- User dropdown --}}
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-sm text-gray-400 hover:text-white transition-colors ml-1">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-display font-700
                                {{ auth()->user()->isAdmin() ? 'bg-red-900 text-red-300' : (auth()->user()->isEmployee() ? 'bg-yellow-900 text-yellow-300' : 'bg-brand-900 text-brand-400') }}">
                                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                            </div>
                            <span class="hidden sm:block max-w-[80px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-3 h-3 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute right-0 top-full mt-3 w-52 bg-dark-700 border border-dark-500 rounded-2xl shadow-2xl shadow-black/60 overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-1 group-hover:translate-y-0">
                            <div class="px-4 py-3 border-b border-dark-500">
                                <p class="text-xs text-gray-600 mb-0.5">Signed in as</p>
                                <p class="text-sm text-white font-medium truncate">{{ auth()->user()->email }}</p>
                                <span class="mt-1.5 inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full font-medium
                                    {{ auth()->user()->isAdmin() ? 'bg-red-950 text-red-400 border border-red-900' : (auth()->user()->isEmployee() ? 'bg-yellow-950 text-yellow-400 border border-yellow-900' : 'bg-brand-950 text-brand-400 border border-brand-900') }}">
                                    <span class="w-1 h-1 rounded-full bg-current"></span>
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </div>
                            @if(auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-dark-600 transition-colors">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                Admin Panel
                            </a>
                            @endif
                            @if(auth()->user()->isCustomer())
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-dark-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profile
                            </a>
                            <a href="{{ route('orders.my') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-dark-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                My Orders
                            </a>
                            @endif
                            <div class="border-t border-dark-500">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:text-red-400 hover:bg-dark-600 transition-colors text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest

                {{-- Mobile toggle --}}
                <button id="mob-toggle" class="md:hidden p-2 text-gray-500 hover:text-white transition-colors ml-1">
                    <svg id="mob-open"  class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg id="mob-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mob-menu" class="md:hidden overflow-hidden" style="max-height:0;opacity:0;transition:max-height 0.35s ease,opacity 0.3s ease">
            <div class="py-3 space-y-0.5 border-t border-dark-600">
                <a href="{{ route('home') }}"           class="block px-3 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-dark-700 rounded-xl transition-colors">Home</a>
                <a href="{{ route('products.index') }}" class="block px-3 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-dark-700 rounded-xl transition-colors">Shop All</a>
                @foreach([['⚡','electronics'],['👕','clothing'],['📚','books'],['🏠','home'],['🏃','sports']] as [$icon,$cat])
                <a href="{{ route('products.index', ['category'=>$cat]) }}" class="flex items-center gap-2 pl-6 pr-3 py-2 text-sm text-gray-500 hover:text-white hover:bg-dark-700 rounded-xl transition-colors">
                    {{ $icon }} {{ ucfirst($cat) }}
                </a>
                @endforeach
                <a href="{{ route('contact') }}" class="block px-3 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-dark-700 rounded-xl transition-colors">Contact</a>
                <form action="{{ route('products.search') }}" method="GET" class="px-3 pt-2 pb-3">
                    <input type="text" name="search" placeholder="Search products…" class="w-full bg-dark-700 border border-dark-500 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600">
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- FLASH MESSAGES --}}
@if(session('success') || session('error') || session('info') || $errors->any())
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 space-y-2 animate-fade-up">
    @if(session('success'))
    <div class="flex items-center gap-3 bg-brand-950 border border-brand-900 text-brand-400 px-4 py-3 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-950 border border-red-900 text-red-400 px-4 py-3 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div class="flex items-center gap-3 bg-blue-950 border border-blue-900 text-blue-400 px-4 py-3 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        {{ session('info') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-950 border border-red-900 text-red-400 px-4 py-3 rounded-xl text-sm">
        <p class="font-medium mb-1">Please fix the following:</p>
        <ul class="list-disc list-inside space-y-0.5 text-red-400/80">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif
</div>
@endif

<main class="flex-grow">@yield('content')</main>

{{-- FOOTER --}}
<footer class="bg-dark-800 border-t border-dark-600 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
            <div class="col-span-2 lg:col-span-1">
                <a href="{{ route('home') }}" class="font-display font-800 text-xl text-white">VOLT<span class="text-brand-500">.</span></a>
                <p class="mt-3 text-sm text-gray-600 leading-relaxed">Quality products, fast delivery, fair prices.</p>
                <form id="newsletter-form" class="mt-4 flex gap-2">
                    @csrf
                    <input type="email" name="email" placeholder="Your email" class="flex-1 min-w-0 bg-dark-700 border border-dark-500 rounded-xl px-3 py-2 text-sm text-white placeholder-gray-600">
                    <button type="submit" class="btn-green text-white text-sm font-medium px-3 py-2 rounded-xl whitespace-nowrap">Go</button>
                </form>
                {{-- Seller CTA - subtle --}}
                <div class="mt-4">
                    <a href="{{ route('login') }}" class="seller-link text-gray-700 hover:text-gray-500">Sell on VOLT →</a>
                </div>
            </div>
            <div>
                <h4 class="font-display text-xs font-700 text-gray-500 uppercase tracking-widest mb-4">Shop</h4>
                <ul class="space-y-2">
                    @foreach(['electronics','clothing','books','home','sports'] as $c)
                    <li><a href="{{ route('products.index',['category'=>$c]) }}" class="text-sm text-gray-600 hover:text-brand-400 transition-colors capitalize">{{ ucfirst($c) }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-display text-xs font-700 text-gray-500 uppercase tracking-widest mb-4">Help</h4>
                <ul class="space-y-2">
                    @foreach([['faq','FAQ'],['page.shipping','Shipping'],['page.returns','Returns'],['contact','Contact']] as [$r,$l])
                    <li><a href="{{ route($r) }}" class="text-sm text-gray-600 hover:text-brand-400 transition-colors">{{ $l }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-display text-xs font-700 text-gray-500 uppercase tracking-widest mb-4">Company</h4>
                <ul class="space-y-2">
                    @foreach([['page.about','About'],['page.careers','Careers'],['page.privacy','Privacy'],['page.terms','Terms']] as [$r,$l])
                    <li><a href="{{ route($r) }}" class="text-sm text-gray-600 hover:text-brand-400 transition-colors">{{ $l }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-display text-xs font-700 text-gray-500 uppercase tracking-widest mb-4">Account</h4>
                <ul class="space-y-2">
                    @guest
                    <li><a href="{{ route('login') }}"    class="text-sm text-gray-600 hover:text-brand-400 transition-colors">Login</a></li>
                    <li><a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-brand-400 transition-colors">Register</a></li>
                    @else
                    <li><a href="{{ route('orders.my') }}" class="text-sm text-gray-600 hover:text-brand-400 transition-colors">My Orders</a></li>
                    <li><a href="{{ route('profile') }}"   class="text-sm text-gray-600 hover:text-brand-400 transition-colors">Profile</a></li>
                    @if(auth()->user()->isStaff())
                    <li><a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-brand-400 transition-colors">Admin Panel</a></li>
                    @endif
                    @endguest
                </ul>
            </div>
        </div>
        <div class="mt-10 pt-6 border-t border-dark-700 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-gray-700">© {{ date('Y') }} VOLT Store. All rights reserved.</p>
            <div class="flex items-center gap-4 text-xs text-gray-700">
                <span>🔒 Secure Checkout</span>
                <span>📦 Free Shipping $75+</span>
                <span>↩️ 7-Day Returns</span>
            </div>
        </div>
    </div>
</footer>

{{-- TOAST CONTAINER --}}
<div id="toast-container" class="fixed bottom-5 right-5 z-[200] space-y-2.5 pointer-events-none max-w-xs w-full"></div>

<script>
// Mobile menu
document.getElementById('mob-toggle').addEventListener('click',function(){
    const m=document.getElementById('mob-menu'),o=document.getElementById('mob-open'),c=document.getElementById('mob-close'),open=m.style.maxHeight!=='0px'&&m.style.maxHeight!=='';
    m.style.maxHeight=open?'0px':'480px'; m.style.opacity=open?'0':'1';
    o.classList.toggle('hidden',!open); c.classList.toggle('hidden',open);
});

// Toast system
function showToast(msg,type='success'){
    const p={success:'bg-brand-950 border-brand-900 text-brand-400',error:'bg-red-950 border-red-900 text-red-400',info:'bg-blue-950 border-blue-900 text-blue-400'};
    const i={success:'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z',error:'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z',info:'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z'};
    const t=document.createElement('div');
    t.className=`pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl border text-sm shadow-2xl animate-toast-in ${p[type]||p.success}`;
    t.innerHTML=`<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="${i[type]||i.success}" clip-rule="evenodd"/></svg><span class="flex-1">${msg}</span><button onclick="this.parentElement.remove()" class="opacity-40 hover:opacity-80 transition-opacity ml-1 flex-shrink-0">✕</button>`;
    document.getElementById('toast-container').appendChild(t);
    setTimeout(()=>t.remove(),4500);
}

// Update cart badge
function updateCartBadge(n){
    const b=document.getElementById('cart-badge');
    if(!b)return;
    b.textContent=n>9?'9+':n;
    b.classList.toggle('hidden',n<1);
}

// Add to cart (global)
function addToCart(productId,qty=1,size='N/A',color='N/A'){
    const btn=document.querySelector(`[data-add-id="${productId}"]`);
    if(btn){btn.disabled=true;btn.dataset.orig=btn.innerHTML;btn.innerHTML='<svg class="w-4 h-4 animate-spin inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>';}
    fetch('{{ route("cart.add") }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({product_id:productId,quantity:qty,size,color})})
    .then(r=>r.json())
    .then(d=>{
        if(d.success){updateCartBadge(d.cart_count);showToast(d.message,'success');}
        else showToast(d.message||'Could not add to cart.','error');
    })
    .catch(()=>showToast('Network error. Try again.','error'))
    .finally(()=>{if(btn){btn.disabled=false;btn.innerHTML=btn.dataset.orig||'Add to Cart';}});
}

// Newsletter
document.getElementById('newsletter-form').addEventListener('submit',function(e){
    e.preventDefault();
    const em=this.querySelector('input[name="email"]').value;
    fetch('{{ route("newsletter.subscribe") }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({email:em})})
    .then(r=>r.json()).then(d=>{showToast(d.message||'Subscribed!','success');this.reset();}).catch(()=>showToast('Error. Try again.','error'));
});
</script>
@stack('scripts')
</body>
</html>