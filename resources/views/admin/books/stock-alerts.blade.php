@extends('layouts.admin')

@section('title', 'Alertes Stock')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">📦 Alertes de Stock</h1>
            <p class="text-gray-600">Livres en rupture ou stock faible</p>
        </div>
        <a href="{{ route('admin.books.manage') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 font-semibold">
            ← Retour à l'inventaire
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Rupture de stock</p>
                <p class="text-3xl font-bold text-red-600 mt-2">
                    {{ \App\Models\Book::outOfStock()->count() }}
                </p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Stock faible</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">
                    {{ \App\Models\Book::lowStock()->count() }}
                </p>
            </div>
            <div class="bg-orange-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-medium">Total alertes</p>
                <p class="text-3xl font-bold text-teal-600 mt-2">
                    {{ \App\Models\Book::outOfStock()->count() + \App\Models\Book::lowStock()->count() }}
                </p>
            </div>
            <div class="bg-teal-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.books.stock-alerts') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Titre, ISBN, Auteur..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                />
            </div>

            <!-- Stock Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">État du stock</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Tous</option>
                    <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Rupture</option>
                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Stock faible</option>
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    <option value="">Toutes</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2 items-end">
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 font-semibold flex-1">
                    🔍 Filtrer
                </button>
                <a href="{{ route('admin.books.stock-alerts') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    ↻
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Results Table -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
        <p class="text-sm text-gray-600">
            <strong>{{ $books->total() }}</strong> livre(s) trouvé(s)
        </p>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                🖨️ Imprimer
            </button>
            <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                📊 Export Excel
            </button>
        </div>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Actuel</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seuil Min</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">État</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($books as $book)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div>
                        <div class="text-sm font-medium text-gray-900" dir="auto">{{ $book->title }}</div>
                        <div class="text-xs text-gray-500">{{ $book->barcode ?? $book->isbn ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-500" dir="auto">{{ $book->author->name ?? '-' }}</div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500" dir="auto">
                    {{ $book->category->name ?? '-' }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $book->zone->code ?? 'N/A' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    {{ number_format($book->retail_price) }} DH
                </td>
                <td class="px-6 py-4">
                    <span class="text-lg font-bold {{ $book->is_out_of_stock ? 'text-red-600' : 'text-orange-600' }}">
                        {{ $book->stock_quantity }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $book->min_stock_level ?? $book->reorder_level ?? 5 }}
                </td>
                <td class="px-6 py-4">
                    @if($book->is_out_of_stock)
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 border border-red-300">
                ⚠️ RUPTURE
            </span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 border border-orange-300">
                            ⚡ STOCK FAIBLE
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right text-sm font-medium">
                    <button onclick="showReorderModal({{ $book->id }})" class="text-green-600 hover:text-green-900 mr-3" title="Commander">
                        📦
                    </button>
                    <a href="{{ route('admin.books.manage') }}?barcode_scan={{ $book->barcode }}" class="text-teal-600 hover:text-teal-900" title="Voir détails">
                        👁️
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                    ✅ Aucune alerte de stock! Tous les livres sont bien approvisionnés.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $books->withQueryString()->links() }}
</div>

<script>
function showReorderModal(bookId) {
    alert('Commander le livre ID: ' + bookId + '\nFonctionnalité à implémenter.');
}

function exportToExcel() {
    window.location.href = '{{ route("admin.books.stock-alerts") }}?export=excel&' + new URLSearchParams(window.location.search);
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
