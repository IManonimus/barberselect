@extends('layouts.user')

@section('title', ($catalog->name ?? 'Detail Katalog') . ' - BarberSelect')
@section('subtitle', 'Detail katalog')

@section('content')
    <section class="mx-auto max-w-6xl px-5 py-10 md:py-14">
        <a href="/katalog" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs font-semibold text-white/85 hover:bg-white/10">
            <span aria-hidden="true">←</span> Kembali ke Katalog
        </a>

        <div class="mt-6 grid gap-6 lg:grid-cols-2 lg:items-start">
            <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] backdrop-blur">
                <div class="relative aspect-[16/12] overflow-hidden">
                    <img
                        src="{{ $catalog->image_url ?? 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80' }}"
                        alt="{{ $catalog->name }}"
                        class="h-full w-full object-cover opacity-95"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/75 via-neutral-950/10 to-transparent"></div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <p class="text-xs font-semibold tracking-[0.35em] text-white/60">DETAIL</p>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-white md:text-3xl">{{ $catalog->name }}</h1>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="rounded-full border border-white/15 bg-white/5 px-3 py-1 text-[11px] font-semibold text-white/80">
                            {{ $catalog->category->name ?? 'Modern' }}
                        </span>
                        <span class="rounded-full border border-white/15 bg-white/5 px-3 py-1 text-[11px] font-semibold text-white/70">
                            Perawatan: {{ $catalog->care_level ?? 'Sedang' }}
                        </span>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-white/70">
                        {{ $catalog->description ?? 'Gaya rambut ini menawarkan tampilan yang bersih, modern, dan mudah dijelaskan ke barber.' }}
                    </p>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <p class="text-xs font-semibold tracking-[0.25em] text-white/55">RINCIAN</p>
                    <div class="mt-4 grid gap-3 text-sm">
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-neutral-950/60 px-4 py-3">
                            <span class="text-white/60">Cocok untuk bentuk wajah</span>
                            <span class="font-semibold text-white/85">{{ $catalog->face_shape ?? 'Oval, Bulat, Kotak' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-neutral-950/60 px-4 py-3">
                            <span class="text-white/60">Jenis rambut</span>
                            <span class="font-semibold text-white/85">{{ $catalog->hair_type ?? 'Lurus, Bergelombang' }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <p class="text-xs font-semibold tracking-[0.25em] text-white/55">TIPS</p>
                    <h2 class="mt-2 text-lg font-semibold text-white">Tips & rekomendasi</h2>
                    <ul class="mt-4 space-y-2 text-sm text-white/70">
                        @php
                            $defaultTips = [
                                'Gunakan produk styling yang sesuai dengan jenis rambut kamu',
                                'Rapikan potongan tiap 4–6 minggu untuk menjaga bentuk',
                                'Pakai shampoo/conditioner yang cocok agar rambut tetap sehat',
                                'Tunjukkan referensi ini ke barber untuk hasil terbaik',
                                'Jaga kebersihan rambut dan kulit kepala secara rutin',
                            ];
                            $tipsList = collect(preg_split("/\r\n|\n|\r/", $catalog->tips ?? ''))
                                ->map(fn ($tip) => trim($tip))
                                ->filter()
                                ->values();
                            if ($tipsList->isEmpty()) {
                                $tipsList = collect($defaultTips);
                            }
                        @endphp
                        @foreach ($tipsList as $tip)
                            <li class="flex items-start gap-3">
                                <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">•</span>
                                <span>{{ $tip }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
                    <p class="text-xs font-semibold tracking-[0.25em] text-white/55">AI</p>
                    <h2 class="mt-2 text-lg font-semibold text-white">Rekomendasi AI personal</h2>
                    <p class="mt-2 text-sm text-white/60">Tulis kebutuhan kamu (bentuk wajah, aktivitas, preferensi) untuk rekomendasi yang lebih spesifik.</p>

                    <form id="aiRecommendationForm" class="mt-4 space-y-3">
                        <textarea id="aiQuery" maxlength="500" placeholder="Contoh: wajah oval, kerja kantoran, suka gaya clean dan modern..." required class="w-full rounded-2xl border border-white/10 bg-neutral-950/60 p-4 text-sm text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/10"></textarea>
                        <div class="flex flex-wrap items-center gap-3">
                            <button id="aiSubmitButton" type="submit" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-neutral-950 hover:bg-white/90">Dapatkan rekomendasi</button>
                            <p id="aiLoading" class="hidden text-sm font-semibold text-white/70">AI sedang menyusun rekomendasi...</p>
                        </div>
                    </form>
                    <div id="aiFeedback" class="mt-4 hidden whitespace-pre-wrap rounded-2xl border border-white/10 bg-neutral-950/60 p-4 text-sm leading-relaxed text-white/75" role="status" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('aiRecommendationForm');
    const queryInput = document.getElementById('aiQuery');
    const submitButton = document.getElementById('aiSubmitButton');
    const feedback = document.getElementById('aiFeedback');
    const loading = document.getElementById('aiLoading');

    const setLoading = (isLoading) => {
        submitButton.disabled = isLoading;
        loading.classList.toggle('hidden', !isLoading);
        feedback.classList.add('hidden');
    };

    const showFeedback = (message) => {
        feedback.textContent = message;
        feedback.classList.remove('hidden');
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const query = queryInput.value.trim();
        if (query.length < 5) {
            showFeedback('Masukkan deskripsi minimal 5 karakter agar AI bisa memberikan rekomendasi yang relevan.');
            return;
        }

        setLoading(true);
        try {
            const response = await fetch('/ai/recommend', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ query })
            });

            let result = {};
            try {
                result = await response.json();
            } catch (e) {
                result = {};
            }

            if (!response.ok) {
                const message = result.error || 'Terjadi kesalahan saat memproses rekomendasi.';
                throw new Error(message);
            }

            showFeedback(result.recommendation || 'Belum ada rekomendasi yang dapat ditampilkan.');
        } catch (error) {
            showFeedback(error.message || 'Gagal memuat rekomendasi AI. Coba lagi beberapa saat.');
        } finally {
            setLoading(false);
        }
    });
</script>
@endpush