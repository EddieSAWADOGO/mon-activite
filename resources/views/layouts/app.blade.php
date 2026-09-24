<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Mon-Activité' }} - Gestion Commerciale</title>

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Tailwind CSS (Play CDN) with strict palette config -->
    <script>
        window.tailwind = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (via CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        /* Smooth scrolling & touch optimization */
        html { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50 flex flex-col sm:flex-row pb-16 sm:pb-0"
      x-data="{
          mobileMenuOpen: false,
          toast: {
              show: {{ session('success') || session('error') ? 'true' : 'false' }},
              type: '{{ session('error') ? 'error' : 'success' }}',
              message: '{{ session('success') ?? session('error') ?? '' }}'
          },
          showToast(msg, type = 'success') {
              this.toast.message = msg;
              this.toast.type = type;
              this.toast.show = true;
              setTimeout(() => { this.toast.show = false; }, 4000);
          }
      }">

    <!-- Toast Notification Banner -->
    <div x-cloak x-show="toast.show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-xl shadow-lg border border-slate-200 p-4 flex items-start gap-3">

        <template x-if="toast.type === 'success'">
            <x-heroicon-o-check-circle class="w-6 h-6 text-emerald-600 flex-shrink-0" />
        </template>
        <template x-if="toast.type === 'error'">
            <x-heroicon-o-x-circle class="w-6 h-6 text-red-600 flex-shrink-0" />
        </template>

        <div class="flex-1 text-sm font-medium text-slate-800" x-text="toast.message"></div>

        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600">
            <x-heroicon-o-plus class="w-5 h-5 rotate-45" />
        </button>
    </div>

    <!-- Desktop / Tablet Sidebar -->
    <aside class="hidden sm:flex sm:flex-col sm:w-64 bg-slate-900 text-white flex-shrink-0 min-h-screen">
        <!-- Logo Header -->
        <div class="p-4 border-b border-slate-800 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold">
                <x-heroicon-o-cube class="w-6 h-6" />
            </div>
            <div>
                <h1 class="font-bold text-base tracking-tight leading-tight">Mon-Activité</h1>
                <p class="text-xs text-slate-400">Gestion & Traçabilité</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                <span>Tableau de bord</span>
            </a>

            <a href="{{ route('produits.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('produits.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <x-heroicon-o-cube class="w-5 h-5" />
                <span>Produits</span>
            </a>

            <a href="{{ route('stock.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('stock.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <x-heroicon-o-archive-box class="w-5 h-5" />
                <span>Stock</span>
            </a>

            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('achats.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('achats.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                        <span>Achats</span>
                    </a>
                @endif
            @endauth

            <a href="{{ route('ventes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('ventes.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
                <span>Ventes</span>
            </a>

            <a href="{{ route('factures.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('factures.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <x-heroicon-o-document-text class="w-5 h-5" />
                <span>Factures</span>
            </a>

            <a href="{{ route('paiements.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('paiements.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <x-heroicon-o-banknotes class="w-5 h-5" />
                <span>Règlements</span>
            </a>

            <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('clients.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <x-heroicon-o-user class="w-5 h-5" />
                <span>Clients</span>
            </a>

            <a href="{{ route('fournisseurs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('fournisseurs.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <x-heroicon-o-truck class="w-5 h-5" />
                <span>Fournisseurs</span>
            </a>

            @auth
                @if (auth()->user()->canManageInventoryOperations())
                    <a href="{{ route('retours.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('retours.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-heroicon-o-arrow-uturn-left class="w-5 h-5" />
                        <span>Retours clients</span>
                    </a>

                    <a href="{{ route('pertes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('pertes.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-heroicon-o-x-circle class="w-5 h-5" />
                        <span>Pertes</span>
                    </a>

                    <a href="{{ route('reconditionnement.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('reconditionnement.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-heroicon-o-arrows-right-left class="w-5 h-5" />
                        <span>Reconditionnement</span>
                    </a>
                @endif

                @if (auth()->user()->canAccessHistory())
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                        <x-heroicon-o-clock class="w-5 h-5" />
                        <span>Historique & Suivi</span>
                    </a>
                @endif

                @if (auth()->user()->canManageUsers())
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                        <x-heroicon-o-users class="w-5 h-5" />
                        <span>Utilisateurs</span>
                    </a>
                @endif
            @endauth
        </nav>

        <!-- User profile footer -->
        <div class="p-4 border-t border-slate-800 flex items-center justify-between">
            @auth
                <div class="truncate">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ auth()->user()->role?->label() }}</p>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-white p-1 rounded-lg" title="Déconnexion">
                        <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                    </button>
                </form>
            @else
                <div class="text-xs text-slate-400">Non connecté</div>
            @endauth
        </div>
    </aside>

    <!-- Mobile Top Navigation Header -->
    <header class="sm:hidden bg-slate-900 text-white p-4 flex items-center justify-between sticky top-0 z-30 shadow-md">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold">
                <x-heroicon-o-cube class="w-5 h-5" />
            </div>
            <span class="font-bold text-base">Mon-Activité</span>
        </div>

        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-300 hover:text-white">
            <x-heroicon-o-squares-2x2 class="w-6 h-6" x-show="!mobileMenuOpen" />
            <x-heroicon-o-plus class="w-6 h-6 rotate-45" x-show="mobileMenuOpen" x-cloak />
        </button>
    </header>

    <!-- Mobile Slide-over Menu -->
    <div x-cloak x-show="mobileMenuOpen" class="sm:hidden fixed inset-0 z-40 bg-slate-900/95 p-6 flex flex-col justify-between overflow-y-auto">
        <div class="space-y-3">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <h2 class="text-lg font-bold text-white">Menu</h2>
                <button @click="mobileMenuOpen = false" class="text-slate-400 p-2">
                    <x-heroicon-o-plus class="w-6 h-6 rotate-45" />
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <a @click="mobileMenuOpen = false" href="#" class="p-3 bg-slate-800 rounded-xl text-slate-200 flex flex-col items-center gap-2 text-xs font-medium">
                    <x-heroicon-o-squares-2x2 class="w-6 h-6 text-emerald-500" />
                    <span>Tableau de bord</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('produits.index') }}" class="p-3 bg-slate-800 rounded-xl text-slate-200 flex flex-col items-center gap-2 text-xs font-medium">
                    <x-heroicon-o-cube class="w-6 h-6 text-emerald-500" />
                    <span>Produits</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('stock.index') }}" class="p-3 bg-slate-800 rounded-xl text-slate-200 flex flex-col items-center gap-2 text-xs font-medium">
                    <x-heroicon-o-archive-box class="w-6 h-6 text-emerald-500" />
                    <span>Stock</span>
                </a>
                @auth
                    @if (auth()->user()->isAdmin())
                        <a @click="mobileMenuOpen = false" href="{{ route('achats.index') }}" class="p-3 bg-slate-800 rounded-xl text-slate-200 flex flex-col items-center gap-2 text-xs font-medium">
                            <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-emerald-500" />
                            <span>Achats</span>
                        </a>
                    @endif
                @endauth
                <a @click="mobileMenuOpen = false" href="{{ route('ventes.index') }}" class="p-3 bg-slate-800 rounded-xl text-slate-200 flex flex-col items-center gap-2 text-xs font-medium">
                    <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-emerald-500" />
                    <span>Ventes</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('factures.index') }}" class="p-3 bg-slate-800 rounded-xl text-slate-200 flex flex-col items-center gap-2 text-xs font-medium">
                    <x-heroicon-o-document-text class="w-6 h-6 text-emerald-500" />
                    <span>Factures</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('clients.index') }}" class="p-3 bg-slate-800 rounded-xl text-slate-200 flex flex-col items-center gap-2 text-xs font-medium">
                    <x-heroicon-o-user class="w-6 h-6 text-emerald-500" />
                    <span>Clients</span>
                </a>
                <a @click="mobileMenuOpen = false" href="{{ route('fournisseurs.index') }}" class="p-3 bg-slate-800 rounded-xl text-slate-200 flex flex-col items-center gap-2 text-xs font-medium">
                    <x-heroicon-o-truck class="w-6 h-6 text-emerald-500" />
                    <span>Fournisseurs</span>
                </a>
            </div>
        </div>

        @auth
            <div class="pt-6 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ auth()->user()->role?->label() }}</p>
                </div>
            </div>
        @endauth
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full">
        @if (isset($header))
            <div class="mb-6">
                {{ $header }}
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Mobile Bottom Navigation Bar (Persistent touch bar) -->
    <nav class="sm:hidden fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-slate-200 flex items-center justify-around py-2 px-1 shadow-lg">
        <a href="{{ route('produits.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('produits.*') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-900' }} text-[10px] font-medium">
            <x-heroicon-o-cube class="w-5 h-5" />
            <span>Produits</span>
        </a>

        <a href="{{ route('stock.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('stock.*') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-900' }} text-[10px] font-medium">
            <x-heroicon-o-archive-box class="w-5 h-5" />
            <span>Stock</span>
        </a>

        <a href="{{ route('ventes.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('ventes.*') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-900' }} text-[10px] font-medium">
            <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
            <span>Ventes</span>
        </a>

        <a href="{{ route('factures.index') }}" class="flex flex-col items-center gap-0.5 {{ request()->routeIs('factures.*') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-900' }} text-[10px] font-medium">
            <x-heroicon-o-document-text class="w-5 h-5" />
            <span>Factures</span>
        </a>
    </nav>
</body>
</html>
