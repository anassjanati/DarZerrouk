{{-- resources/views/components/manager/sidebar.blade.php --}}
<aside x-data="{ open: true }"
       :class="open ? 'w-60 lg:w-64' : 'w-16'"
       class="hidden md:flex flex-col min-h-screen flex-shrink-0 transition-all duration-300 ease-in-out
              [background:linear-gradient(90deg,rgba(151,247,229,1)_0%,rgba(174,195,205,1)_50%,rgba(155,178,235,1)_100%)]
              shadow-[0_0_40px_rgba(15,23,42,0.18)]">

    {{-- Top: toggle --}}
    <div class="flex items-center justify-end px-2 pt-3 pb-2">
        <button @click="open = !open"
                class="h-8 w-8 flex items-center justify-center rounded-full bg-sky-600 text-white hover:bg-sky-700 text-xs">
            <svg x-show="open" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <svg x-show="!open" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-2 pb-4 space-y-5 text-xs">
        {{-- Principal --}}
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-semibold uppercase tracking-wide text-slate-600"
               x-show="open" x-transition>
                Principal
            </p>

            {{-- Manager Dashboard --}}
            <a href="{{ route('manager.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-2xl cursor-pointer transition-colors duration-150
                      {{ request()->routeIs('manager.dashboard')
                          ? 'bg-sky-600 text-white shadow-md'
                          : 'text-slate-800 hover:bg-white/60 hover:text-sky-700' }}">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl
                             {{ request()->routeIs('manager.dashboard') ? 'bg-white/20' : 'bg-white/80' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('manager.dashboard') ? 'text-white' : 'text-sky-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </span>
                <span x-show="open" x-transition>Tableau de bord</span>
            </a>

            {{-- POS (manager overview) --}}
            <a href="{{ route('pos.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-2xl cursor-pointer transition-colors duration-150
                      {{ request()->routeIs('pos.*')
                          ? 'bg-sky-600 text-white shadow-md'
                          : 'text-slate-800 hover:bg-white/60 hover:text-sky-700' }}">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl
                             {{ request()->routeIs('pos.*') ? 'bg-white/20' : 'bg-white/80' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('pos.*') ? 'text-white' : 'text-sky-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 2.3a1 1 0 00.7 1.7H17m0 0a2 2 0 100 4 2 2 0 000-4m-8 2a2 2 0 11-4 0 2 2 0 014 0"/>
                        </path>
                    </svg>
                </span>
                <span x-show="open" x-transition>Point de vente</span>
            </a>
        </div>

        {{-- Gestion des livres --}}
        <div class="space-y-1">
            <p class="px-3 text-[10px] font-semibold uppercase tracking-wide text-slate-600"
               x-show="open" x-transition>
                Gestion des livres
            </p>

            <a href="{{ route('manager.books.manage') }}""
               class="flex items-center gap-3 px-3 py-2 rounded-2xl cursor-pointer transition-colors duration-150
                      {{ request()->routeIs('admin.books.manage')
                          ? 'bg-sky-600 text-white shadow-md'
                          : 'text-slate-800 hover:bg-white/60 hover:text-sky-700' }}">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl
                             {{ request()->routeIs('admin.books.manage') ? 'bg-white/20' : 'bg-white/80' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.books.manage') ? 'text-white' : 'text-sky-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.25v13m0-13C10.8 5.5 9.25 5 7.5 5S4.17 5.5 3 6.25v13C4.17 18.5 5.75 18 7.5 18s3.3.5 4.5 1.25m0-13C13.2 5.5 14.75 5 16.5 5s3.33.5 4.5 1.25v13C19.83 18.5 18.25 18 16.5 18s-3.3.5-4.5 1.25"/>
                    </svg>
                </span>
                <span x-show="open" x-transition>Livres</span>
            </a>

            <a href="{{ route('manager.magasinage.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-2xl cursor-pointer transition-colors duration-150
                      {{ request()->routeIs('admin.magasinage.*')
                          ? 'bg-sky-600 text-white shadow-md'
                          : 'text-slate-800 hover:bg-white/60 hover:text-sky-700' }}">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl
                             {{ request()->routeIs('admin.magasinage.*') ? 'bg-white/20' : 'bg-white/80' }}">
                    <span class="text-[11px] font-semibold {{ request()->routeIs('admin.magasinage.*') ? 'text-white' : 'text-sky-700' }}">
                        M
                    </span>
                </span>
                <span x-show="open" x-transition class="font-medium">Magasinage</span>
            </a>

            <a href="{{ route('manager.bon_de_commande.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-2xl cursor-pointer transition-colors duration-150
                      {{ request()->routeIs('admin.bon_de_commande.*')
                          ? 'bg-sky-600 text-white shadow-md'
                          : 'text-slate-800 hover:bg-white/60 hover:text-sky-700' }}">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl
                             {{ request()->routeIs('admin.bon_de_commande.*') ? 'bg-white/20' : 'bg-white/80' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.bon_de_commande.*') ? 'text-white' : 'text-sky-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <span x-show="open" x-transition class="font-medium">Bons de Commande</span>
            </a>

            <a href="{{ route('manager.stocks.transfer') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-2xl cursor-pointer transition-colors duration-150
                      {{ request()->routeIs('admin.stocks.transfer')
                          ? 'bg-sky-600 text-white shadow-md'
                          : 'text-slate-800 hover:bg-white/60 hover:text-sky-700' }}">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl
                             {{ request()->routeIs('admin.stocks.transfer') ? 'bg-white/20' : 'bg-white/80' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.stocks.transfer') ? 'text-white' : 'text-sky-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </span>
                <span x-show="open" x-transition class="font-medium">Transferts de Stock</span>
            </a>
        </div>
    </nav>
</aside>
