@php
    $links = [
        ['route' => 'delivery.dashboard', 'active' => 'delivery.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
        ['route' => 'delivery.orders.index', 'active' => 'delivery.orders.*', 'label' => 'My Deliveries', 'icon' => 'truck'],
    ];
    $icons = [
        'home' => 'M3 9.5L12 4l9 5.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z',
        'truck' => 'M3 7h11v8H3V7zm11 3h4l3 3v2h-7v-5zM6 19a2 2 0 100-4 2 2 0 000 4zm11 0a2 2 0 100-4 2 2 0 000 4z',
    ];
@endphp

@foreach($links as $link)
    <a href="{{ route($link['route']) }}"
       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
              {{ request()->routeIs($link['active']) ? 'bg-maroon-600 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$link['icon']] }}" />
        </svg>
        <span>{{ $link['label'] }}</span>
    </a>
@endforeach
