<nav class="bg-teal-600 text-white shadow-lg">
    <div class="container mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <div>
                    <h1 class="text-2xl font-bold">Dar Zerrouk</h1>
                    <p class="text-xs opacity-90">Administration</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <!-- Current Time -->
                <div class="text-right">
                    <p class="text-sm opacity-90">{{ now()->format('d/m/Y') }}</p>
                    <p class="text-xs opacity-75" id="current-time"></p>
                </div>

                <!-- User Menu -->
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs opacity-75">{{ auth()->user()->role->display_name }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-white text-teal-600 px-4 py-2 rounded-lg hover:bg-gray-100 font-semibold transition">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // Update time every second
    setInterval(() => {
        document.getElementById('current-time').textContent = new Date().toLocaleTimeString('fr-FR');
    }, 1000);
</script>
