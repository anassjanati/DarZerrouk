@extends('layouts.admin')

@section('title', 'Gestion des livres')

@section('content')

<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestion des livres</h1>
            <p class="text-gray-600">Rechercher, filtrer et gérer votre inventaire</p>
        </div>
        <div class="flex gap-2">
            <!-- Single book add (links to add page, NO duplicate form) -->
            <a href="{{ route('admin.books.create') }}" class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ajouter un livre
            </a>
            <!-- Bulk import modal button -->
            <button onclick="openImportModal()" class="bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-800 font-semibold flex items-center gap-2">
                📥 Importer CSV
            </button>
        </div>
    </div>
</div>

<!-- Unified Search & Filters -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.books.manage') }}" id="searchForm">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche générale</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Titre, Auteur, Éditeur, ISBN/code-barres/scanner..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                    autofocus
                    autocomplete="off"
                />
                <p class="text-xs text-teal-700 mt-1 font-medium">📦 Peut rechercher par titre, auteur, ISBN ou scanner code-barres!</p>
            </div>
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Auteur</label>
                <select name="author_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    <option value="">Tous</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>
                            {{ $author->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Éditeur</label>
                <select name="publisher_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    <option value="">Tous</option>
                    @foreach($publishers as $publisher)
                        <option value="{{ $publisher->id }}" {{ request('publisher_id') == $publisher->id ? 'selected' : '' }}>
                            {{ $publisher->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Zone</label>
                <select name="zone_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    <option value="">Toutes</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                            {{ $zone->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                <select name="stock_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    <option value="">Tous</option>
                    <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>En stock</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stock faible</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Rupture</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2 items-end mt-6">
            <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 font-semibold">
                🔍 Rechercher
            </button>
            <a href="{{ route('admin.books.manage') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Table Result -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
        <p class="text-sm text-gray-600">
            <strong>{{ $books->total() }}</strong> livre(s) trouvé(s)
        </p>
        <div class="flex gap-2">
            <select onchange="window.location.href='?sort='+this.value" class="px-3 py-1 border border-gray-300 rounded text-sm">
                <option value="title">Titre</option>
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date d'ajout</option>
                <option value="stock_quantity" {{ request('sort') == 'stock_quantity' ? 'selected' : '' }}>Stock</option>
                <option value="price_2" {{ request('sort') == 'price_2' ? 'selected' : '' }}>Prix</option>
            </select>
        </div>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Livre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($books as $book)
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="showBookDetails({{ $book->id }})">
                <td class="px-6 py-4">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $book->title }}</div>
                        <div class="text-xs text-gray-500">{{ $book->barcode ?? $book->isbn ?? 'N/A' }}</div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $book->author->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $book->category->name ?? '-' }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $book->zone->code ?? 'N/A' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @if($book->price_2 > 0 && $book->price_2 < $book->price_1)
                        <div>
                            <div class="text-sm text-gray-500 line-through">
                                {{ number_format($book->price_1, 2) }} DH
                            </div>
                            <div class="text-lg font-semibold text-teal-600">
                                {{ number_format($book->price_2, 2) }} DH
                            </div>
                            <div class="text-xs text-green-600">
                                -{{ number_format($book->discount_percentage_calculated, 0) }}%
                            </div>
                        </div>
                    @else
                        <div class="text-sm font-medium text-gray-900">
                            {{ number_format($book->price_1, 2) }} DH
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($book->isOutOfStock())
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Rupture
                        </span>
                    @elseif($book->isLowStock())
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                            {{ $book->stock_quantity }}
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $book->stock_quantity }}
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right text-sm font-medium" onclick="event.stopPropagation()">
                    <a href="{{ route('admin.books.show', $book->id) }}" class="text-teal-600 hover:text-teal-900 mr-3" title="Détails">👁️</a>
                    <a href="{{ route('admin.books.edit', $book->id) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Modifier">✏️</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                    Aucun livre trouvé
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

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Importer des livres (CSV)</h2>
            <button onclick="closeImportModal()" class="text-gray-500 hover:text-gray-700 text-lg">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.books.import.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-bold">Fichier CSV</label>
                <input name="csv_file" type="file" accept=".csv,.txt" required class="w-full border border-gray-300 px-3 py-2 rounded"/>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-700 text-white px-5 py-2 rounded hover:bg-blue-800 font-semibold">
                    Importer
                </button>
                <button type="button" onclick="closeImportModal()" class="bg-gray-200 text-gray-700 px-5 py-2 rounded hover:bg-gray-300 font-semibold">
                    Annuler
                </button>
            </div>
        </form>
        <p class="text-xs text-gray-500 mt-4">Format: titre, auteur, catégorie, prix, stock... (voir modèle export si besoin)</p>
    </div>
</div>

<!-- Book Details Modal (optional, keep if you use it) -->
<div id="bookDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold">Détails du livre</h2>
            <button onclick="closeBookDetails()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="bookDetailsContent" class="p-6">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<script>
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}
function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}
function showBookDetails(bookId) {
    document.getElementById('bookDetailsModal').classList.remove('hidden');
    document.getElementById('bookDetailsContent').innerHTML = '<p class="text-center py-8">Chargement...</p>';
    fetch(`/admin/books/${bookId}/details`)
        .then(res => res.json())
        .then(book => {
            document.getElementById('bookDetailsContent').innerHTML = `
                <dl class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Titre</dt>
                        <dd class="mt-1 text-lg font-semibold">${book.title}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Auteur</dt>
                        <dd class="mt-1">${book.author?.name || '-'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                        <dd class="mt-1">${book.isbn || '-'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Code-barres</dt>
                        <dd class="mt-1" style="font-family: monospace;">${book.barcode || '-'}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Prix Normal</dt>
                        <dd class="mt-1 font-semibold">${book.price_1} DH</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Prix Remisé / Gros</dt>
                        <dd class="mt-1 font-semibold text-teal-600">${book.price_2} DH</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stock</dt>
                        <dd class="mt-1 font-semibold">${book.stock_quantity} unités</dd>
                    </div>
                    ${book.notes ? `
                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1">${book.notes}</dd>
                    </div>
                    ` : ''}
                </dl>
            `;
        });
}
function closeBookDetails() {
    document.getElementById('bookDetailsModal').classList.add('hidden');
}
</script>

@endsection
