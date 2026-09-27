<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Mon-Activité' }} - Gestion Commerciale</title>

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="/logo.webp">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN) -->
    <script>
        window.tailwind = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    fontSize: {
                        'xs':   ['0.8rem',   { lineHeight: '1.25rem' }],
                        'sm':   ['0.9rem',   { lineHeight: '1.4rem'  }],
                        'base': ['1rem',     { lineHeight: '1.6rem'  }],
                        'lg':   ['1.125rem', { lineHeight: '1.75rem' }],
                        'xl':   ['1.25rem',  { lineHeight: '1.85rem' }],
                        '2xl':  ['1.5rem',   { lineHeight: '2rem'    }],
                        '3xl':  ['1.875rem', { lineHeight: '2.25rem' }],
                    },
                    colors: {
                        emerald: {
                            50:  '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        html { -webkit-tap-highlight-color: transparent; scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 overflow-x-hidden selection:bg-emerald-500 selection:text-white"
      x-data="appData()"
      x-init="init()"
      @submit.window="handleFormSubmit($event)">

<script>
    function appData() {
        return {
            mobileMenuOpen: false,
            globalLoading: false,
            loadingText: 'Traitement en cours...',
            toast: {
                show: {{ session('success') || session('error') ? 'true' : 'false' }},
                type: '{{ session('error') ? 'error' : 'success' }}',
                message: @json(session('success') ?? session('error') ?? '')
            },
            init() {
                if (this.toast.show) {
                    setTimeout(() => { this.toast.show = false; }, 4000);
                }
            },
            showToast(msg, type) {
                this.toast.message = msg;
                this.toast.type = type || 'success';
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 4000);
            },
            showLoader(msg) {
                this.loadingText = msg || 'Traitement en cours...';
                this.globalLoading = true;
            },
            hideLoader() {
                this.globalLoading = false;
            },
            handleFormSubmit(e) {
                if (e.defaultPrevented) return;
                const form = e.target;
                if (!form || form.tagName !== 'FORM') return;

                const method = (form.getAttribute('method') || 'GET').toUpperCase();
                if (method === 'GET') return;

                const submitter = e.submitter || document.activeElement;
                const submitterText = submitter ? (submitter.textContent || '').toLowerCase() : '';
                const action = (form.action || '').toLowerCase();

                let msg = form.dataset.loadingText;
                if (!msg) {
                    if (action.includes('destroy') || action.includes('delete') || action.includes('discard') || submitterText.includes('supprim') || submitterText.includes('annuler')) {
                        msg = 'Suppression en cours...';
                    } else if (action.includes('reset')) {
                        msg = 'Réinitialisation en cours...';
                    } else if (action.includes('login')) {
                        msg = 'Connexion en cours...';
                    } else {
                        msg = 'Enregistrement en cours...';
                    }
                }
                this.showLoader(msg);
            }
        };
    }
</script>

    <!-- ── Toast Notification Adaptatif Mobile-First ── -->
    <div x-cloak x-show="toast.show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="-translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-3 left-3 right-3 sm:left-auto sm:right-4 z-[90] max-w-[calc(100vw-1.5rem)] sm:max-w-sm mx-auto sm:mx-0 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200/90 shadow-xl p-3.5 sm:p-4 flex items-start gap-3 text-slate-800 select-none">

        <template x-if="toast.type === 'success'">
            <div class="p-1 rounded-xl bg-emerald-100 text-emerald-600 shrink-0 mt-0.5">
                <x-heroicon-o-check-circle class="w-5 h-5" />
            </div>
        </template>
        <template x-if="toast.type === 'error'">
            <div class="p-1 rounded-xl bg-red-100 text-red-600 shrink-0 mt-0.5">
                <x-heroicon-o-x-circle class="w-5 h-5" />
            </div>
        </template>

        <div class="flex-1 text-xs sm:text-sm font-semibold text-slate-800 leading-snug break-words" x-text="toast.message"></div>

        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition shrink-0" aria-label="Fermer">
            <x-heroicon-o-x-mark class="w-4 h-4" />
        </button>
    </div>

    <!-- ── Modal Loading Overlay Global (Centré) ── -->
    <div x-cloak
         x-show="globalLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm select-none">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl max-w-xs sm:max-w-sm w-full text-center space-y-4 border border-slate-100 transform transition-all my-auto mx-auto">
            <div class="relative w-16 h-16 mx-auto flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-emerald-100"></div>
                <div class="absolute inset-0 rounded-full border-4 border-emerald-600 border-t-transparent animate-spin"></div>
                <x-heroicon-o-arrow-path class="w-7 h-7 text-emerald-600 animate-spin" />
            </div>
            <div class="space-y-1">
                <p class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-snug" x-text="loadingText"></p>
                <p class="text-xs text-slate-500 font-medium">Veuillez patienter un instant...</p>
            </div>
        </div>
    </div>

    <!-- Layout Container Principal -->
    <div class="min-h-screen bg-slate-50">

        <!-- ── Sidebar Desktop (fixe à gauche) ── -->
        <aside class="hidden sm:flex sm:flex-col sm:w-64 sm:fixed sm:inset-y-0 sm:left-0 z-30
                      bg-white border-r border-slate-200/80 flex-shrink-0 h-screen">

            <!-- En-tête Sidebar -->
            <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                <img src="/logo.webp" alt="Mon-Activité" class="w-9 h-9 object-contain shrink-0">
                <div class="truncate">
                    <p class="font-bold text-base tracking-tight text-slate-900 leading-tight">Mon-Activité</p>
                    <p class="text-xs text-slate-400 font-medium">Gestion Commerciale</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-3 space-y-5 overflow-y-auto">

                <!-- Dashboard -->
                <div>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all active:scale-98
                              {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Tableau de bord</span>
                    </a>
                </div>

                <!-- Commercial -->
                <div class="space-y-0.5">
                    <p class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Commercial</p>

                    <a href="{{ route('ventes.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                              {{ request()->routeIs('ventes.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-arrow-up-tray class="w-5 h-5 {{ request()->routeIs('ventes.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Ventes</span>
                    </a>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('achats.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                                      {{ request()->routeIs('achats.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-arrow-down-tray class="w-5 h-5 {{ request()->routeIs('achats.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Achats</span>
                            </a>
                        @endif
                    @endauth

                    <a href="{{ route('factures.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                              {{ request()->routeIs('factures.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-document-text class="w-5 h-5 {{ request()->routeIs('factures.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Factures</span>
                    </a>

                    <a href="{{ route('paiements.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                              {{ request()->routeIs('paiements.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-banknotes class="w-5 h-5 {{ request()->routeIs('paiements.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Règlements</span>
                    </a>
                </div>

                <!-- Catalogue & Stock -->
                <div class="space-y-0.5">
                    <p class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Catalogue & Stock</p>

                    <a href="{{ route('produits.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                              {{ request()->routeIs('produits.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-cube class="w-5 h-5 {{ request()->routeIs('produits.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Produits</span>
                    </a>

                    <a href="{{ route('stock.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                              {{ request()->routeIs('stock.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-archive-box class="w-5 h-5 {{ request()->routeIs('stock.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Stock</span>
                    </a>

                    @auth
                        @if (auth()->user()->canManageInventoryOperations())
                            <a href="{{ route('retours.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                                      {{ request()->routeIs('retours.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-arrow-uturn-left class="w-5 h-5 {{ request()->routeIs('retours.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Retours clients</span>
                            </a>

                            <a href="{{ route('pertes.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                                      {{ request()->routeIs('pertes.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-x-circle class="w-5 h-5 {{ request()->routeIs('pertes.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Pertes</span>
                            </a>

                            <a href="{{ route('reconditionnement.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                                      {{ request()->routeIs('reconditionnement.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-arrows-right-left class="w-5 h-5 {{ request()->routeIs('reconditionnement.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Reconditionnement</span>
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Partenaires -->
                <div class="space-y-0.5">
                    <p class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Partenaires & Suivi</p>

                    <a href="{{ route('clients.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                              {{ request()->routeIs('clients.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-user class="w-5 h-5 {{ request()->routeIs('clients.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Clients</span>
                    </a>

                    <a href="{{ route('fournisseurs.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                              {{ request()->routeIs('fournisseurs.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-truck class="w-5 h-5 {{ request()->routeIs('fournisseurs.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Fournisseurs</span>
                    </a>

                    @auth
                        @if (auth()->user()->canAccessHistory())
                            <a href="{{ route('historique.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                                      {{ request()->routeIs('historique.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-clock class="w-5 h-5 {{ request()->routeIs('historique.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Historique</span>
                            </a>
                        @endif

                        @if (auth()->user()->canManageUsers())
                            <a href="{{ route('utilisateurs.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all active:scale-98
                                      {{ request()->routeIs('utilisateurs.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-users class="w-5 h-5 {{ request()->routeIs('utilisateurs.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Utilisateurs</span>
                            </a>
                        @endif
                    @endauth
                </div>
            </nav>

            <!-- Profil utilisateur -->
            <div class="p-3 border-t border-slate-100">
                @auth
                    <div class="flex items-center gap-3 px-2 py-1.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold text-emerald-700">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="flex-1 truncate min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->role?->label() }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" data-loading-text="Déconnexion en cours...">
                            @csrf
                            <button type="submit"
                                    class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
                                    title="Déconnexion">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </aside>

        <!-- ── Contenu Principal ── -->
        <div class="sm:pl-64 flex flex-col min-h-screen w-full min-w-0">

            <!-- ── Header Mobile sticky iOS Glassmorphism style ── -->
            <header class="sm:hidden bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 py-3 flex items-center justify-between sticky top-0 z-30 w-full">
                <div class="flex items-center gap-2.5">
                    <img src="/logo.webp" alt="Mon-Activité" class="w-8 h-8 object-contain shrink-0">
                    <span class="font-extrabold text-base text-slate-900 tracking-tight">Mon-Activité</span>
                </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 text-slate-700 rounded-xl hover:bg-slate-100 active:scale-95 transition focus:outline-none"
                        aria-label="Menu principal">
                    <x-heroicon-o-bars-3 class="w-6 h-6" x-show="!mobileMenuOpen" />
                    <x-heroicon-o-x-mark class="w-6 h-6" x-show="mobileMenuOpen" x-cloak />
                </button>
            </header>

            <!-- ── Menu Mobile Complet (slide-over modal) ── -->
            <div x-cloak x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-[-10px]"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-[-10px]"
                 class="sm:hidden fixed inset-0 z-50 bg-white/95 backdrop-blur-xl p-5 flex flex-col overflow-y-auto">

                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2.5">
                        <img src="/logo.webp" alt="Logo" class="w-8 h-8 object-contain shrink-0">
                        <div>
                            <p class="font-extrabold text-slate-900 text-base leading-tight">Mon-Activité</p>
                            <p class="text-xs text-slate-400">Navigation</p>
                        </div>
                    </div>
                    <button @click="mobileMenuOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl bg-slate-100 active:scale-95 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2.5 flex-1 pb-6">
                    <a @click="mobileMenuOpen = false" href="{{ route('dashboard') }}"
                       class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                        <x-heroicon-o-squares-2x2 class="w-6 h-6 text-emerald-600" />
                        <span>Tableau de bord</span>
                    </a>

                    <a @click="mobileMenuOpen = false" href="{{ route('ventes.index') }}"
                       class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                        <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-600" />
                        <span>Ventes</span>
                    </a>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a @click="mobileMenuOpen = false" href="{{ route('achats.index') }}"
                               class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                                <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-emerald-600" />
                                <span>Achats</span>
                            </a>
                        @endif
                    @endauth

                    <a @click="mobileMenuOpen = false" href="{{ route('factures.index') }}"
                       class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                        <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600" />
                        <span>Factures</span>
                    </a>

                    <a @click="mobileMenuOpen = false" href="{{ route('paiements.index') }}"
                       class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                        <x-heroicon-o-banknotes class="w-6 h-6 text-emerald-600" />
                        <span>Règlements</span>
                    </a>

                    <a @click="mobileMenuOpen = false" href="{{ route('produits.index') }}"
                       class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                        <x-heroicon-o-cube class="w-6 h-6 text-emerald-600" />
                        <span>Produits</span>
                    </a>

                    <a @click="mobileMenuOpen = false" href="{{ route('stock.index') }}"
                       class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                        <x-heroicon-o-archive-box class="w-6 h-6 text-emerald-600" />
                        <span>Stock</span>
                    </a>

                    <a @click="mobileMenuOpen = false" href="{{ route('clients.index') }}"
                       class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                        <x-heroicon-o-user class="w-6 h-6 text-emerald-600" />
                        <span>Clients</span>
                    </a>

                    <a @click="mobileMenuOpen = false" href="{{ route('fournisseurs.index') }}"
                       class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                        <x-heroicon-o-truck class="w-6 h-6 text-emerald-600" />
                        <span>Fournisseurs</span>
                    </a>

                    @auth
                        @if (auth()->user()->canManageInventoryOperations())
                            <a @click="mobileMenuOpen = false" href="{{ route('retours.index') }}"
                               class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                                <x-heroicon-o-arrow-uturn-left class="w-6 h-6 text-emerald-600" />
                                <span>Retours</span>
                            </a>

                            <a @click="mobileMenuOpen = false" href="{{ route('pertes.index') }}"
                               class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                                <x-heroicon-o-x-circle class="w-6 h-6 text-emerald-600" />
                                <span>Pertes</span>
                            </a>

                            <a @click="mobileMenuOpen = false" href="{{ route('reconditionnement.index') }}"
                               class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                                <x-heroicon-o-arrows-right-left class="w-6 h-6 text-emerald-600" />
                                <span>Recond.</span>
                            </a>
                        @endif

                        @if (auth()->user()->canAccessHistory())
                            <a @click="mobileMenuOpen = false" href="{{ route('historique.index') }}"
                               class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                                <x-heroicon-o-clock class="w-6 h-6 text-emerald-600" />
                                <span>Historique</span>
                            </a>
                        @endif

                        @if (auth()->user()->canManageUsers())
                            <a @click="mobileMenuOpen = false" href="{{ route('utilisateurs.index') }}"
                               class="p-3.5 bg-slate-50/80 rounded-2xl flex flex-col items-center justify-center gap-1.5 text-xs font-semibold text-slate-700 border border-slate-200/80 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 active:scale-95 transition">
                                <x-heroicon-o-users class="w-6 h-6 text-emerald-600" />
                                <span>Utilisateurs</span>
                            </a>
                        @endif
                    @endauth
                </div>

                @auth
                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ auth()->user()->role?->label() }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" data-loading-text="Déconnexion en cours...">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 active:scale-95 transition">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </div>

            <!-- ── Zone de contenu principal ── -->
            <main class="flex-1 p-3 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full min-w-0 overflow-x-hidden space-y-5 sm:space-y-6 pb-24 sm:pb-8">
                @if (isset($header))
                    <div class="mb-2">{{ $header }}</div>
                @endif

                {{ $slot }}
            </main>
        </div>

    </div>

    <!-- ── Barre de navigation mobile tactile iOS Glassmorphism style ── -->
    <nav class="sm:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200/80 flex items-center justify-around py-2 px-1">
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-xl transition active:scale-95 {{ request()->routeIs('dashboard') ? 'text-emerald-600 font-bold' : 'text-slate-400' }} text-[10px]">
            <x-heroicon-o-squares-2x2 class="w-5 h-5" />
            <span>Accueil</span>
        </a>
        <a href="{{ route('ventes.index') }}"
           class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-xl transition active:scale-95 {{ request()->routeIs('ventes.*') ? 'text-emerald-600 font-bold' : 'text-slate-400' }} text-[10px]">
            <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
            <span>Ventes</span>
        </a>
        <a href="{{ route('produits.index') }}"
           class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-xl transition active:scale-95 {{ request()->routeIs('produits.*') ? 'text-emerald-600 font-bold' : 'text-slate-400' }} text-[10px]">
            <x-heroicon-o-cube class="w-5 h-5" />
            <span>Produits</span>
        </a>
        <a href="{{ route('stock.index') }}"
           class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-xl transition active:scale-95 {{ request()->routeIs('stock.*') ? 'text-emerald-600 font-bold' : 'text-slate-400' }} text-[10px]">
            <x-heroicon-o-archive-box class="w-5 h-5" />
            <span>Stock</span>
        </a>
        <a href="{{ route('factures.index') }}"
           class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-xl transition active:scale-95 {{ request()->routeIs('factures.*') ? 'text-emerald-600 font-bold' : 'text-slate-400' }} text-[10px]">
            <x-heroicon-o-document-text class="w-5 h-5" />
            <span>Factures</span>
        </a>
    </nav>

</body>
</html>
