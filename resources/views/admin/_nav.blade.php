{{-- Admin navigation partial — @include('admin._nav') in every admin view --}}
<div class="flex flex-wrap gap-2 mb-6 bg-dark-800 border border-dark-600 rounded-2xl p-2">
    @php
    $adminLinks = [
        ['route' => 'admin.dashboard',  'label' => 'Dashboard',  'icon' => '📊'],
        ['route' => 'admin.orders',     'label' => 'Orders',     'icon' => '📦'],
        ['route' => 'admin.products',   'label' => 'Products',   'icon' => '🛍️'],
        ['route' => 'admin.stock',      'label' => 'Stock',      'icon' => '📋'],
        ['route' => 'admin.returns',    'label' => 'Returns',    'icon' => '↩️'],
        ['route' => 'admin.feedback',   'label' => 'Feedback',   'icon' => '💬'],
    ];
    if(auth()->user()->isAdmin()) {
        $adminLinks[] = ['route' => 'admin.employees', 'label' => 'Employees', 'icon' => '👥'];
    }
    @endphp

    @foreach($adminLinks as $link)
    <a href="{{ route($link['route']) }}"
       class="flex items-center gap-1.5 px-3 py-2 text-sm rounded-xl transition-all
              {{ request()->routeIs($link['route']) || request()->routeIs($link['route'].'.*')
                 ? 'bg-brand-700 text-white font-medium'
                 : 'text-gray-400 hover:text-white hover:bg-dark-700' }}">
        <span class="text-base">{{ $link['icon'] }}</span>
        {{ $link['label'] }}
    </a>
    @endforeach
</div>