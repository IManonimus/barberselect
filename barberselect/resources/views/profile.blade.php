@extends('layouts.user')

@section('title', 'Profil - BarberSelect')
@section('subtitle', 'Profil')

@section('content')
    <section class="mx-auto max-w-6xl px-5 py-10 md:py-14">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div>
                <p class="text-xs font-semibold tracking-[0.35em] text-white/60">PROFIL</p>
                <h1 class="mt-3 text-2xl font-semibold tracking-tight text-white md:text-3xl">Ubah profil</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/65 md:text-base">
                    Perbarui nama, email, dan (opsional) password akun kamu.
                </p>
            </div>
        </div>

        <div class="mt-8 max-w-3xl rounded-3xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur">
            @if (session('status'))
                <div class="mb-4 rounded-2xl border border-emerald-400/25 bg-emerald-400/10 p-4 text-sm font-semibold text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-red-400/25 bg-red-400/10 p-4 text-sm text-red-200">
                    <p class="font-semibold">Periksa lagi input kamu:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/profil" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="text-sm font-semibold text-white/80">Nama</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-neutral-950/60 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/10"
                        />
                    </div>
                    <div>
                        <label for="email" class="text-sm font-semibold text-white/80">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-neutral-950/60 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/10"
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="text-sm font-semibold text-white/80">Password baru <span class="text-white/40">(opsional)</span></label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-neutral-950/60 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/10"
                        />
                        <p class="mt-2 text-xs text-white/45">Kosongkan jika tidak ingin mengganti password.</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="text-sm font-semibold text-white/80">Konfirmasi password</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="mt-2 w-full rounded-2xl border border-white/10 bg-neutral-950/60 px-4 py-3 text-sm text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-white/10"
                        />
                    </div>
                </div>

                <button type="submit" class="w-full rounded-full bg-white px-6 py-3 text-sm font-semibold text-neutral-950 hover:bg-white/90">
                    Simpan perubahan
                </button>
            </form>
        </div>
    </section>
@endsection
