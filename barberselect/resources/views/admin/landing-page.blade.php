<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Landing Page - BarberSelect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg0:#0b1020;
            --bg1:#0f172a;
            --panel: rgba(18, 24, 43, 0.72);
            --panel2: rgba(15, 22, 38, 0.55);
            --border: rgba(148,163,184,0.16);
            --text:#e5e7eb;
            --muted:#94a3b8;
            --primary:#38bdf8;
            --accent:#22c55e;
            --warn:#f59e0b;
            --danger:#fb7185;
            --shadow: 0 18px 40px rgba(0,0,0,0.35);
        }
        *{ box-sizing:border-box; }
        body{
            font-family:'Inter', sans-serif;
            margin:0;
            min-height:100vh;
            background: radial-gradient(1200px 600px at 20% 0%, rgba(56,189,248,0.12), transparent 60%),
                        radial-gradient(900px 500px at 90% 10%, rgba(34,197,94,0.10), transparent 55%),
                        linear-gradient(180deg, var(--bg0), var(--bg1));
            color: var(--text);
            display:flex;
        }
        .sidebar{
            width: 270px;
            min-height:100vh;
            padding: 18px 14px;
            background: rgba(15, 23, 42, 0.75);
            border-right: 1px solid var(--border);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
        }
        .brand{
            display:flex;
            align-items:center;
            justify-content: space-between;
            padding: 10px 10px 14px;
            margin-bottom: 8px;
        }
        .brand strong{ font-size: 1.05rem; letter-spacing: .2px; }
        .pill{
            font-size: .75rem;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(56,189,248,0.12);
            border: 1px solid rgba(56,189,248,0.22);
            color: #bfe9ff;
            font-weight: 700;
        }
        .nav a{
            display:flex;
            align-items:center;
            gap:10px;
            color: var(--text);
            text-decoration:none;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid transparent;
            margin-bottom: 8px;
            background: transparent;
            transition: .2s ease;
            font-weight: 600;
        }
        .nav a:hover{ background: rgba(148,163,184,0.10); border-color: rgba(148,163,184,0.18); }
        .nav a.active{ background: rgba(56,189,248,0.14); border-color: rgba(56,189,248,0.22); }
        .logout-btn{
            width: 100%;
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(20,184,166,0.22);
            background: rgba(20,184,166,0.15);
            color: #cffff7;
            font-weight: 800;
            cursor: pointer;
        }
        .main{ flex:1; padding: 18px 18px 30px; }
        .topbar{
            display:flex;
            align-items:center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: rgba(18, 24, 43, 0.72);
            box-shadow: var(--shadow);
            margin-bottom: 14px;
        }
        .topbar .title{ display:flex; flex-direction: column; gap: 2px; }
        .topbar h1{ margin:0; font-size: 1.05rem; letter-spacing: .2px; }
        .topbar p{ margin:0; color: var(--muted); font-size: .9rem; }
        .badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(2, 6, 23, 0.18);
            color: var(--muted);
            font-weight: 700;
            font-size: .8rem;
        }
        .card{
            background: rgba(18, 24, 43, 0.72);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 14px 16px;
            box-shadow: var(--shadow);
        }
        .grid{ display:grid; grid-template-columns: 1fr; gap: 14px; }
        .two{ display:grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        label{ display:block; font-weight:800; font-size:.85rem; color:#dbeafe; margin: 12px 0 6px; }
        input[type="text"], input[type="number"], textarea{
            width:100%;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(148,163,184,0.20);
            background: rgba(15, 22, 38, 0.55);
            color: var(--text);
            outline:none;
        }
        textarea{ min-height: 110px; resize: vertical; }
        .hint{ color: var(--muted); font-size: .85rem; margin-top: 6px; line-height: 1.5; }
        .row{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid rgba(56,189,248,0.22);
            background: rgba(56,189,248,0.14);
            color: #d9f3ff;
            font-weight: 900;
            cursor: pointer;
            text-decoration:none;
        }
        .btn.secondary{
            border-color: rgba(148,163,184,0.18);
            background: rgba(148,163,184,0.10);
            color: var(--text);
            font-weight: 800;
        }
        .alert{
            border: 1px solid rgba(34,197,94,0.22);
            background: rgba(34,197,94,0.12);
            color: #caffda;
            padding: 10px 12px;
            border-radius: 14px;
            margin-bottom: 12px;
            font-weight: 800;
        }
        .check{
            display:flex;
            gap:10px;
            align-items:center;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid rgba(148,163,184,0.16);
            background: rgba(15, 22, 38, 0.55);
        }
        .check input{ width: 18px; height: 18px; }
        @media (max-width: 1024px){
            body{ display:block; }
            .sidebar{ width:100%; position:relative; }
            .main{ padding: 14px; }
            .two{ grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <strong>BarberSelect</strong>
            <span class="pill">Admin</span>
        </div>

        <nav class="nav">
            <a href="/admin">📊 Dashboard</a>
            <a href="/admin/categories">🗂 Kategori</a>
            <a href="/admin/catalogs">✂️ Katalog</a>
            <a class="active" href="/admin/landing-page">🖥 Landing Page</a>
            <a href="/">🏠 Beranda (User View)</a>
            <a href="/admin/profile">👤 Edit Profil Admin</a>
        </nav>

        <form method="POST" action="/logout" style="margin:0;">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="title">
                <h1>Landing Page</h1>
                <p>Edit semua konten yang tampil di halaman utama</p>
            </div>
            <div class="row">
                <span class="badge">👋 {{ $user->name }}</span>
                <a class="btn secondary" href="/" target="_blank" rel="noreferrer">Preview →</a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        <form class="grid" method="POST" action="/admin/landing-page">
            @csrf

            <section class="card">
                <strong>Hero</strong>
                <div class="two">
                    <div>
                        <label>Kicker</label>
                        <input type="text" name="hero_kicker" value="{{ old('hero_kicker', $lp['hero']['kicker'] ?? '') }}">
                    </div>
                    <div>
                        <label>Background URL</label>
                        <input type="text" name="hero_background_url" value="{{ old('hero_background_url', $lp['hero']['background_url'] ?? '') }}">
                    </div>
                </div>
                <label>Title</label>
                <input type="text" name="hero_title" value="{{ old('hero_title', $lp['hero']['title'] ?? '') }}">
                <label>Subtitle</label>
                <textarea name="hero_subtitle">{{ old('hero_subtitle', $lp['hero']['subtitle'] ?? '') }}</textarea>

                <div class="two">
                    <div>
                        <label>CTA Primary Text</label>
                        <input type="text" name="hero_cta_primary_text" value="{{ old('hero_cta_primary_text', $lp['hero']['cta_primary_text'] ?? '') }}">
                        <label>CTA Primary Href</label>
                        <input type="text" name="hero_cta_primary_href" value="{{ old('hero_cta_primary_href', $lp['hero']['cta_primary_href'] ?? '') }}">
                    </div>
                    <div>
                        <label>CTA Secondary Text</label>
                        <input type="text" name="hero_cta_secondary_text" value="{{ old('hero_cta_secondary_text', $lp['hero']['cta_secondary_text'] ?? '') }}">
                        <label>CTA Secondary Href</label>
                        <input type="text" name="hero_cta_secondary_href" value="{{ old('hero_cta_secondary_href', $lp['hero']['cta_secondary_href'] ?? '') }}">
                    </div>
                </div>
            </section>

            <section class="card">
                <strong>Tampilkan/Sembunyikan Section</strong>
                <div class="row" style="margin-top:10px;">
                    <label class="check"><input type="checkbox" name="section_catalog" value="1" {{ old('section_catalog', ($lp['sections']['catalog'] ?? true) ? '1' : '') ? 'checked' : '' }}> Katalog</label>
                    <label class="check"><input type="checkbox" name="section_trends" value="1" {{ old('section_trends', ($lp['sections']['trends'] ?? true) ? '1' : '') ? 'checked' : '' }}> Tren</label>
                    <label class="check"><input type="checkbox" name="section_ai" value="1" {{ old('section_ai', ($lp['sections']['ai'] ?? true) ? '1' : '') ? 'checked' : '' }}> AI</label>
                    <label class="check"><input type="checkbox" name="section_about" value="1" {{ old('section_about', ($lp['sections']['about'] ?? true) ? '1' : '') ? 'checked' : '' }}> Tentang</label>
                </div>
                <div class="hint">Uncheck untuk menyembunyikan section di landing page.</div>
            </section>

            <section class="card">
                <strong>Catalog Section</strong>
                <div class="two">
                    <div>
                        <label>Kicker</label>
                        <input type="text" name="catalog_kicker" value="{{ old('catalog_kicker', $lp['catalog']['kicker'] ?? '') }}">
                    </div>
                    <div>
                        <label>Jumlah item (take)</label>
                        <input type="number" name="catalog_take" value="{{ old('catalog_take', $lp['catalog']['take'] ?? 6) }}" min="1" max="24">
                    </div>
                </div>
                <label>Title</label>
                <input type="text" name="catalog_title" value="{{ old('catalog_title', $lp['catalog']['title'] ?? '') }}">
                <label>Subtitle</label>
                <textarea name="catalog_subtitle">{{ old('catalog_subtitle', $lp['catalog']['subtitle'] ?? '') }}</textarea>
                <label>Hint</label>
                <input type="text" name="catalog_hint" value="{{ old('catalog_hint', $lp['catalog']['hint'] ?? '') }}">
            </section>

            <section class="card">
                <strong>Trends Section</strong>
                <label>Kicker</label>
                <input type="text" name="trends_kicker" value="{{ old('trends_kicker', $lp['trends']['kicker'] ?? '') }}">
                <label>Title</label>
                <input type="text" name="trends_title" value="{{ old('trends_title', $lp['trends']['title'] ?? '') }}">
                <label>Subtitle</label>
                <textarea name="trends_subtitle">{{ old('trends_subtitle', $lp['trends']['subtitle'] ?? '') }}</textarea>
                <label>Hint</label>
                <input type="text" name="trends_hint" value="{{ old('trends_hint', $lp['trends']['hint'] ?? '') }}">

                <label>Trends Items (JSON array)</label>
                <textarea name="trends_items_json">{{ old('trends_items_json', json_encode($lp['trends']['items'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                <div class="hint">Format: <code>[{"title":"...","desc":"..."}, ...]</code></div>
            </section>

            <section class="card">
                <strong>AI Section</strong>
                <div class="two">
                    <div>
                        <label>Kicker</label>
                        <input type="text" name="ai_kicker" value="{{ old('ai_kicker', $lp['ai']['kicker'] ?? '') }}">
                    </div>
                    <div>
                        <label>Button Text</label>
                        <input type="text" name="ai_button_text" value="{{ old('ai_button_text', $lp['ai']['button_text'] ?? '') }}">
                    </div>
                </div>
                <label>Title</label>
                <input type="text" name="ai_title" value="{{ old('ai_title', $lp['ai']['title'] ?? '') }}">
                <label>Subtitle</label>
                <textarea name="ai_subtitle">{{ old('ai_subtitle', $lp['ai']['subtitle'] ?? '') }}</textarea>
                <div class="two">
                    <div>
                        <label>Label</label>
                        <input type="text" name="ai_label" value="{{ old('ai_label', $lp['ai']['label'] ?? '') }}">
                    </div>
                    <div>
                        <label>Placeholder</label>
                        <input type="text" name="ai_placeholder" value="{{ old('ai_placeholder', $lp['ai']['placeholder'] ?? '') }}">
                    </div>
                </div>
                <label>Hint</label>
                <input type="text" name="ai_hint" value="{{ old('ai_hint', $lp['ai']['hint'] ?? '') }}">
                <div class="two">
                    <div>
                        <label>Result Title</label>
                        <input type="text" name="ai_result_title" value="{{ old('ai_result_title', $lp['ai']['result_title'] ?? '') }}">
                    </div>
                    <div>
                        <label>Disclaimer Title</label>
                        <input type="text" name="ai_disclaimer_title" value="{{ old('ai_disclaimer_title', $lp['ai']['disclaimer_title'] ?? '') }}">
                    </div>
                </div>
                <label>Disclaimer Text</label>
                <textarea name="ai_disclaimer_text">{{ old('ai_disclaimer_text', $lp['ai']['disclaimer_text'] ?? '') }}</textarea>
            </section>

            <section class="card">
                <strong>About Section</strong>
                <label>Kicker</label>
                <input type="text" name="about_kicker" value="{{ old('about_kicker', $lp['about']['kicker'] ?? '') }}">
                <label>Title</label>
                <input type="text" name="about_title" value="{{ old('about_title', $lp['about']['title'] ?? '') }}">
                <label>Subtitle</label>
                <textarea name="about_subtitle">{{ old('about_subtitle', $lp['about']['subtitle'] ?? '') }}</textarea>
                <label>Bullets (JSON array of strings)</label>
                <textarea name="about_bullets_json">{{ old('about_bullets_json', json_encode($lp['about']['bullets'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                <div class="hint">Format: <code>["bullet 1","bullet 2","bullet 3"]</code></div>
            </section>

            <section class="card">
                <strong>Footer</strong>
                <label>Left</label>
                <input type="text" name="footer_left" value="{{ old('footer_left', $lp['footer']['left'] ?? '') }}">
                <div class="hint">Gunakan <code>{year}</code> untuk tahun otomatis.</div>
                <label>Right</label>
                <input type="text" name="footer_right" value="{{ old('footer_right', $lp['footer']['right'] ?? '') }}">
            </section>

            <section class="card">
                <div class="row" style="justify-content:flex-end;">
                    <button class="btn" type="submit">💾 Simpan Perubahan</button>
                </div>
                @if ($errors->any())
                    <div class="hint" style="margin-top:10px;color:#fecaca;">
                        Ada error validasi. Cek field yang terlalu panjang atau format JSON yang tidak valid.
                    </div>
                @endif
            </section>
        </form>
    </main>
</body>
</html>

