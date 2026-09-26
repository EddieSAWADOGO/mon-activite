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
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50"
      x-data="appData()"
      x-init="init()">

<script>
    function appData() {
        return {
            mobileMenuOpen: false,
            toast: {
                show: {{ session('success') || session('error') ? 'true' : 'false' }},
                type: '{{ session('error') ? 'error' : 'success' }}',
                message: @json(session('success') ?? session('error') ?? '')
            },
            init() {},
            showToast(msg, type) {
                this.toast.message = msg;
                this.toast.type = type || 'success';
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 4000);
            }
        };
    }
</script>

    <!-- Toast Notification -->
    <div x-cloak x-show="toast.show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-4 flex items-start gap-3">

        <template x-if="toast.type === 'success'">
            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" />
        </template>
        <template x-if="toast.type === 'error'">
            <x-heroicon-o-x-circle class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
        </template>

        <div class="flex-1 text-sm font-medium text-slate-800" x-text="toast.message"></div>

        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
            <x-heroicon-o-x-mark class="w-4 h-4" />
        </button>
    </div>

    <!-- Layout -->
    <div class="min-h-screen flex">

        <!-- ── Sidebar Desktop (fixe) ── -->
        <aside class="hidden sm:flex sm:flex-col sm:w-64 sm:fixed sm:inset-y-0 sm:left-0 z-30
                      bg-white border-r border-slate-200 flex-shrink-0 h-screen">

            <!-- En-tête Sidebar -->
            <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 p-1.5 flex items-center justify-center shrink-0">
                    <img src="/logo.webp" alt="Mon-Activité" class="w-full h-full object-contain">
                </div>
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
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all
                              {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-squares-2x2 class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Tableau de bord</span>
                    </a>
                </div>

                <!-- Commercial -->
                <div class="space-y-0.5">
                    <p class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Commercial</p>

                    <a href="{{ route('ventes.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('ventes.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-arrow-up-tray class="w-5 h-5 {{ request()->routeIs('ventes.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Ventes</span>
                    </a>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('achats.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                                      {{ request()->routeIs('achats.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-arrow-down-tray class="w-5 h-5 {{ request()->routeIs('achats.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Achats</span>
                            </a>
                        @endif
                    @endauth

                    <a href="{{ route('factures.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('factures.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-document-text class="w-5 h-5 {{ request()->routeIs('factures.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Factures</span>
                    </a>

                    <a href="{{ route('paiements.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('paiements.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-banknotes class="w-5 h-5 {{ request()->routeIs('paiements.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Règlements</span>
                    </a>
                </div>

                <!-- Catalogue & Stock -->
                <div class="space-y-0.5">
                    <p class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Catalogue & Stock</p>

                    <a href="{{ route('produits.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('produits.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-cube class="w-5 h-5 {{ request()->routeIs('produits.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Produits</span>
                    </a>

                    <a href="{{ route('stock.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('stock.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-archive-box class="w-5 h-5 {{ request()->routeIs('stock.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Stock</span>
                    </a>

                    @auth
                        @if (auth()->user()->canManageInventoryOperations())
                            <a href="{{ route('retours.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                                      {{ request()->routeIs('retours.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-arrow-uturn-left class="w-5 h-5 {{ request()->routeIs('retours.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Retours clients</span>
                            </a>

                            <a href="{{ route('pertes.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                                      {{ request()->routeIs('pertes.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-x-circle class="w-5 h-5 {{ request()->routeIs('pertes.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Pertes</span>
                            </a>

                            <a href="{{ route('reconditionnement.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                                      {{ request()->routeIs('reconditionnement.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
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
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('clients.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-user class="w-5 h-5 {{ request()->routeIs('clients.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Clients</span>
                    </a>

                    <a href="{{ route('fournisseurs.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                              {{ request()->routeIs('fournisseurs.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <x-heroicon-o-truck class="w-5 h-5 {{ request()->routeIs('fournisseurs.*') ? 'text-white' : 'text-slate-400' }}" />
                        <span>Fournisseurs</span>
                    </a>

                    @auth
                        @if (auth()->user()->canAccessHistory())
                            <a href="{{ route('historique.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                                      {{ request()->routeIs('historique.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <x-heroicon-o-clock class="w-5 h-5 {{ request()->routeIs('historique.*') ? 'text-white' : 'text-slate-400' }}" />
                                <span>Historique</span>
                            </a>
                        @endif

                        @if (auth()->user()->canManageUsers())
                            <a href="{{ route('utilisateurs.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                                      {{ request()->routeIs('utilisateurs.*') ? 'bg-emerald-600 text-white font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
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
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold text-emerald-700">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="flex-1 truncate min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->role?->label() }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
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
        <div class="flex-1 sm:pl-64 flex flex-col min-h-screen w-full min-w-0">

            <!-- ── Header Mobile (à l'intérieur du contenu) ── -->
            <header class="sm:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 p-1 flex items-center justify-center border border-slate-200">
                        <img src="/logo.webp" alt="Mon-Activité" class="w-full h-full object-contain">
                    </div>
                    <span class="font-bold text-base text-slate-900 tracking-tight">Mon-Activité</span>
                </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 text-slate-600 rounded-xl hover:bg-slate-100 transition">
                    <x-heroicon-o-bars-3 class="w-5 h-5" x-show="!mobileMenuOpen" />
                    <x-heroicon-o-x-mark class="w-5 h-5" x-show="mobileMenuOpen" x-cloak />
                </button>
            </header>

            <!-- ── Menu Mobile (slide-over, fixed donc hors du flux) ── -->
            <div x-cloak x-show="mobileMenuOpen"
                 class="sm:hidden fixed inset-0 z-40 bg-white p-5 flex flex-col overflow-y-auto">

                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <img src="/logo.webp" alt="Logo" class="w-7 h-7 object-contain">
                        <span class="font-bold text-slate-900">Mon-Activité</span>
                    </div>
                    <button @click="mobileMenuOpen = false" class="p-2 text-slate-400 hover:text-slate-700 rounded-lg">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2.5 flex-1">
                    <a @click="mobileMenuOpen = false" href="{{ route('dashboard') }}"
                       class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center gap-2 text-sm font-semibold text-slate-700 border border-slate-200 hover:bg-slate-100 transition">
                        <x-heroicon-o-squares-2x2 class="w-6 h-6 text-emerald-600" />
                        <span>Tableau de bord</span>
                    </a>
                    <a @click="mobileMenuOpen = false" href="{{ route('ventes.index') }}"
                       class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center gap-2 text-sm font-semibold text-slate-700 border border-slate-200 hover:bg-slate-100 transition">
                        <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-600" />
                        <span>Ventes</span>
                    </a>
                    <a @click="mobileMenuOpen = false" href="{{ route('produits.index') }}"
                       class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center gap-2 text-sm font-semibold text-slate-700 border border-slate-200 hover:bg-slate-100 transition">
                        <x-heroicon-o-cube class="w-6 h-6 text-emerald-600" />
                        <span>Produits</span>
                    </a>
                    <a @click="mobileMenuOpen = false" href="{{ route('stock.index') }}"
                       class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center gap-2 text-sm font-semibold text-slate-700 border border-slate-200 hover:bg-slate-100 transition">
                        <x-heroicon-o-archive-box class="w-6 h-6 text-emerald-600" />
                        <span>Stock</span>
                    </a>
                    <a @click="mobileMenuOpen = false" href="{{ route('factures.index') }}"
                       class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center gap-2 text-sm font-semibold text-slate-700 border border-slate-200 hover:bg-slate-100 transition">
                        <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600" />
                        <span>Factures</span>
                    </a>
                    <a @click="mobileMenuOpen = false" href="{{ route('clients.index') }}"
                       class="p-4 bg-slate-50 rounded-2xl flex flex-col items-center gap-2 text-sm font-semibold text-slate-700 border border-slate-200 hover:bg-slate-100 transition">
                        <x-heroicon-o-user class="w-6 h-6 text-emerald-600" />
                        <span>Clients</span>
                    </a>
                </div>

                @auth
                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ auth()->user()->role?->label() }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl border border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </div>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full space-y-6">
                @if (isset($header))
                    <div class="mb-2">{{ $header }}</div>
                @endif

                {{ $slot }}
            </main>
        </div>

    </div>



</body>
</html>
