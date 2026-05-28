<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Katalog - BarberSelect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg0:#0b1020;
            --bg1:#0f172a;
            --panel: rgba(18, 24, 43, 0.72);
            --border: rgba(148,163,184,0.16);
            --text:#e5e7eb;
            --muted:#94a3b8;
            --primary:#38bdf8;
            --accent:#22c55e;
            --shadow: 0 18px 40px rgba(0,0,0,0.35);
        }
        *{ box-sizing:border-box; }
        body{
            font-family:'Inter', sans-serif;
            margin:0;
            min-height:100vh;
            background: radial-gradient(1200px 600px at 20% 0%, rgba(56,189,248,0.12), transparent 60%),
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
        .main{
            flex:1;
            padding: 18px 18px 30px;
        }
        .topbar{
            display:flex;
            justify-content: space-between;
            align-items:flex-start;
            gap: 14px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--panel);
            box-shadow: var(--shadow);
            margin-bottom: 14px;
        }
        .topbar h1{ margin:0; font-size: 1.05rem; letter-spacing: .2px; }
        .topbar p{ margin:4px 0 0; color: var(--muted); font-size: .9rem; }
        .card{
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px;
            box-shadow: var(--shadow);
            max-width: 920px;
        }
        label{ display:block; margin-top: 12px; color: #cbd5e1; font-weight: 800; }
        input, select, textarea{
            width:100%;
            padding: 12px 12px;
            border-radius: 12px;
            border: 1px solid rgba(148,163,184,0.20);
            margin-top: 8px;
            background: rgba(2, 6, 23, 0.20);
            color: var(--text);
            outline:none;
        }
        input:focus, select:focus, textarea:focus{
            border-color: rgba(56,189,248,0.45);
            box-shadow: 0 0 0 4px rgba(56,189,248,0.15);
        }
        .btn{
            margin-top: 16px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(34,197,94,0.20);
            background: rgba(34,197,94,0.14);
            color: #d1fae5;
            font-weight: 900;
            cursor:pointer;
            width: 100%;
        }
        .link{
            color: #c8efff;
            text-decoration:none;
            font-weight: 800;
        }
        .grid{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .span2{ grid-column: span 2; }
        @media (max-width: 1024px){
            body{ display:block; }
            .sidebar{ width:100%; position:relative; }
            .main{ padding: 14px; }
            .topbar{ flex-direction: column; align-items: stretch; }
            .card{ max-width: 100%; }
        }
        @media (max-width: 760px){
            .grid{ grid-template-columns: 1fr; }
            .span2{ grid-column: auto; }
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
            <a class="active" href="/admin/catalogs">✂️ Katalog</a>
            <a href="/admin/landing-page">🖥 Landing Page</a>
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
            <div>
                <h1>Edit Katalog</h1>
                <p>Perbarui informasi katalog gaya rambut.</p>
            </div>
            <a class="link" href="/admin/catalogs">← Kembali</a>
        </div>

        <div class="card">
            <form method="POST" action="/admin/catalogs/{{ $catalog->id }}">
                @csrf
                @method('PUT')
                <div class="grid">
                    <div>
                        <label for="name">Nama Katalog</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $catalog->name) }}" required>
                    </div>
                    <div>
                        <label for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $catalog->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="span2">
                        <label for="description">Deskripsi (opsional)</label>
                        <textarea id="description" name="description" rows="4">{{ old('description', $catalog->description) }}</textarea>
                    </div>
                    <div>
                        <label for="care_level">Level Perawatan</label>
                        <input id="care_level" name="care_level" type="text" value="{{ old('care_level', $catalog->care_level) }}" placeholder="Contoh: Sedang">
                    </div>
                    <div>
                        <label for="face_shape">Cocok untuk Bentuk Wajah</label>
                        <input id="face_shape" name="face_shape" type="text" value="{{ old('face_shape', $catalog->face_shape) }}" placeholder="Contoh: Oval, Bulat, Kotak">
                    </div>
                    <div class="span2">
                        <label for="hair_type">Jenis Rambut</label>
                        <input id="hair_type" name="hair_type" type="text" value="{{ old('hair_type', $catalog->hair_type) }}" placeholder="Contoh: Lurus, Bergelombang">
                    </div>
                    <div class="span2">
                        <label for="tips">Tips & Rekomendasi (1 tips per baris)</label>
                        <textarea id="tips" name="tips" rows="5" placeholder="Gunakan produk styling yang sesuai&#10;Potong rambut secara berkala">{{ old('tips', $catalog->tips) }}</textarea>
                    </div>
                    <div class="span2">
                        <label for="image_url">URL Foto Katalog</label>
                        <input id="image_url" name="image_url" type="url" value="{{ old('image_url', $catalog->image_url) }}" placeholder="https://example.com/foto.jpg">
                    </div>
                </div>

                <button class="btn" type="submit">Perbarui Katalog</button>
            </form>
        </div>
    </main>
</body>
</html>
