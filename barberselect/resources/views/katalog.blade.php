@extends('layouts.user')

@section('title', 'Katalog - BarberSelect')
@section('subtitle', 'Katalog')

@section('content')
    <section class="mx-auto max-w-6xl px-5 py-10 md:py-14">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div>
                <p class="text-xs font-semibold tracking-[0.35em] text-white/60">KATALOG</p>
                <h1 class="mt-3 text-2xl font-semibold tracking-tight text-white md:text-3xl">Jelajahi gaya rambut</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/65 md:text-base">
                    Filter berdasarkan kategori, lalu buka detail untuk rekomendasi dan tips.
                </p>
            </div>
            <a href="/kategori" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10">
                Lihat kategori
            </a>
        </div>

        <div class="mt-8 rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
            <div class="flex flex-wrap gap-2" id="filterBar">
                <button class="filter-btn rounded-full bg-white px-4 py-2 text-xs font-semibold text-neutral-950" data-filter="all">Semua</button>
                @php
                    $categories = \App\Models\Category::orderBy('name')->get();
                @endphp
                @foreach($categories as $category)
                    <button class="filter-btn rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10" data-filter="{{ strtolower($category->name) }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" id="catalogGrid">
                @foreach ($catalogs as $catalog)
                    <article class="catalog-item group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] transition hover:bg-white/[0.06]" data-category="{{ strtolower($catalog->category->name ?? 'lainnya') }}">
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
                                <h2 class="text-base font-semibold tracking-tight text-white">{{ $catalog->name }}</h2>
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
@endsection

@push('scripts')
<script>
    const filterButtons = document.querySelectorAll('.filter-btn');
    const catalogItems = document.querySelectorAll('.catalog-item');

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
</script>
@endpush
