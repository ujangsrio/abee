<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aretha Beauty</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-100 to-white text-gray-800 font-sans antialiased">

<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white/70 backdrop-blur-sm shadow-lg p-6 border-r border-purple-100 hidden md:block">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-purple-700 tracking-wide">Aretha Beauty</h1>
            <p class="text-sm text-gray-500">Admin Dashboard</p>
        </div>
        <nav class="flex flex-col space-y-3 text-sm">
            @php
                $menu = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => '🏠'],
                    ['name' => 'Manajemen Layanan', 'route' => 'layanan.index', 'icon' => '💄'],
                    ['name' => 'Data Reservasi', 'route' => 'reservasi.index', 'icon' => '🗓️'],
                    ['name' => 'Promo', 'route' => 'promo.index', 'icon' => '🎁'],
                    ['name' => 'Membership', 'route' => 'membership.index', 'icon' => '👑'],
                    ['name' => 'Pelanggan', 'route' => 'pelanggan.index', 'icon' => '🧍‍♀️'],
                    ['name' => 'Histori Layanan', 'route' => 'histori.index', 'icon' => '🕘'],
                    ['name' => 'Pengaturan', 'route' => 'pengaturan.index', 'icon' => '⚙️'],
                    ['name' => 'Laporan & Export', 'route' => 'laporan.index', 'icon' => '📊'],
                ];
            @endphp

            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-2 px-3 py-2 rounded transition
                   {{ request()->routeIs($item['route']) ? 'bg-purple-700 text-white font-semibold' : 'hover:bg-purple-100 text-gray-700' }}">
                    <span>{{ $item['icon'] }}</span>
                    <span>{{ $item['name'] }}</span>
                </a>
            @endforeach
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-y-auto">
        @yield('content')
    </main>
</div>

<!-- Tempat untuk push script grafik -->
@stack('scripts')

</body>
</html>
