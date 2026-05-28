<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BarberSelect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:rgb(98, 95, 95);
            --secondary:rgb(6, 6, 6);
            --panel: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #dbe2f0;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .wrapper {
            width: 100%;
            max-width: 980px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 24px;
            padding: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.2);
        }

        .card {
            background: var(--panel);
            border-radius: 18px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        .visual {
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,.3), transparent 35%),
                linear-gradient(160deg,rgb(255, 255, 255) 0%,rgb(118, 118, 118) 55%,rgb(0, 0, 0) 100%);
            color: #fff;
            padding: 34px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand { font-weight: 700; letter-spacing: .2px; }
        .visual h3 { margin: 12px 0 8px; font-size: 1.8rem; }
        .visual p { margin: 0; opacity: .9; line-height: 1.6; }

        .form-area {
            padding: 34px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            margin: 0 0 8px;
            color: var(--text);
            font-size: 2rem;
        }

        .subtitle {
            margin: 0 0 18px;
            color: var(--muted);
        }

        label {
            display: block;
            margin: 12px 0 6px;
            color: #334155;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            outline: none;
            font-size: .95rem;
        }

        input:focus {
            border-color: rgba(102, 126, 234, 0.7);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.16);
        }

        .btn {
            margin-top: 18px;
            width: 100%;
            padding: 12px 14px;
            background: linear-gradient(120deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
        }

        .actions {
            margin-top: 14px;
            text-align: center;
            color: var(--muted);
        }

        .actions a {
            color: #334155;
            text-decoration: none;
            font-weight: 600;
        }

        .back-home {
            margin-top: 8px;
            display: inline-block;
            color: #475569;
            text-decoration: none;
            font-weight: 600;
        }

        .error {
            color: #b42318;
            margin-bottom: 8px;
            background: #ffeaea;
            border: 1px solid #f7b3b3;
            padding: 10px 12px;
            border-radius: 10px;
        }

        @media (max-width: 880px) {
            .card {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .visual {
                min-height: 220px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="visual">
                <div class="brand">BarberSelect</div>
                <div>
                    <h3>Buat Akun Mu</h3>
                    <p>
                        Daftar untuk mulai menemukan gaya rambut yang paling cocok,
                        dapatkan rekomendasi AI personal, dan eksplor katalog modern.
                    </p>
                </div>
                <small>Elegant • Modern • Personalized</small>
            </div>

            <div class="form-area">
                <h2>Daftar BarberSelect</h2>
                <p class="subtitle">Buat akun baru Anda sekarang.</p>
                @if ($errors->any())
                    <div class="error">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="/register">
                    @csrf
                    <label for="name">Nama</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>

                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>

                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>

                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>

                    <button class="btn" type="submit">Daftar</button>
                </form>

                <div class="actions">
                    Sudah punya akun? <a href="/login">Login</a>
                </div>
                <div class="actions">
                    <a class="back-home" href="/">← Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
