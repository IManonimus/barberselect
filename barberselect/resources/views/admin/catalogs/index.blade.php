<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Katalog - BarberSelect</title>
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
            --danger:#fb7185;
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
        .btn-primary{
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(56,189,248,0.22);
            background: rgba(56,189,248,0.15);
            color: #c8efff;
            font-weight: 800;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:8px;
            white-space: nowrap;
        }
        .panel{
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px;
            box-shadow: var(--shadow);
        }
        .alert{
            padding: 12px 14px;
            border-radius: 14px;
            margin-bottom: 14px;
            border: 1px solid rgba(34,197,94,0.25);
            background: rgba(34,197,94,0.12);
            color: #d1fae5;
            font-weight: 700;
        }
        .table{
            width: 100%;
            border-collapse: collapse;
            overflow:hidden;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: var(--panel2);
        }
        th, td{
            padding: 12px 12px;
            border-bottom: 1px solid rgba(148,163,184,0.12);
            text-align:left;
            font-size: .92rem;
        }
        th{
            color: #cbd5e1;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            background: rgba(2, 6, 23, 0.20);
        }
        tr:hover td{ background: rgba(148,163,184,0.06); }
        .thumb{
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid rgba(148,163,184,0.18);
        }
        .actions{
            display:flex;
            gap: 10px;
            align-items:center;
            flex-wrap: wrap;
        }
        .actions a{
            color: #c8efff;
            text-decoration:none;
            font-weight: 800;
        }
        .actions button{
            background: rgba(251,113,133,0.14);
            border: 1px solid rgba(251,113,133,0.25);
            color: #fecdd3;
            border-radius: 999px;
            padding: 6px 10px;
            cursor:pointer;
            font-weight: 800;
        }
        @media (max-width: 1024px){
            body{ display:block; }
            .sidebar{ width:100%; position:relative; }
            .main{ padding: 14px; }
            .topbar{ flex-direction: column; align-items: stretch; }
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
                <h1>Manajemen Katalog</h1>
                <p>Tambah, edit, atau hapus item katalog gaya rambut.</p>
            </div>
            <a href="/admin/catalogs/create" class="btn-primary">➕ Tambah Katalog</a>
        </div>

        <div class="panel">
            @if(session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            <table class="table">
                <thead>
                    <tr><th style="width:90px;">ID</th><th style="width:90px;">Foto</th><th>Nama</th><th>Kategori</th><th style="width:190px;">Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($catalogs as $catalog)
                        <tr>
                            <td>{{ $catalog->id }}</td>
                            <td>
                                @if ($catalog->image_url)
                                    <img src="{{ $catalog->image_url }}" alt="{{ $catalog->name }}" class="thumb">
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-weight:800;">{{ $catalog->name }}</td>
                            <td>{{ $catalog->category->name }}</td>
                            <td>
                                <div class="actions">
                                    <a href="/admin/catalogs/{{ $catalog->id }}/edit">Edit</a>
                                    <form method="POST" action="/admin/catalogs/{{ $catalog->id }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
