<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Admin Panel</title>
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important;}</style>
</head>
<body class="bg-gray-100 min-h-screen flex" x-data="{ sidebarOpen: false }">

    {{-- Overlay (mobile) --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-gray-900 text-white p-4 transform transition-transform duration-200 lg:translate-x-0 flex flex-col"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex items-center justify-between mb-6">
            <div class="text-xl font-bold text-indigo-400">⚙️ Admin Panel</div>
            <button @click="sidebarOpen = false" class="lg:hidden text-white text-xl">✕</button>
        </div>

        <nav class="space-y-1 text-sm flex-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-800">📊 Dashboard</a>
            <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">📦 Products</a>
            <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">🗂️ Categories</a>
            <a href="{{ route('admin.brands.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">🏷️ Brands</a>
            <a href="{{ route('admin.attributes.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">🎨 Attributes</a>
            <a href="{{ route('admin.inventory.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">📦 Inventory</a>
            <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">🛒 Orders</a>
            <a href="{{ route('admin.customers.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">👥 Customers</a>
            <a href="{{ route('admin.coupons.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">🎟️ Coupons</a>
            <a href="{{ route('admin.reviews.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">⭐ Reviews</a>
            <a href="{{ route('admin.payment-methods.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">💳 Payment Methods</a>

            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full text-left block px-3 py-2 rounded hover:bg-gray-800">
                    📈 Reports ▾
                </button>
                <div x-show="open" x-cloak class="ml-4 mt-1 space-y-1 text-xs">
                    <a href="{{ route('admin.reports.sales') }}" class="block px-3 py-1 rounded hover:bg-gray-800">Sales</a>
                    <a href="{{ route('admin.reports.products') }}" class="block px-3 py-1 rounded hover:bg-gray-800">Products</a>
                    <a href="{{ route('admin.reports.customers') }}" class="block px-3 py-1 rounded hover:bg-gray-800">Customers</a>
                    <a href="{{ route('admin.reports.payments') }}" class="block px-3 py-1 rounded hover:bg-gray-800">Payments</a>
                </div>
            </div>

            <a href="{{ route('admin.newsletter.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">📧 Newsletter</a>
            <a href="{{ route('admin.banners.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">🖼️ Banners</a>
            <a href="{{ route('admin.pages.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">📄 Pages</a>
            <a href="{{ route('admin.contact-messages.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">✉️ Messages</a>
            <a href="{{ route('admin.settings.index') }}" class="block px-3 py-2 rounded hover:bg-gray-800">⚙️ Settings</a>
        </nav>

        <div class="mt-4 pt-4 border-t border-gray-700 text-xs text-gray-400">
            Logged in as:<br>
            <span class="text-white">{{ auth('admin')->user()->name ?? 'Guest' }}</span>
        </div>
    </aside>

    {{-- Main area --}}
    <div class="flex-1 flex flex-col min-w-0 w-full lg:ml-0">
        <header class="bg-white shadow px-4 md:px-6 py-3 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden text-2xl text-gray-700">☰</button>
                <h1 class="text-base md:text-lg font-semibold">@yield('title', 'Dashboard')</h1>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:underline">Logout</button>
            </form>
        </header>
        <main class="flex-1 p-4 md:p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>