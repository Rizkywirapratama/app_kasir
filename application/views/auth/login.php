<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — NOL DERAJAT COFFEE POS</title>
    <meta name="description" content="Masuk ke sistem Point of Sale Nol Derajat Coffee">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-base: #06070d;
            --bg-1: #0a0d18;
            --bg-2: rgba(167, 139, 250, 0.08);
            --panel: transparent;
            --line: rgba(255,255,255,0.1);
            --text: #f5f7ff;
            --muted: rgba(255,255,255,0.72);
            --soft: rgba(255,255,255,0.5);
            --icon: #cbd5e1;
            --placeholder: rgba(255,255,255,0.42);
            --shadow: rgba(0, 0, 0, 0.45);
            --accent: #facc15;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-base);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
            background-image:
                radial-gradient(circle at 18% 15%, rgba(124,58,237,0.18), transparent 25%),
                radial-gradient(circle at 82% 85%, rgba(59,130,246,0.15), transparent 20%),
                linear-gradient(180deg, #03050a 0%, #050814 100%);
        }

        .auth-wrapper {
            width: min(100%, 760px);
            position: relative;
            margin: 0 auto 0 0;
        }

        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.2rem;
            margin-bottom: 18px;
        }

        .brand-icon {
            font-size: 2.8rem;
            color: var(--icon);
            line-height: 1;
            margin-bottom: 8px;
        }

        .brand-name {
            font-size: clamp(3.2rem, 5vw, 6rem);
            font-weight: 800;
            letter-spacing: -0.08em;
            line-height: 0.9;
            color: var(--text);
            text-transform: uppercase;
        }

        .brand-tagline {
            font-size: 1.7rem;
            color: rgba(255,255,255,0.68);
            font-weight: 500;
            margin-top: 8px;
        }

        .auth-card {
            width: 100%;
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 0;
        }

        .card-heading {
            font-size: clamp(3rem, 4vw, 5rem);
            font-weight: 800;
            letter-spacing: -0.07em;
            margin: 18px 0 10px;
            line-height: 1.05;
        }

        .card-sub {
            font-size: 1.5rem;
            color: var(--muted);
            line-height: 1.5;
            margin-bottom: 22px;
        }

        form {
            display: block;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            color: var(--text);
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .relative {
            position: relative;
        }

        .absolute {
            position: absolute;
        }

        .left-3 { left: 14px; }
        .right-3 { right: 14px; }
        .top-1\/2 { top: 50%; }
        .-translate-y-1\/2 { transform: translateY(-50%); }

        .input-icon,
        .btn-eye,
        .demo-item-icon {
            color: var(--soft);
        }

        .input-icon {
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            pointer-events: none;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            background: rgba(255,255,255,0.02);
            color: var(--text);
            padding: 18px 52px 18px 52px;
            font-size: 1.2rem;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: var(--placeholder);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: rgba(196,181,253,0.8);
            box-shadow: 0 0 0 3px rgba(167,139,250,0.12);
            background: rgba(255,255,255,0.03);
        }

        .btn-eye {
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 0.98rem;
            padding: 0;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-login {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border: none;
            border-radius: 14px;
            background: transparent;
            color: var(--text);
            font-size: 1.7rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            padding: 18px 18px;
            cursor: pointer;
            transition: transform .2s ease, opacity .2s ease;
            text-transform: uppercase;
            margin-top: 6px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
        }

        .btn-login i {
            font-size: 1rem;
        }

        .auth-divider {
            position: relative;
            margin: 26px 0 16px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            color: var(--muted);
            font-size: 1.08rem;
            font-weight: 700;
            text-transform: none;
        }

        .auth-divider::before {
            content: "";
            display: inline-block;
            width: 18px;
            height: 18px;
            margin-right: 10px;
            background: linear-gradient(180deg, #fff 0%, #d8dee7 100%);
            border-radius: 4px;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.2);
        }

        .demo-box {
            margin-top: 6px;
        }

        .demo-grid {
            display: grid;
            gap: 12px;
            margin-top: 12px;
        }

        .demo-item {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            color: var(--text);
            padding: 4px 0;
        }

        .demo-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 10px;
        }

        .demo-item:hover {
            opacity: 0.9;
        }

        .demo-item-role {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.6rem;
            font-weight: 700;
            min-width: 150px;
        }

        .demo-item-cred {
            font-size: 1.3rem;
            color: var(--muted);
        }

        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid rgba(248,113,113,0.35);
            border-radius: 10px;
            background: rgba(239,68,68,0.08);
            color: #fecaca;
            margin: 12px 0 20px;
        }

        .auth-footer {
            margin-top: 26px;
            font-size: 1.15rem;
            color: rgba(255,255,255,0.6);
            text-align: left;
            letter-spacing: 0.01em;
        }

        @media (max-width: 480px) {
            body {
                padding: 24px 18px;
            }

            .brand-name {
                font-size: 2.1rem;
            }

            .card-heading {
                font-size: 2.2rem;
            }

            .btn-login {
                font-size: 1.02rem;
            }

            .demo-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        body {
            min-height: 100vh;
            height: auto;
            padding: 36px 20px;
        }

        .auth-wrapper {
            width: min(100%, 480px);
            margin: auto;
        }

        .auth-brand {
            align-items: center;
            text-align: center;
            gap: 0;
            margin-bottom: 22px;
        }

        .brand-icon {
            width: min(100%, 440px);
            height: auto;
            margin: 0 auto;
        }

        .brand-icon img {
            display: block;
            width: 100%;
            height: auto;
            object-fit: contain;
            object-position: center;
        }

        .brand-name {
            font-size: clamp(1.35rem, 5vw, 1.75rem);
            letter-spacing: .08em;
            line-height: 1.25;
        }

        .brand-tagline {
            margin-top: 4px;
            font-size: .88rem;
            letter-spacing: .04em;
        }

        .auth-card {
            padding: clamp(22px, 6vw, 34px);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 24px;
            background: linear-gradient(150deg, rgba(20, 23, 35, .96), rgba(10, 12, 20, .97));
            box-shadow: 0 24px 70px rgba(0, 0, 0, .42), inset 0 1px rgba(255, 255, 255, .05);
            backdrop-filter: blur(18px);
        }

        .card-heading {
            margin: 0 0 8px;
            font-size: clamp(1.65rem, 6vw, 2rem);
            letter-spacing: -.055em;
            line-height: 1.2;
        }

        .card-sub {
            margin-bottom: 24px;
            font-size: .9rem;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            margin-bottom: 8px;
            font-size: .88rem;
            letter-spacing: .01em;
        }

        .text-gray-300 {
            color: rgba(255, 255, 255, .82);
        }

        .text-gray-400 {
            color: rgba(255, 255, 255, .52);
        }

        input[type="text"],
        input[type="password"] {
            height: 52px;
            padding: 12px 46px;
            border-color: rgba(255, 255, 255, .12);
            border-radius: 13px;
            background: rgba(255, 255, 255, .045);
            font-size: .9rem;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: rgba(255, 255, 255, .38);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: rgba(245, 190, 106, .8);
            box-shadow: 0 0 0 4px rgba(245, 190, 106, .12);
            background: rgba(255, 255, 255, .06);
        }

        .input-icon {
            z-index: 1;
            left: 16px;
        }

        .btn-eye {
            right: 13px;
            color: rgba(255, 255, 255, .58);
        }

        .btn-eye:hover {
            color: #f5be6a;
        }

        .btn-login {
            min-height: 52px;
            margin-top: 4px;
            padding: 14px 18px;
            border: 1px solid rgba(255, 218, 165, .38);
            border-radius: 13px;
            background: linear-gradient(135deg, #d9a45f, #b97b3f);
            box-shadow: 0 10px 24px rgba(185, 123, 63, .22);
            color: #1b120b;
            font-size: .9rem;
            letter-spacing: .08em;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(185, 123, 63, .3);
            filter: brightness(1.06);
        }

        .btn-login:focus-visible,
        .btn-eye:focus-visible,
        .demo-item:focus-visible {
            outline: 3px solid rgba(245, 190, 106, .7);
            outline-offset: 3px;
        }

        .btn-login:disabled {
            cursor: wait;
            opacity: .75;
            transform: none;
        }

        .auth-divider {
            gap: 12px;
            margin: 24px 0 16px;
            color: rgba(255, 255, 255, .56);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: "";
            display: block;
            width: auto;
            height: 1px;
            flex: 1;
            margin: 0;
            border-radius: 0;
            background: rgba(255, 255, 255, .12);
            box-shadow: none;
        }

        .demo-box {
            margin-top: 0;
        }

        .demo-title {
            margin-bottom: 10px;
            color: rgba(255, 255, 255, .72);
            font-size: .82rem;
            font-weight: 600;
        }

        .demo-title i {
            margin-right: 5px;
            color: #e0ad6b;
        }

        .demo-grid {
            gap: 8px;
            margin-top: 0;
        }

        .demo-item {
            width: 100%;
            min-height: 54px;
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 12px;
            background: rgba(255, 255, 255, .035);
            font: inherit;
            text-align: left;
            transition: border-color .2s ease, background .2s ease, transform .2s ease;
        }

        .demo-item:hover {
            transform: translateY(-1px);
            border-color: rgba(245, 190, 106, .35);
            background: rgba(255, 255, 255, .065);
            opacity: 1;
        }

        .demo-item-role {
            min-width: 92px;
            gap: 7px;
            font-size: .86rem;
            font-weight: 700;
        }

        .demo-item-cred {
            font-size: .78rem;
            color: rgba(255, 255, 255, .58);
        }

        .alert-error {
            padding: 11px 13px;
            border-radius: 12px;
            font-size: .84rem;
        }

        .auth-footer {
            margin-top: 18px;
            text-align: center;
            font-size: .72rem;
            color: rgba(255, 255, 255, .42);
        }

        @media (max-width: 480px) {
            body {
                padding: 24px 16px;
            }

            .brand-name {
                font-size: clamp(1.25rem, 7vw, 1.55rem);
            }

            .card-heading {
                font-size: 1.65rem;
            }

            .btn-login {
                font-size: .88rem;
            }

            .demo-item {
                flex-direction: row;
                align-items: center;
            }

            .demo-item-role {
                min-width: 82px;
            }
        }

        @media (max-height: 700px) {
            body {
                align-items: flex-start;
            }
        }

        html,
        body {
            min-height: 100%;
            height: auto;
        }

        :root {
            --text: #202735;
            --muted: #667085;
            --soft: #8490a2;
            --placeholder: #929bad;
        }

        body {
            color: var(--text);
            background-color: #fff;
            background-image: none;
        }

        .auth-brand { margin-bottom: 18px; }

        .brand-icon {
            width: min(100%, 360px);
            height: auto;
            margin: 0 auto 12px;
        }

        .brand-icon img {
            display: block;
            width: 100%;
            height: auto;
            object-fit: contain;
            object-position: center;
        }

        .brand-name,
        .brand-tagline {
            display: none;
        }

        .auth-card {
            border-color: #e5e8ee;
            background: rgba(255, 255, 255, .97);
            box-shadow: 0 24px 60px rgba(31, 41, 55, .1), 0 2px 8px rgba(31, 41, 55, .04);
        }

        .card-heading,
        .form-label {
            color: #202735;
        }

        .card-sub {
            color: #667085;
        }

        .text-gray-300 {
            color: #475467;
        }

        .text-gray-400 {
            color: #7b8494;
        }

        input[type="text"],
        input[type="password"] {
            border-color: #dce1e8;
            background: #fff;
            color: #202735;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #929bad;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #c88b4a;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(200, 139, 74, .14);
        }

        .input-icon,
        .btn-eye {
            color: #7b8494;
        }

        .btn-eye:hover {
            color: #a4662e;
        }

        .btn-login {
            border-color: #1d4ed8;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            box-shadow: 0 10px 22px rgba(37, 99, 235, .22);
            color: #fff;
        }

        .btn-login:hover {
            box-shadow: 0 14px 26px rgba(37, 99, 235, .3);
        }

        .btn-login:focus-visible,
        .btn-eye:focus-visible,
        .demo-item:focus-visible {
            outline-color: rgba(37, 99, 235, .65);
        }

        .auth-divider {
            color: #7b8494;
        }

        .auth-divider::before,
        .auth-divider::after {
            background: #e5e8ee;
        }

        .demo-title {
            color: #667085;
        }

        .demo-title i {
            color: #a4662e;
        }

        .demo-item {
            border-color: #e5e8ee;
            background: #fff;
            color: #202735;
        }

        .demo-item:hover {
            border-color: #d8b98f;
            background: #fffaf3;
        }

        .demo-item-cred {
            color: #667085;
        }

        .alert-error {
            border-color: #f1c6ca;
            background: #fff4f4;
            color: #a52a35;
        }

        .auth-footer {
            color: #7b8494;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-brand">
            <div class="brand-icon">
                <img src="<?= base_url('assets/images/ICON.png') ?>" alt="Logo NOL DERAJAT COFFEE">
            </div>
        </div>

        <div class="auth-card">
            <?php if ($this->session->flashdata('error')): ?>
            <div class="alert-error">
                <i class="fas fa-circle-exclamation fa-fw"></i>
                <span><?= $this->session->flashdata('error') ?></span>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/proses_login') ?>" method="POST" id="loginForm">
                <div class="form-group mb-4">
                    <label for="username" class="form-label text-sm font-semibold text-gray-300">Username</label>
                    <div class="relative mt-2">
                        <span class="input-icon absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" id="username" class="w-full pl-10 pr-3 py-3 rounded-lg bg-white border border-slate-200 text-slate-900"
                               placeholder="Masukkan username Anda"
                               value="<?= set_value('username') ?>"
                               required autocomplete="username">
                    </div>
                </div>

                <div class="form-group mb-5">
                    <label for="password" class="form-label text-sm font-semibold text-gray-300">Password</label>
                    <div class="relative mt-2">
                        <span class="input-icon absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password"
                               class="pr-toggle w-full pl-10 pr-12 py-3 rounded-lg bg-white border border-slate-200 text-slate-900"
                               placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="btn-eye absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" onclick="togglePw()" id="eyeBtn" title="Tampilkan / Sembunyikan">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login w-full rounded-lg py-3 font-extrabold flex items-center justify-center gap-2" id="loginBtn">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    <span id="loginBtnText">MASUK SEKARANG</span>
                </button>
            </form>

        </div>

    </div>

    <script>
        function togglePw() {
            const pw = document.getElementById('password');
            const ic = document.getElementById('eyeIcon');
            if (pw.type === 'password') {
                pw.type = 'text';
                ic.className = 'fas fa-eye-slash';
            } else {
                pw.type = 'password';
                ic.className = 'fas fa-eye';
            }
        }

        function fillDemo(user, pass) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
            document.getElementById('username').dispatchEvent(new Event('input'));
        }

        const loginForm = document.getElementById('loginForm');
        let loginAudioContext = null;

        function playLoginSound(success) {
            if (!loginAudioContext) return;

            const frequencies = success ? [660, 880] : [440, 300];
            const now = loginAudioContext.currentTime;
            frequencies.forEach((frequency, index) => {
                const oscillator = loginAudioContext.createOscillator();
                const gain = loginAudioContext.createGain();
                const startsAt = now + index * 0.16;

                oscillator.type = 'sine';
                oscillator.frequency.value = frequency;
                gain.gain.setValueAtTime(0.0001, startsAt);
                gain.gain.exponentialRampToValueAtTime(0.14, startsAt + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, startsAt + 0.14);
                oscillator.connect(gain);
                gain.connect(loginAudioContext.destination);
                oscillator.start(startsAt);
                oscillator.stop(startsAt + 0.15);
            });
        }

        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const btn = document.getElementById('loginBtn');
            const txt = document.getElementById('loginBtnText');
            const icon = btn.querySelector('i');
            let originalIcon = icon.className;

            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                try {
                    loginAudioContext = loginAudioContext || new AudioContextClass();
                    await loginAudioContext.resume();
                } catch (error) {
                    console.warn('Suara login tidak tersedia:', error);
                }
            }

            btn.disabled = true;
            txt.textContent = 'MEMPROSES...';
            icon.className = 'fas fa-spinner fa-spin';

            try {
                const response = await fetch(loginForm.action, {
                    method: 'POST',
                    body: new FormData(loginForm),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();
                if (!response.ok || result.status !== 'success' || typeof result.redirect !== 'string') {
                    playLoginSound(false);
                    let alert = document.getElementById('loginError');
                    if (!alert) {
                        alert = document.createElement('div');
                        alert.id = 'loginError';
                        alert.className = 'alert-error';
                        alert.innerHTML = '<i class="fas fa-circle-exclamation fa-fw"></i><span></span>';
                        loginForm.parentNode.insertBefore(alert, loginForm);
                    }
                    alert.querySelector('span').textContent = result.message || 'Login gagal. Silakan coba lagi.';
                    return;
                }

                playLoginSound(true);
                await new Promise(resolve => setTimeout(resolve, 350));
                window.location.href = result.redirect;
            } catch (error) {
                playLoginSound(false);
                let alert = document.getElementById('loginError');
                if (!alert) {
                    alert = document.createElement('div');
                    alert.id = 'loginError';
                    alert.className = 'alert-error';
                    alert.innerHTML = '<i class="fas fa-circle-exclamation fa-fw"></i><span></span>';
                    loginForm.parentNode.insertBefore(alert, loginForm);
                }
                alert.querySelector('span').textContent = 'Tidak dapat menghubungi server. Periksa koneksi dan coba lagi.';
            } finally {
                btn.disabled = false;
                txt.textContent = 'MASUK SEKARANG';
                icon.className = originalIcon;
            }
        });
    </script>
</body>
</html>
