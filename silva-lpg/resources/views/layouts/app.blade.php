<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — Silva LPG Trading</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: { 50:'#fbeeee',100:'#f6d9d9',200:'#e9b3b3',300:'#d98282',400:'#c24f4f',500:'#9e2b2b',600:'#7a1f24',700:'#5e181c',800:'#481216',900:'#330c10' },
                        status: { success:'#16a34a','success-bg':'#dcfce7', warning:'#b45309','warning-bg':'#fef3c7', danger:'#dc2626','danger-bg':'#fee2e2' },
                    }
                }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    {{-- If your project builds assets with Vite, swap the two <script> tags above for: @vite(['resources/css/app.css','resources/js/app.js']) --}}
</head>
<body class="bg-gray-50 text-gray-900 antialiased" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

        {{-- Left sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 bg-maroon-800 text-white flex flex-col transform transition-transform duration-200 lg:translate-x-0 lg:static lg:inset-auto"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 flex items-center gap-2 px-5 border-b border-white/10">
                <div class="w-8 h-8 rounded-md bg-maroon-600 flex items-center justify-center font-bold text-sm">SL</div>
                <div class="leading-tight">
                    <p class="font-semibold text-sm">Silva LPG Trading</p>
                    <p class="text-[11px] text-white/50 capitalize">{{ auth()->user()->role ?? '' }} panel</p>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
                @auth
                    @if(auth()->user()->isAdmin())
                        @include('layouts.partials.nav-admin')
                    @elseif(auth()->user()->isCashier())
                        @include('layouts.partials.nav-cashier')
                    @elseif(auth()->user()->isDelivery())
                        @include('layouts.partials.nav-delivery')
                    @endif
                @endauth
            </nav>

            <div class="border-t border-white/10 p-3">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="w-8 h-8 rounded-full bg-maroon-600 flex items-center justify-center text-xs font-semibold">
                        {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium truncate">{{ auth()->user()->name ?? '' }}</p>
                        <p class="text-[11px] text-white/50 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="block px-2 py-1.5 rounded-md text-xs text-white/70 hover:bg-white/10 hover:text-white">Profile settings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-2 py-1.5 rounded-md text-xs text-white/70 hover:bg-white/10 hover:text-white">
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="flex-1 min-w-0 flex flex-col">

            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-maroon-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="font-semibold text-gray-900">{{ $header ?? $title ?? '' }}</h1>
                </div>
                <div class="text-xs text-gray-400">{{ now()->format('D, M j, Y') }}</div>
            </header>

            <main class="flex-1 p-4 lg:p-8">
                @if (session('success'))
                    <div class="mb-4 bg-status-success-bg border border-green-200 text-status-success text-sm font-medium px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 bg-status-danger-bg border border-red-200 text-status-danger text-sm font-medium px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 bg-status-danger-bg border border-red-200 text-status-danger text-sm font-medium px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

</body>
</html>
