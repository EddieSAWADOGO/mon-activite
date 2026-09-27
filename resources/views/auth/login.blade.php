<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Mon-Activité</title>
    <link rel="icon" type="image/webp" href="/logo.webp">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        :root {
            --font: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            --emerald: #059669;
            --emerald-dark: #047857;
            --emerald-light: #ecfdf5;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        html, body {
            height: 100%;
            font-family: var(--font);
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Layout ────────────────────────────── */
        .page {
            display: flex;
            min-height: 100vh;
            background: var(--slate-50);
        }

        /* ─── Panel gauche (illustration) ───────── */
        .panel-left {
            display: none;
            flex: 1;
            position: relative;
            overflow: hidden;
            background: var(--slate-900);
        }

        @media (min-width: 1024px) {
            .panel-left { display: flex; align-items: center; justify-content: center; }
        }

        .panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(5,150,105,0.25) 0%, transparent 70%),
                radial-gradient(ellipse 60% 80% at 80% 70%, rgba(5,150,105,0.15) 0%, transparent 70%),
                var(--slate-900);
        }

        .panel-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .panel-left-content {
            position: relative;
            z-index: 1;
            padding: 3rem;
            max-width: 480px;
            text-align: left;
        }

        .panel-logo {
            margin-bottom: 2.5rem;
        }

        .panel-logo img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .panel-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }

        .panel-title span {
            color: #34d399;
        }

        .panel-desc {
            font-size: 1rem;
            color: var(--slate-400);
            line-height: 1.7;
            font-weight: 400;
        }

        .panel-features {
            margin-top: 3rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--slate-300);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .feature-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #34d399;
            flex-shrink: 0;
        }

        /* ─── Panel droit (formulaire) ───────────── */
        .panel-right {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            background: #fff;
        }

        @media (min-width: 1024px) {
            .panel-right {
                width: 480px;
                flex-shrink: 0;
                border-left: 1px solid var(--slate-100);
            }
        }

        .form-container {
            width: 100%;
            max-width: 380px;
        }

        .mobile-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 1024px) {
            .mobile-logo { display: none; }
        }

        .mobile-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .form-heading {
            margin-bottom: 2rem;
            text-align: center;
        }

        @media (min-width: 1024px) {
            .form-heading {
                text-align: left;
            }
        }

        .form-heading h1 {
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.03em;
            line-height: 1.2;
        }

        .form-heading p {
            margin-top: 0.4rem;
            font-size: 0.9375rem;
            color: var(--slate-500);
            font-weight: 400;
        }

        /* ─── Alertes ─────────────────────────── */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 12px;
            font-size: 0.8125rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: var(--emerald-light);
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert svg { flex-shrink: 0; margin-top: 1px; }

        /* ─── Champs de formulaire ─────────────── */
        .field {
            margin-bottom: 1.125rem;
        }

        .field label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 0.4rem;
            text-align: left;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap svg.icon-left {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--slate-400);
            pointer-events: none;
        }

        .field input {
            width: 100%;
            height: 46px;
            padding: 0 0.875rem 0 2.625rem;
            border: 1.5px solid var(--slate-200);
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: var(--font);
            font-weight: 400;
            color: var(--slate-900);
            background: var(--slate-50);
            outline: none;
            transition: border-color 0.15s, background 0.15s;
            -webkit-appearance: none;
        }

        .field input:focus {
            border-color: var(--emerald);
            background: #fff;
        }

        .field input::placeholder {
            color: var(--slate-300);
        }

        .field input.has-right { padding-right: 2.75rem; }

        .btn-eye {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--slate-400);
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.15s;
        }

        .btn-eye:hover { color: var(--slate-600); }

        /* ─── Bouton de connexion ──────────────── */
        .btn-login {
            width: 100%;
            height: 48px;
            background: var(--emerald);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.9375rem;
            font-weight: 700;
            font-family: var(--font);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 0.25rem;
            transition: background 0.15s, transform 0.1s;
            letter-spacing: -0.01em;
        }

        .btn-login:hover {
            background: var(--emerald-dark);
        }

        .btn-login:active {
            transform: scale(0.985);
        }

        .btn-login svg {
            transition: transform 0.2s;
        }

        .btn-login:hover svg {
            transform: translateX(3px);
        }

        /* ─── Footer ──────────────────────────── */
        .form-footer {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.75rem;
            color: var(--slate-400);
        }
    </style>
</head>
<body>

<div class="page" x-data="{ showPwd: false, globalLoading: false }">

    <!-- Modal Loading Connexion -->
    <div x-cloak
         x-show="globalLoading"
         style="position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
        <div style="background: #fff; border-radius: 1.5rem; padding: 1.75rem 2rem; max-width: 320px; width: 100%; text-align: center; border: 1px solid #f1f5f9; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div style="position: relative; width: 4rem; height: 4rem; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center;">
                <div style="position: absolute; inset: 0; border-radius: 9999px; border: 4px solid #ecfdf5;"></div>
                <div style="position: absolute; inset: 0; border-radius: 9999px; border: 4px solid #059669; border-top-color: transparent; animation: spin 1s linear infinite;"></div>
                <svg style="width: 1.75rem; height: 1.75rem; color: #059669; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <p style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Connexion en cours...</p>
            <p style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Veuillez patienter un instant...</p>
        </div>
    </div>

    <!-- ─── Panneau gauche (illustration) ──────────────────── -->
    <div class="panel-left">
        <div class="panel-left-content">
            <div class="panel-logo">
                <img src="/logo.webp" alt="Mon-Activité">
            </div>

            <h2 class="panel-title">
                Gérez votre<br>activité avec<br><span>clarté</span>.
            </h2>

            <p class="panel-desc">
                Une plateforme complète pour piloter vos ventes,
                votre stock et votre équipe — au même endroit.
            </p>

            <div class="panel-features">
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    <span>Tableau de bord en temps réel</span>
                </div>
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    <span>Gestion des ventes & factures</span>
                </div>
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    <span>Suivi du stock & reconditionnement</span>
                </div>
                <div class="feature-item">
                    <div class="feature-dot"></div>
                    <span>Accès multi-rôles sécurisé</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Panneau droit (formulaire) ──────────────────────── -->
    <div class="panel-right">
        <div class="form-container">

            <!-- Logo mobile -->
            <div class="mobile-logo">
                <img src="/logo.webp" alt="Logo">
            </div>

            <!-- Titre -->
            <div class="form-heading">
                <h1>Connexion</h1>
                <p>Connexion à votre espace de gestion</p>
            </div>

            <!-- Alertes -->
            @if (session('success'))
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Formulaire -->
            <form method="POST" action="{{ route('login.store') }}" @submit="globalLoading = true">
                @csrf

                <!-- Email -->
                <div class="field">
                    <label for="email">Adresse e-mail</label>
                    <div class="input-wrap">
                        <svg class="icon-left" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="votre@email.com"
                            autocomplete="email"
                            autofocus
                            required
                        >
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrap">
                        <svg class="icon-left" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input
                            id="password"
                            :type="showPwd ? 'text' : 'password'"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                            class="has-right"
                        >
                        <button type="button" class="btn-eye" @click="showPwd = !showPwd" tabindex="-1">
                            <svg x-show="!showPwd" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPwd" x-cloak width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.982 8.982 0 013.122-.663c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-1.785 1.785a3 3 0 01-4.243-4.243m4.243 4.243L3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Bouton -->
                <button type="submit" class="btn-login" style="margin-top: 1.5rem;">
                    <span>Se connecter</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- Footer -->
            <div class="form-footer">
                Mon-Activité &copy; {{ date('Y') }}
            </div>
        </div>
    </div>

</div>

</body>
</html>
