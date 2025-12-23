<nav class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
        {{-- Left: logo + app name + role --}}
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-2xl bg-gradient-to-tr from-emerald-500 to-cyan-500 flex items-center justify-center shadow-md">
                <span class="text-white text-lg font-bold">DZ</span>
            </div>
            <div>
                <h1 class="text-sm font-semibold text-slate-900 leading-tight">Dar Zerrouk</h1>
                <p class="text-[11px] text-slate-500">
                    @if(auth()->user()->hasRole('admin')) Administration
                    @elseif(auth()->user()->hasRole('manager')) Management
                    @elseif(auth()->user()->hasRole('superviseur')) Supervision
                    @elseif(auth()->user()->hasRole('cashier')) Caisse
                    @endif
                </p>
            </div>
        </div>

        {{-- Center: search (optional, hide on small screens) 
        <div class="hidden md:flex flex-1 justify-center px-6">
            <div class="w-full max-w-md relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                    </svg>
                </span>
                <input
                    type="text"
                    placeholder="Rechercher un livre, un code‑barres..."
                    class="w-full pl-9 pr-3 py-1.5 rounded-full border border-slate-200 bg-slate-50 text-xs text-slate-700
                           focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white"
                >
            </div>
        </div>--}}

        {{-- Right: time + user --}}
        <div class="flex items-center gap-4">
            {{-- Date / time --}}
            <div class="hidden sm:block text-right">
                <p class="text-xs text-slate-500 font-medium">{{ now()->format('d/m/Y') }}</p>
                <p class="text-[11px] text-slate-400" id="current-time"></p>
            </div>

            {{-- User pill + logout --}}
            <div class="flex items-center gap-2">
                <div class="hidden sm:block text-right">
                    <p class="text-xs font-semibold text-slate-900">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-[11px] text-emerald-600">
                        {{ auth()->user()->role->display_name }}
                    </p>
                </div>
                <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-emerald-500 to-indigo-500
                            text-xs font-medium text-white flex items-center justify-center shadow">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ml-1">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center rounded-full bg-slate-900 text-white text-xs px-3 py-1.5
                                   hover:bg-slate-800 transition">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    setInterval(() => {
        const el = document.getElementById('current-time');
        if (el) el.textContent = new Date().toLocaleTimeString('fr-FR');
    }, 1000);
</script>
