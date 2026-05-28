<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BarberSelect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg0:#0b1020;
            --bg1:#0f172a;
            --panel:#121a2b;
            --panel2:#0f1626;
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
        .brand strong{
            font-size: 1.05rem;
            letter-spacing: .2px;
        }
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
        .nav a:hover{
            background: rgba(148,163,184,0.10);
            border-color: rgba(148,163,184,0.18);
        }
        .nav a.active{
            background: rgba(56,189,248,0.14);
            border-color: rgba(56,189,248,0.22);
        }

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

        .topbar .title{
            display:flex;
            flex-direction: column;
            gap: 2px;
        }
        .topbar h1{
            margin:0;
            font-size: 1.05rem;
            letter-spacing: .2px;
        }
        .topbar p{
            margin:0;
            color: var(--muted);
            font-size: .9rem;
        }

        .avatar{
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: radial-gradient(circle at 30% 20%, rgba(56,189,248,0.9), rgba(99,102,241,0.45));
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight: 900;
        }

        .grid{
            display:grid;
            grid-template-columns: 2fr 1fr;
            gap: 14px;
        }

        .card{
            background: rgba(18, 24, 43, 0.72);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 14px 16px;
            box-shadow: var(--shadow);
        }

        .stat-row{
            display:grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 14px;
        }

        .stat{
            padding: 12px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(15, 22, 38, 0.65);
        }
        .stat small{
            color: var(--muted);
            font-weight: 700;
            display:block;
            margin-bottom: 6px;
        }
        .stat strong{
            font-size: 1.5rem;
            letter-spacing: .3px;
        }

        .chart{
            height: 180px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(56,189,248,0.12), rgba(56,189,248,0.02));
            overflow:hidden;
            position: relative;
        }

        .chart svg{
            width:100%;
            height:100%;
            display:block;
        }

        .table{
            width:100%;
            border-collapse: collapse;
            overflow:hidden;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(15, 22, 38, 0.55);
        }
        .table th, .table td{
            padding: 12px 12px;
            border-bottom: 1px solid rgba(148,163,184,0.12);
            text-align:left;
            font-size: .92rem;
        }
        .table th{
            color: #cbd5e1;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            background: rgba(2, 6, 23, 0.20);
        }
        .table tr:hover td{
            background: rgba(148,163,184,0.06);
        }

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

        .quick a{
            display:flex;
            justify-content: space-between;
            align-items:center;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid var(--border);
            color: var(--text);
            text-decoration:none;
            margin-bottom: 10px;
            background: rgba(15, 22, 38, 0.55);
        }
        .quick a:hover{
            background: rgba(148,163,184,0.08);
        }

        .activity-list{
            list-style:none;
            padding:0;
            margin:0;
            display:flex;
            flex-direction: column;
            gap: 10px;
        }

        .activity-item{
            border: 1px solid var(--border);
            background: rgba(15, 22, 38, 0.55);
            border-radius: 14px;
            padding: 10px 12px;
        }

        .activity-item .row{
            display:flex;
            align-items:center;
            justify-content: space-between;
            gap: 10px;
        }

        .activity-item .title{
            font-weight: 800;
            color: var(--text);
            font-size: .92rem;
        }

        .activity-item .sub{
            margin-top: 4px;
            color: var(--muted);
            font-size: .85rem;
        }

        @media (max-width: 1024px){
            body{ display:block; }
            .sidebar{ width:100%; position:relative; }
            .main{ padding: 14px; }
            .grid{ grid-template-columns: 1fr; }
            .stat-row{ grid-template-columns: 1fr; }
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
            <a class="active" href="/admin">📊 Dashboard</a>
            <a href="/admin/categories">🗂 Kategori</a>
            <a href="/admin/catalogs">✂️ Katalog</a>
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
            <div class="title">
                <h1>Dashboard</h1>
                <p>Ringkasan sistem & aktivitas terbaru</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="badge">👋 {{ $user->name }}</span>
                <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            </div>
        </div>

        <div class="grid">
            <section class="card">
                <div class="stat-row">
                    <div class="stat">
                        <small>Total Users</small>
                        <strong style="color: var(--primary)">{{ $totalUsers }}</strong>
                    </div>
                    <div class="stat">
                        <small>Total Kategori</small>
                        <strong style="color: var(--warn)">{{ $categoryCount }}</strong>
                    </div>
                    <div class="stat">
                        <small>Total Katalog</small>
                        <strong style="color: var(--accent)">{{ $catalogCount }}</strong>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;">
                    <strong>Aktivitas</strong>
                    <span class="badge">Ringkasan</span>
                </div>

                <ul id="activityList" class="activity-list">
                    @forelse ($latestActivities as $activity)
                        <li class="activity-item" data-activity-id="{{ $activity->id }}">
                            <div class="row">
                                <div class="title">{{ $activity->action }}</div>
                                <div class="badge">{{ $activity->created_at?->diffForHumans() }}</div>
                            </div>
                            <div class="sub">
                                {{ $activity->user?->name ?? 'System' }}
                                @if (!empty($activity->meta))
                                    · {{ json_encode($activity->meta) }}
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="activity-item">
                            <div class="row">
                                <div class="title">Belum ada aktivitas</div>
                                <div class="badge">—</div>
                            </div>
                            <div class="sub">Aktivitas akan muncul otomatis saat ada aksi admin/user.</div>
                        </li>
                    @endforelse
                </ul>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;">
                    <span class="badge">✅ Sistem aktif</span>
                    <span class="badge">🔒 Admin only</span>
                    <span class="badge">⚡ Gemini AI enabled</span>
                </div>
            </section>

            <aside class="card">
                <strong style="display:block;margin-bottom:10px;">Menu Cepat</strong>
                <div class="quick">
                    <a href="/admin/categories"><span>Kelola Kategori</span><span>→</span></a>
                    <a href="/admin/catalogs"><span>Kelola Katalog</span><span>→</span></a>
                    <a href="/admin/landing-page"><span>Kelola Landing Page</span><span>→</span></a>
                    <a href="/"><span>Beranda (User View)</span><span>→</span></a>
                    <a href="/admin/profile"><span>Edit Profil Admin</span><span>→</span></a>
                </div>

                <strong style="display:block;margin:16px 0 10px;">User Terbaru</strong>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($latestUsers as $index => $u)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </aside>
        </div>
    </main>

    <script>
        (function () {
            const activityList = document.getElementById('activityList');
            if (!activityList) return;

            const render = (items) => {
                if (!Array.isArray(items) || items.length === 0) {
                    activityList.innerHTML = `
                        <li class="activity-item">
                            <div class="row">
                                <div class="title">Belum ada aktivitas</div>
                                <div class="badge">—</div>
                            </div>
                            <div class="sub">Aktivitas akan muncul otomatis saat ada aksi admin/user.</div>
                        </li>
                    `;
                    return;
                }

                activityList.innerHTML = items.map((it) => {
                    const who = it.user?.name || 'System';
                    const meta = it.meta ? ` · ${JSON.stringify(it.meta)}` : '';
                    const time = it.created_at_human || '';
                    return `
                        <li class="activity-item" data-activity-id="${it.id}">
                            <div class="row">
                                <div class="title">${it.action}</div>
                                <div class="badge">${time}</div>
                            </div>
                            <div class="sub">${who}${meta}</div>
                        </li>
                    `;
                }).join('');
            };

            const poll = async () => {
                try {
                    const res = await fetch('/admin/activity/feed', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    render(data.items);
                } catch (e) {
                    // ignore
                }
            };

            setInterval(poll, 4000);
        })();
    </script>
</body>
</html>
