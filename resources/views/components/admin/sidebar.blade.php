<div class="w-64 bg-white shadow-lg min-h-screen">
    <nav class="p-4">
        <div class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.dashboard') ? 'bg-teal-50 text-teal-600 font-semibold' : 'text-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Tableau de bord</span>
            </a>

            <!-- Users Management -->
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.users.*') ? 'bg-teal-50 text-teal-600 font-semibold' : 'text-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Utilisateurs</span>
            </a>

            <!-- Divider -->
            <div class="border-t my-4"></div>

            <!-- POS (if user is cashier/manager) -->
            @if(auth()->user()->isCashier() || auth()->user()->isManager())
            <a href="{{ route('pos.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Point de vente</span>
            </a>
            @endif

            

            <!-- Reports (placeholder for future)
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition text-gray-400 cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Rapports</span>
                <span class="ml-auto text-xs bg-gray-200 px-2 py-1 rounded">Bientôt</span>
            </a> -->

            <!-- Settings (placeholder for future)
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition text-gray-400 cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Paramètres</span>
                <span class="ml-auto text-xs bg-gray-200 px-2 py-1 rounded">Bientôt</span>
            </a> -->
            <!-- Activity Logs -->
            
            <!-- BOOKS MANAGEMENT SECTION -->
            <div class="mb-2">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestion des livres</p>
            </div>
            <a href="{{ route('admin.zones.overview') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.zones.overview') ? 'bg-teal-100 text-teal-700' : 'text-gray-700 hover:bg-gray-100' }}">
    
    <span class="font-medium">Zones & Emplacement</span>
</a>


<li>
    <a href="{{ route('admin.magasinage.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-teal-50 hover:text-teal-900 rounded transition-all">
        <span class="material-icons mr-2"></span>
        Magasinage
    </a>
</li>


            <!-- Books (THIS IS THE IMPORTANT LINK) -->
            <a href="{{ route('admin.books.manage') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.books.manage') ? 'bg-teal-50 text-teal-600 font-semibold' : 'text-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Livres</span>
                <a href="{{ route('admin.activity-logs.index') }}" 
            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition {{ request()->routeIs('admin.activity-logs.*') ? 'bg-teal-50 text-teal-600 font-semibold' : 'text-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Journal d'activité</span>
            </a>
                <!-- Stock Alerts Link -->
<a href="{{ route('admin.books.stock-alerts') }}" 
   class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.books.stock-alerts') ? 'bg-teal-100 text-teal-700' : 'text-gray-700 hover:bg-gray-100' }}">
    <span class="text-2xl">📦</span>
    <div class="flex-1">
        <span class="font-medium">Alertes Stock</span>
        @php
            $alertCount = \App\Models\Book::outOfStock()->count() + \App\Models\Book::lowStock()->count();
        @endphp
        @if($alertCount > 0)
            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                {{ $alertCount }}
            </span>
        @endif
    </div>
</a>

            </a>
        </div>
    </nav>
</div>
