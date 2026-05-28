@extends('layouts.user')

@section('title', 'Kategori - BarberSelect')
@section('subtitle', 'Kategori')

@section('content')
    <section class="mx-auto max-w-6xl px-5 py-10 md:py-14">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div>
                <p class="text-xs font-semibold tracking-[0.35em] text-white/60">KATEGORI</p>
                <h1 class="mt-3 text-2xl font-semibold tracking-tight text-white md:text-3xl">Daftar kategori</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/65 md:text-base">
                    Gunakan kategori untuk memfilter katalog agar pencarian gaya lebih cepat dan rapi.
                </p>
            </div>
            <a href="/katalog" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10">
                Lihat katalog
            </a>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($categories as $category)
                <article class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <div class="flex items-center gap-4">
                        <div class="relative h-14 w-14 overflow-hidden rounded-2xl border border-white/10 bg-neutral-950/60">
                            <img
                                src="{{ $category->image_url ?? 'https://images.unsplash.com/photo-1520975693411-bce0c5c6858f?auto=format&fit=crop&w=240&q=80' }}"
                                alt="{{ $category->name }}"
                                class="h-full w-full object-cover opacity-90"
                                loading="lazy"
                            />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold tracking-[0.25em] text-white/45">ID {{ $category->id }}</p>
                            <h2 class="mt-1 truncate text-base font-semibold text-white">{{ $category->name }}</h2>
                            <p class="mt-1 text-sm text-white/60">Kategori katalog BarberSelect</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
