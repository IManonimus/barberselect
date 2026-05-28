<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BarberSelect')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { color-scheme: dark; }
        html { scroll-behavior: smooth; }
        .noise {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='180' height='180' filter='url(%23n)' opacity='.15'/%3E%3C/svg%3E");
        }
    </style>
    @stack('head')
</head>
<body class="bg-neutral-950 text-neutral-100 antialiased">
    <div class="fixed inset-0 -z-10 bg-neutral-950">
        <div class="absolute inset-0 opacity-70 [background:radial-gradient(60%_40%_at_50%_0%,rgba(255,255,255,0.10)_0%,rgba(255,255,255,0)_60%)]"></div>
        <div class="noise absolute inset-0 mix-blend-soft-light"></div>
    </div>

    <header class="sticky top-0 z-50 border-b border-white/10 bg-neutral-950/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4">
            <a href="/" class="group inline-flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                    <span class="h-2.5 w-2.5 rounded-full bg-white/80"></span>
                </span>
                <span class="text-sm font-semibold tracking-[0.2em] text-white/90 group-hover:text-white">BARBERSELECT</span>
            </a>

            @php
                $path = request()->path();
                $isActive = function (string $href) use ($path): bool {
                    $hrefPath = ltrim(parse_url($href, PHP_URL_PATH) ?? '', '/');
                    if ($hrefPath === '') return $path === '';
                    return $path === $hrefPath || str_starts_with($path, $hrefPath . '/');
                };
            @endphp

            <nav class="hidden items-center gap-7 text-sm text-white/75 md:flex">
                <a href="/dashboard" class="{{ $isActive('dashboard') ? 'text-white' : 'hover:text-white' }}">Dashboard</a>
                <a href="/kategori" class="{{ $isActive('kategori') ? 'text-white' : 'hover:text-white' }}">Kategori</a>
                <a href="/katalog" class="{{ $isActive('katalog') ? 'text-white' : 'hover:text-white' }}">Katalog</a>
                <a href="/profil" class="{{ $isActive('profil') ? 'text-white' : 'hover:text-white' }}">Profil</a>
                @if(auth()->user()?->is_admin)
                    <a href="/admin" class="hover:text-white">Admin</a>
                @endif
            </nav>

            <div class="flex items-center gap-2">
                <div class="hidden items-center gap-3 sm:flex">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15 text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </span>
                    <div class="hidden text-sm text-white/70 md:block">
                        <div class="font-semibold text-white/85">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="text-xs text-white/45">@yield('subtitle', 'User area')</div>
                    </div>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-neutral-950 hover:bg-white/90">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="border-t border-white/10">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

