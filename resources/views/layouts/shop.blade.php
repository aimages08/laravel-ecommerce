<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>@yield('meta_title', setting('meta_title', setting('store_name'))) </title>

        <meta name="description" content="@yield('meta_description', setting('meta_description'))">
        <meta name="keywords" content="@yield('meta_keywords', setting('meta_keywords'))">

        {{-- Open Graph --}}
        <meta property="og:title" content="@yield('meta_title', setting('meta_title'))">
        <meta property="og:description" content="@yield('meta_description', setting('meta_description'))">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">

        {{-- Google Analytics --}}
        @if (setting('google_analytics_id'))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('google_analytics_id') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ setting('google_analytics_id') }}');
            </script>
        @endif

    @php $favicon = setting('favicon'); @endphp
    @if ($favicon)
        <link rel="icon" href="{{ str_starts_with($favicon, 'http') ? $favicon : asset('storage/' . $favicon) }}">
    @endif

    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important;}</style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    @if (setting('whatsapp_enabled') == '1' && setting('whatsapp_number'))
        @php
            $waNumber = preg_replace('/[^0-9]/', '', setting('whatsapp_number'));
            $waMessage = urlencode(setting('whatsapp_message', 'Hello!'));
        @endphp
        <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}"
           target="_blank"
           class="fixed bottom-6 right-6 z-50 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition"
           title="Chat on WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7">
                <path d="M20.52 3.48A11.87 11.87 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.14 1.6 5.95L0 24l6.32-1.65a11.85 11.85 0 0 0 5.73 1.46h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.17-3.43-8.44ZM12.06 21.8h-.01a9.85 9.85 0 0 1-5.03-1.38l-.36-.22-3.75.98 1-3.66-.23-.37a9.84 9.84 0 0 1-1.51-5.25c0-5.45 4.44-9.88 9.9-9.88 2.64 0 5.13 1.03 7 2.9a9.83 9.83 0 0 1 2.9 6.99c0 5.45-4.44 9.89-9.9 9.89Zm5.44-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.41-1.49-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.01-1.04 2.48 0 1.47 1.07 2.88 1.22 3.08.15.2 2.11 3.22 5.11 4.51.71.31 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.09 1.76-.72 2.01-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35Z"/>
            </svg>
        </a>
    @endif

   <header class="bg-white shadow sticky top-0 z-40" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 py-3">

        {{-- Top row --}}
        <div class="flex items-center justify-between gap-3">
            {{-- Logo --}}
            <a href="{{ route('shop.home') }}" class="flex items-center gap-2 text-xl md:text-2xl font-bold text-indigo-600 whitespace-nowrap">
                @php $logo = setting('logo'); @endphp
                @if ($logo)
                    <img src="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}"
                         class="h-8 md:h-9" alt="Logo">
                @else
                    <span>🛍️</span>
                @endif
                <span>{{ setting('store_name', 'Shoping Website') }}</span>
            </a>

            {{-- Desktop search --}}
            <form action="{{ route('shop.products') }}" method="GET" class="hidden md:flex flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search products..."
                       class="flex-1 border rounded-l px-3 py-2 text-sm">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-r text-sm">🔍</button>
            </form>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex gap-4 items-center whitespace-nowrap">
                <a href="{{ route('shop.home') }}" class="text-gray-700 hover:text-indigo-600">Home</a>
                <a href="{{ route('shop.products') }}" class="text-gray-700 hover:text-indigo-600">Products</a>
                <a href="{{ route('shop.contact') }}" class="text-gray-700 hover:text-indigo-600">Contact</a>
                <a href="{{ route('shop.cart') }}" id="cart-count-link" class="text-gray-700 hover:text-indigo-600">
                    Cart ({{ count(session('cart', [])) }})
                </a>

                @auth
                    <a href="{{ route('shop.my-account') }}" class="text-gray-700 hover:text-indigo-600">My Account</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-indigo-600">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Login</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Register</a>
                @endauth
            </nav>

            {{-- Mobile buttons --}}
            <div class="flex md:hidden items-center gap-2">
                <a href="{{ route('shop.cart') }}" class="relative text-gray-700 text-xl">
                    🛒
                    <span id="cart-count-mobile"
                          class="absolute -top-1 -right-2 bg-indigo-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                        {{ count(session('cart', [])) }}
                    </span>
                </a>
                <button @click="mobileOpen = !mobileOpen" class="text-2xl text-gray-700">
                    <span x-show="!mobileOpen">☰</span>
                    <span x-show="mobileOpen" x-cloak>✕</span>
                </button>
            </div>
        </div>

        {{-- Mobile Search --}}
        <form action="{{ route('shop.products') }}" method="GET" class="mt-3 md:hidden">
            <div class="flex">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search products..."
                       class="flex-1 border rounded-l px-3 py-2 text-sm">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-r text-sm">🔍</button>
            </div>
        </form>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-cloak class="md:hidden mt-3 pt-3 border-t space-y-1">
            <a href="{{ route('shop.home') }}" class="block px-3 py-2 rounded hover:bg-gray-100">🏠 Home</a>
            <a href="{{ route('shop.products') }}" class="block px-3 py-2 rounded hover:bg-gray-100">🛍️ Products</a>
            <a href="{{ route('shop.contact') }}" class="block px-3 py-2 rounded hover:bg-gray-100">📞 Contact</a>

            @auth
                <a href="{{ route('shop.my-account') }}" class="block px-3 py-2 rounded hover:bg-gray-100">👤 My Account</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-gray-100">🚪 Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Login</a>
                <a href="{{ route('register') }}" class="block bg-indigo-600 text-white px-3 py-2 rounded text-center">Register</a>
            @endauth
        </div>
    </div>
</header>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="flex-1 max-w-7xl mx-auto px-4 py-8 w-full">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h3 class="font-bold mb-3">{{ setting('store_name', 'Shoping Website') }}</h3>
                    <p class="text-sm text-gray-400">{{ setting('store_address', 'Pakistan') }}</p>
                    <p class="text-sm text-gray-400">{{ setting('store_email', 'store@example.com') }}</p>
                    <p class="text-sm text-gray-400">{{ setting('store_phone', '0300-0000000') }}</p>
                </div>

                <div>
                    <h3 class="font-bold mb-3">Quick Links</h3>
                    <ul class="space-y-1 text-sm text-gray-400">
                        <li><a href="{{ route('shop.home') }}" class="hover:text-white">Home</a></li>
                        <li><a href="{{ route('shop.products') }}" class="hover:text-white">Products</a></li>
                        <li><a href="{{ route('shop.contact') }}" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold mb-3">Information</h3>
                    <ul class="space-y-1 text-sm text-gray-400">
                        @foreach (\App\Models\Page::footer()->get() as $p)
                            <li>
                                <a href="{{ route('shop.page', $p->slug) }}" class="hover:text-white">
                                    {{ $p->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-4 text-center text-sm text-gray-400">
                © {{ date('Y') }} {{ setting('store_name', 'Shoping Website') }}. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>