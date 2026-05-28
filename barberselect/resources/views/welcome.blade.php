<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberSelect - Temukan Gaya Rambut Terbaikmu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { color-scheme: dark; }
        html { scroll-behavior: smooth; }
        .noise {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='180' height='180' filter='url(%23n)' opacity='.15'/%3E%3C/svg%3E");
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
@php
    $lp = \App\Models\LandingPageSetting::current();
    $year = date('Y');
@endphp
<body class="bg-neutral-950 text-neutral-100 antialiased">
    <div class="fixed inset-0 -z-10 bg-neutral-950">
        <div class="absolute inset-0 opacity-70 [background:radial-gradient(60%_40%_at_50%_0%,rgba(255,255,255,0.10)_0%,rgba(255,255,255,0)_60%)]"></div>
        <div class="noise absolute inset-0 mix-blend-soft-light"></div>
    </div>

    <header class="sticky top-0 z-50 border-b border-white/10 bg-neutral-950/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-5 sm:py-4">
            <a href="/" class="group inline-flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                    <span class="h-2.5 w-2.5 rounded-full bg-white/80"></span>
                </span>
                <span class="text-xs font-semibold tracking-[0.16em] text-white/90 group-hover:text-white sm:text-sm sm:tracking-[0.2em]">BARBERSELECT</span>
            </a>

            <nav class="hidden items-center gap-7 text-sm text-white/75 md:flex">
                <a href="#catalog" class="hover:text-white">Katalog</a>
                <a href="#trends" class="hover:text-white">Tren</a>
                <a href="#ai" class="hover:text-white">AI</a>
                <a href="#about" class="hover:text-white">Tentang</a>
            </nav>

            <div class="flex items-center gap-1.5 sm:gap-2">
                @guest
                    <a href="/register" class="rounded-full bg-white px-2.5 py-1.5 text-[10px] font-semibold text-neutral-950 hover:bg-white/90 sm:px-4 sm:py-2 sm:text-xs">Daftar</a>
                    <a href="/login" class="rounded-full border border-white/15 bg-white/5 px-2.5 py-1.5 text-[10px] font-semibold text-white hover:bg-white/10 sm:px-4 sm:py-2 sm:text-xs">Login</a>
                    <a href="/admin/login" class="hidden rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white hover:bg-white/10 sm:inline-flex">Admin</a>
                @else
                    @if(auth()->user()->is_admin)
                        <a href="/admin" class="rounded-full border border-white/15 bg-white/5 px-2.5 py-1.5 text-[10px] font-semibold text-white hover:bg-white/10 sm:px-4 sm:py-2 sm:text-xs">Admin Dashboard</a>
                    @else
                        <a href="/dashboard" class="rounded-full border border-white/15 bg-white/5 px-2.5 py-1.5 text-[10px] font-semibold text-white hover:bg-white/10 sm:px-4 sm:py-2 sm:text-xs">Dashboard</a>
                    @endif
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="rounded-full bg-white px-2.5 py-1.5 text-[10px] font-semibold text-neutral-950 hover:bg-white/90 sm:px-4 sm:py-2 sm:text-xs">Logout</button>
                    </form>
                @endguest

                <button id="mobileMenuBtn" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white md:hidden" aria-label="Buka menu" aria-expanded="false" aria-controls="mobileMenuPanel">
                    <svg id="mobileMenuIconOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="mobileMenuIconClose" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobileMenuPanel" class="hidden border-t border-white/10 px-5 py-4 md:hidden">
            <nav class="flex flex-col gap-1 text-sm text-white/80">
                <a href="#catalog" class="rounded-xl px-3 py-2 hover:bg-white/10">Katalog</a>
                <a href="#trends" class="rounded-xl px-3 py-2 hover:bg-white/10">Tren</a>
                <a href="#ai" class="rounded-xl px-3 py-2 hover:bg-white/10">AI</a>
                <a href="#about" class="rounded-xl px-3 py-2 hover:bg-white/10">Tentang</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10">
                <div class="absolute inset-0 bg-cover bg-center opacity-55" style="background-image: url('{{ $lp['hero']['background_url'] ?? '' }}')"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-neutral-950/60 via-neutral-950/70 to-neutral-950"></div>
                <div class="absolute -left-24 top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -right-24 top-40 h-80 w-80 rounded-full bg-white/5 blur-3xl"></div>
            </div>

            <div class="mx-auto grid min-h-[72vh] max-w-6xl items-end px-4 pb-12 pt-12 sm:px-5 sm:pb-16 sm:pt-16 md:min-h-[86vh] md:pb-24 md:pt-24">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold tracking-[0.35em] text-white/70">{{ $lp['hero']['kicker'] ?? '' }}</p>
                    <h1 class="mt-3 text-balance text-3xl font-semibold tracking-tight text-white sm:mt-4 sm:text-4xl md:text-6xl">
                        {{ $lp['hero']['title'] ?? '' }}
                    </h1>
                    <p class="mt-4 max-w-2xl text-pretty text-sm leading-relaxed text-white/70 sm:mt-5 sm:text-base md:text-lg">
                        {{ $lp['hero']['subtitle'] ?? '' }}
                    </p>

                    <div class="mt-7 flex flex-col items-stretch gap-2.5 sm:mt-8 sm:flex-row sm:flex-wrap sm:items-center sm:gap-3">
                        <a href="{{ $lp['hero']['cta_primary_href'] ?? '#ai' }}" class="rounded-full bg-white px-6 py-3 text-center text-sm font-semibold text-neutral-950 hover:bg-white/90">
                            {{ $lp['hero']['cta_primary_text'] ?? 'Mulai dengan AI' }}
                        </a>
                        <a href="{{ $lp['hero']['cta_secondary_href'] ?? '#catalog' }}" class="rounded-full border border-white/15 bg-white/5 px-6 py-3 text-center text-sm font-semibold text-white hover:bg-white/10">
                            {{ $lp['hero']['cta_secondary_text'] ?? 'Jelajahi katalog' }}
                        </a>
                    </div>

                    <div class="mt-10 grid max-w-2xl grid-cols-2 gap-3 md:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-xs font-semibold text-white/70">Kurasi</p>
                            <p class="mt-1 text-sm text-white/80">Koleksi gaya modern & klasik</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-xs font-semibold text-white/70">Personal</p>
                            <p class="mt-1 text-sm text-white/80">Saran sesuai profil kamu</p>
                        </div>
                        <div class="hidden rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur md:block">
                            <p class="text-xs font-semibold text-white/70">Praktis</p>
                            <p class="mt-1 text-sm text-white/80">Mudah dijelaskan ke barber</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if(($lp['sections']['catalog'] ?? true) === true)
        <section id="catalog" class="border-t border-white/10 bg-neutral-950">
            <div class="mx-auto max-w-6xl px-5 py-16 md:py-20">
                <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.35em] text-white/60">{{ $lp['catalog']['kicker'] ?? 'CATALOG' }}</p>
                        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white md:text-3xl">{{ $lp['catalog']['title'] ?? 'Katalog gaya rambut' }}</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/65 md:text-base">
                            {{ $lp['catalog']['subtitle'] ?? '' }}
                        </p>
                    </div>
                    <div class="text-sm text-white/60">{{ $lp['catalog']['hint'] ?? '' }}</div>
                </div>

                <div class="hide-scrollbar mt-8 flex gap-2 overflow-x-auto pb-1" id="filterBar">
                    <button class="filter-btn shrink-0 rounded-full bg-white px-4 py-2 text-xs font-semibold text-neutral-950" data-filter="all">Semua</button>
                    @php
                        $categories = \App\Models\Category::orderBy('name')->get();
                    @endphp
                    @foreach($categories as $category)
                        <button class="filter-btn shrink-0 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10" data-filter="{{ strtolower($category->name) }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" id="catalogGrid">
                    @php
                        $catalogTake = (int) ($lp['catalog']['take'] ?? 6);
                        $catalogTake = max(1, min(24, $catalogTake));
                        $catalogs = \App\Models\Catalog::with('category')->take($catalogTake)->get();
                    @endphp
                    @foreach($catalogs as $catalog)
                        <article class="catalog-item group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] transition hover:bg-white/[0.06]" data-category="{{ strtolower($catalog->category->name ?? 'modern') }}">
                            <div class="relative aspect-[16/10] overflow-hidden">
                                <img
                                    src="{{ $catalog->image_url ?? 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80' }}"
                                    alt="{{ $catalog->name }}"
                                    class="h-full w-full object-cover opacity-90 transition duration-700 group-hover:scale-[1.04] group-hover:opacity-100"
                                    loading="lazy"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/70 via-neutral-950/10 to-transparent"></div>
                            </div>
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <h3 class="text-base font-semibold tracking-tight text-white">{{ $catalog->name }}</h3>
                                    <span class="shrink-0 rounded-full border border-white/15 bg-white/5 px-3 py-1 text-[11px] font-semibold text-white/80">
                                        {{ $catalog->category->name ?? 'Modern' }}
                                    </span>
                                </div>
                                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-white/65">
                                    {{ $catalog->description ?? 'Deskripsi gaya rambut yang bersih, modern, dan mudah dieksekusi.' }}
                                </p>
                                <a href="{{ route('catalog.show', $catalog) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-white/85 hover:text-white">
                                    Lihat detail <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        @if(($lp['sections']['trends'] ?? true) === true)
        <section id="trends" class="border-t border-white/10">
            <div class="mx-auto max-w-6xl px-5 py-16 md:py-20">
                <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.35em] text-white/60">{{ $lp['trends']['kicker'] ?? 'DISCOVER' }}</p>
                        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white md:text-3xl">{{ $lp['trends']['title'] ?? 'Tren rambut terbaru' }}</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/65 md:text-base">
                            {{ $lp['trends']['subtitle'] ?? '' }}
                        </p>
                    </div>
                    <div class="text-sm text-white/60">{{ $lp['trends']['hint'] ?? '' }}</div>
                </div>

                <div class="hide-scrollbar mt-8 flex gap-4 overflow-x-auto pb-2">
                    @foreach(($lp['trends']['items'] ?? []) as $trend)
                        <div class="min-w-[260px] rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur transition hover:bg-white/[0.06] sm:min-w-[320px]">
                            <p class="text-xs font-semibold tracking-[0.25em] text-white/55">TREND</p>
                            <h3 class="mt-2 text-lg font-semibold text-white">{{ $trend['title'] ?? '' }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-white/65">{{ $trend['desc'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        @if(($lp['sections']['ai'] ?? true) === true)
        <section id="ai" class="border-t border-white/10">
            <div class="mx-auto max-w-6xl px-5 py-16 md:py-20">
                <div class="grid gap-10 lg:grid-cols-2 lg:items-start">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.35em] text-white/60">{{ $lp['ai']['kicker'] ?? 'AI ASSISTANT' }}</p>
                        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white md:text-3xl">{{ $lp['ai']['title'] ?? 'Cari gaya rambutmu' }}</h2>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-white/65 md:text-base">
                            {{ $lp['ai']['subtitle'] ?? '' }}
                        </p>
                        <div class="mt-6 rounded-3xl border border-white/10 bg-white/[0.03] p-5">
                            <label for="searchInput" class="text-sm font-semibold text-white/80">{{ $lp['ai']['label'] ?? 'Deskripsimu' }}</label>
                            <div class="mt-3 flex flex-col gap-2.5 rounded-2xl border border-white/10 bg-neutral-950/60 p-3 sm:flex-row sm:items-center sm:gap-3 sm:px-4 sm:py-3">
                                <input
                                    id="searchInput"
                                    type="text"
                                    class="w-full bg-transparent text-sm text-white placeholder:text-white/40 focus:outline-none"
                                    placeholder="{{ $lp['ai']['placeholder'] ?? 'Contoh: wajah oval, kerja kantoran, suka gaya rapi' }}"
                                />
                                <button id="searchBtn" class="w-full shrink-0 rounded-full bg-white px-4 py-2 text-xs font-semibold text-neutral-950 hover:bg-white/90 sm:w-auto">
                                    {{ $lp['ai']['button_text'] ?? 'Cari' }}
                                </button>
                            </div>
                            <p class="mt-3 text-xs text-white/45">{{ $lp['ai']['hint'] ?? '' }}</p>
                            <p class="mt-4 text-sm text-white/70" id="searchStatus"></p>
                            <p class="mt-2 hidden text-sm font-semibold text-white/80" id="aiLoading">AI sedang menyusun rekomendasi...</p>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5">
                            <h3 class="text-sm font-semibold text-white/80">{{ $lp['ai']['result_title'] ?? 'Hasil rekomendasi' }}</h3>
                            <div class="mt-4 hidden whitespace-pre-wrap rounded-2xl border border-white/10 bg-neutral-950/60 p-4 text-sm leading-relaxed text-white/75" id="aiResult"></div>
                            <div class="mt-5 grid gap-4 sm:grid-cols-2" id="aiImageGrid"></div>
                        </div>

                        <div class="mt-6 rounded-3xl border border-amber-400/25 bg-amber-400/10 p-5">
                            <p class="text-sm font-semibold text-amber-200">{{ $lp['ai']['disclaimer_title'] ?? 'Disclaimer' }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-amber-100/80">
                                {{ $lp['ai']['disclaimer_text'] ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if(($lp['sections']['about'] ?? true) === true)
        <section id="about" class="border-t border-white/10">
            <div class="mx-auto max-w-6xl px-5 py-16 md:py-20">
                <div class="grid gap-10 md:grid-cols-2 md:items-start">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.35em] text-white/60">{{ $lp['about']['kicker'] ?? 'ABOUT' }}</p>
                        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white md:text-3xl">{{ $lp['about']['title'] ?? 'Tentang BarberSelect' }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-white/65 md:text-base">
                            {{ $lp['about']['subtitle'] ?? '' }}
                        </p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-6">
                        <ul class="space-y-4 text-sm text-white/70">
                            @foreach(($lp['about']['bullets'] ?? []) as $bullet)
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">•</span>
                                    <span>{{ $bullet }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <footer class="border-t border-white/10">
            <div class="mx-auto flex max-w-6xl flex-col gap-3 px-5 py-10 text-xs text-white/50 md:flex-row md:items-center md:justify-between">
                <p>{{ str_replace('{year}', $year, $lp['footer']['left'] ?? '') }}</p>
                <p class="text-white/40">{{ $lp['footer']['right'] ?? '' }}</p>
            </div>
        </footer>
    </main>

    <script>
        const filterButtons = document.querySelectorAll('.filter-btn');
        const catalogItems = document.querySelectorAll('.catalog-item');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenuPanel = document.getElementById('mobileMenuPanel');
        const mobileMenuIconOpen = document.getElementById('mobileMenuIconOpen');
        const mobileMenuIconClose = document.getElementById('mobileMenuIconClose');

        const setActiveFilterButton = (activeButton) => {
            filterButtons.forEach((btn) => {
                if (btn === activeButton) {
                    btn.classList.add('bg-white', 'text-neutral-950');
                    btn.classList.remove('border', 'border-white/15', 'bg-white/5', 'text-white/85');
                } else {
                    btn.classList.remove('bg-white', 'text-neutral-950');
                    btn.classList.add('border', 'border-white/15', 'bg-white/5', 'text-white/85');
                }
            });
        };

        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const filter = button.getAttribute('data-filter');
                setActiveFilterButton(button);

                catalogItems.forEach((item) => {
                    const category = item.getAttribute('data-category');
                    item.style.display = filter === 'all' || category === filter ? '' : 'none';
                });
            });
        });

        if (mobileMenuBtn && mobileMenuPanel) {
            const closeMobileMenu = () => {
                mobileMenuPanel.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                mobileMenuIconOpen?.classList.remove('hidden');
                mobileMenuIconClose?.classList.add('hidden');
            };

            const toggleMobileMenu = () => {
                const isHidden = mobileMenuPanel.classList.contains('hidden');
                if (isHidden) {
                    mobileMenuPanel.classList.remove('hidden');
                    mobileMenuBtn.setAttribute('aria-expanded', 'true');
                    mobileMenuIconOpen?.classList.add('hidden');
                    mobileMenuIconClose?.classList.remove('hidden');
                } else {
                    closeMobileMenu();
                }
            };

            mobileMenuBtn.addEventListener('click', toggleMobileMenu);

            mobileMenuPanel.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', closeMobileMenu);
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    closeMobileMenu();
                }
            });
        }

        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const searchStatus = document.getElementById('searchStatus');
        const aiLoading = document.getElementById('aiLoading');
        const aiResult = document.getElementById('aiResult');
        const aiImageGrid = document.getElementById('aiImageGrid');

        const setStatus = (text, type = '') => {
            searchStatus.textContent = text;
            searchStatus.className = 'mt-4 text-sm';
            if (type === 'error') searchStatus.classList.add('text-red-300');
            if (type === 'success') searchStatus.classList.add('text-emerald-200');
            if (!type) searchStatus.classList.add('text-white/70');
        };

        const setLoading = (isLoading) => {
            searchBtn.disabled = isLoading;
            aiLoading.classList.toggle('hidden', !isLoading);
            aiLoading.classList.toggle('block', isLoading);
        };

        const runSearch = async () => {
            const query = searchInput.value.trim();
            if (query.length < 5) {
                aiResult.classList.add('hidden');
                aiImageGrid.innerHTML = '';
                setStatus('Masukkan deskripsi minimal 5 karakter agar AI bisa memberi rekomendasi yang relevan.', 'error');
                return;
            }

            setLoading(true);
            setStatus('');
            aiResult.classList.add('hidden');
            aiImageGrid.innerHTML = '';

            try {
                const response = await fetch('/ai/recommend', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ query })
                });

                let result = {};
                try {
                    result = await response.json();
                } catch (error) {
                    result = {};
                }

                if (!response.ok) {
                    if (response.status === 401) {
                        throw new Error('Silakan login dulu untuk menggunakan rekomendasi AI.');
                    }
                    // Jangan hardcode 429: bisa berasal dari throttle app ATAU dari provider AI.
                    // Prioritaskan pesan dari backend agar akurat.
                    throw new Error(result.error || 'Gagal memproses rekomendasi AI.');
                }

                aiResult.textContent = result.recommendation || 'Belum ada rekomendasi dari AI.';
                aiResult.classList.remove('hidden');

                const catalogs = Array.isArray(result.catalog_recommendations) ? result.catalog_recommendations : [];
                if (catalogs.length > 0) {
                    aiImageGrid.innerHTML = catalogs.map((item) => {
                        const imageUrl = item.image_url || 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';
                        const description = (item.description || 'Referensi gaya rambut dari katalog BarberSelect.').slice(0, 100);

                        return `
                            <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] transition hover:bg-white/[0.06]">
                                <div class="relative aspect-[16/10] overflow-hidden">
                                    <img class="h-full w-full object-cover opacity-90 transition duration-700 group-hover:scale-[1.04] group-hover:opacity-100" src="${imageUrl}" alt="${item.name || 'Rekomendasi Gaya'}" loading="lazy" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/70 via-neutral-950/10 to-transparent"></div>
                                </div>
                                <div class="p-5">
                                    <h4 class="text-sm font-semibold text-white">${item.name || 'Gaya Rambut'}</h4>
                                    <p class="mt-2 text-sm leading-relaxed text-white/65">${description}</p>
                                    <a class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-white/85 hover:text-white" href="${item.detail_url || '#'}">Lihat detail <span aria-hidden="true">→</span></a>
                                </div>
                            </article>
                        `;
                    }).join('');
                }

                setStatus('Rekomendasi AI berhasil dibuat.', 'success');
            } catch (error) {
                aiResult.classList.add('hidden');
                aiImageGrid.innerHTML = '';
                setStatus(error.message || 'Terjadi kesalahan saat meminta rekomendasi AI.', 'error');
            } finally {
                setLoading(false);
            }
        };

        searchBtn.addEventListener('click', runSearch);

        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                runSearch();
            }
        });
    </script>
</body>
</html>
