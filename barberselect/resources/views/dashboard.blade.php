@extends('layouts.user')

@section('title', 'Dashboard - BarberSelect')
@section('subtitle', 'User dashboard')

@section('content')
        <section class="mx-auto max-w-6xl px-5 py-10 md:py-14">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <p class="text-xs font-semibold tracking-[0.35em] text-white/60">DASHBOARD</p>
                    <h1 class="mt-3 text-2xl font-semibold tracking-tight text-white md:text-3xl">Ringkasan aktivitas kamu</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/65 md:text-base">
                        Lihat rekomendasi AI terakhir, dan lanjutkan eksplorasi gaya rambut dengan tampilan yang bersih dan modern.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="/katalog" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10">Jelajahi katalog</a>
                    <a href="/" class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-neutral-950 hover:bg-white/90">Cari rekomendasi AI</a>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <p class="text-xs font-semibold tracking-[0.25em] text-white/55">KATALOG</p>
                    <p class="mt-2 text-3xl font-semibold tracking-tight text-white">{{ $totalCatalogs ?? 0 }}</p>
                    <p class="mt-1 text-sm text-white/60">Jumlah gaya di katalog</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <p class="text-xs font-semibold tracking-[0.25em] text-white/55">PENGGUNA</p>
                    <p class="mt-2 text-3xl font-semibold tracking-tight text-white">{{ $totalUsers ?? 0 }}</p>
                    <p class="mt-1 text-sm text-white/60">Total user terdaftar</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur sm:col-span-2 lg:col-span-1">
                    <p class="text-xs font-semibold tracking-[0.25em] text-white/55">AKSI CEPAT</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="/kategori" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10">Kategori</a>
                        <a href="/katalog" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10">Katalog</a>
                        <a href="/profil" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10">Profil</a>
                    </div>
                    <p class="mt-3 text-xs text-white/45">Tip: cari rekomendasi AI dari beranda, hasilnya otomatis tersimpan di sini.</p>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-2 lg:items-start">
                <section class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold tracking-[0.25em] text-white/55">AI</p>
                            <h2 class="mt-2 text-lg font-semibold text-white">Hasil rekomendasi terakhir</h2>
                        </div>
                        <a href="/" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10">Buat lagi</a>
                    </div>

                    @if (empty($latestRecommendation))
                        <div class="mt-4 rounded-2xl border border-white/10 bg-neutral-950/60 p-4 text-sm text-white/65">
                            Belum ada hasil rekomendasi dari pencarian kamu.
                            <span class="text-white/85">Coba cari gaya rambut dulu di halaman beranda.</span>
                        </div>
                    @else
                        <div class="mt-4 text-xs text-white/50">
                            Query: <span class="font-semibold text-white/80">{{ $latestRecommendation['query'] ?? '-' }}</span>
                            <span class="mx-2 text-white/25">•</span>
                            Dibuat: {{ \Carbon\Carbon::parse($latestRecommendation['generated_at'])->format('d M Y H:i') }}
                        </div>
                        <div class="mt-4 whitespace-pre-wrap rounded-2xl border border-white/10 bg-neutral-950/60 p-4 text-sm leading-relaxed text-white/75">
                            {{ $latestRecommendation['recommendation'] }}
                        </div>
                    @endif
                </section>

                <section class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <p class="text-xs font-semibold tracking-[0.25em] text-white/55">REKOMENDASI</p>
                    <h2 class="mt-2 text-lg font-semibold text-white">Referensi dari katalog</h2>
                    <p class="mt-2 text-sm text-white/60">Beberapa gaya yang paling relevan dengan rekomendasi AI kamu.</p>

                    @if (empty($latestRecommendation) || empty($latestRecommendation['catalog_recommendations']))
                        <div class="mt-4 rounded-2xl border border-white/10 bg-neutral-950/60 p-4 text-sm text-white/65">
                            Belum ada referensi katalog. Jalankan AI dari beranda agar muncul rekomendasi gambar di sini.
                        </div>
                    @else
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            @foreach ($latestRecommendation['catalog_recommendations'] as $item)
                                <article class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] transition hover:bg-white/[0.06]">
                                    <div class="relative aspect-[16/10] overflow-hidden">
                                        <img
                                            src="{{ $item['image_url'] ?? 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80' }}"
                                            alt="{{ $item['name'] ?? 'Rekomendasi Gaya' }}"
                                            class="h-full w-full object-cover opacity-90 transition duration-700 group-hover:scale-[1.04] group-hover:opacity-100"
                                            loading="lazy"
                                        />
                                        <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/70 via-neutral-950/10 to-transparent"></div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="text-sm font-semibold text-white">{{ $item['name'] ?? 'Gaya Rambut' }}</h3>
                                        <a href="{{ $item['detail_url'] ?? '#' }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-white/85 hover:text-white">
                                            Lihat detail <span aria-hidden="true">→</span>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </section>
@endsection
