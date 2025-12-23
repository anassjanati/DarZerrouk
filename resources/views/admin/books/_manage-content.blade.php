@php
    $isManager       = request()->routeIs('manager.*');

    // routes that exist for both
    $booksDetails    = $isManager ? 'manager.books.details'   : 'admin.books.details';
    $booksResetRoute = $isManager ? 'manager.books.manage'    : 'admin.books.manage';
    $stocksTransfer  = $isManager ? 'manager.stocks.transfer' : 'admin.stocks.transfer';

    // admin-only routes: always admin
    $booksCreate     = 'admin.books.create';
    $booksImportCsv  = 'admin.books.import.csv';
    $booksEditRoute  = 'admin.books.edit';
    $booksArchive    = 'admin.books.archive';
    $booksUnarchive  = 'admin.books.unarchive';

    $user           = auth()->user();
    $canEditBooks   = $user && $user->canModule('books', 'edit');
    $canCreateBooks = $user && $user->canModule('books', 'create');
@endphp

{{-- Messages globaux --}}
@if (session('success'))
    <div class="mb-3 p-3 bg-green-100 text-green-800 rounded text-sm max-w-4xl mx-auto">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-3 p-3 bg-red-100 text-red-800 rounded text-sm max-w-4xl mx-auto">
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

<style>
/* Layout */
.books-page-wrapper {
    max-width: 1400px;
    margin: 24px auto 40px;
    padding: 0 10px;
}
.books-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 18px;
}
.books-header h1 {
    font-size: 22px;
    font-weight: 600;
    color: #1f2933;
}
.books-header-subtitle {
    margin-top: 4px;
    font-size: 13px;
    color: #6b7280;
}

/* Search bar */
.books-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 14px;
}
.books-filters input,
.books-filters select {
    padding: 7px 10px;
    border-radius: 6px;
    border: 1px solid #c3d1e3;
    font-size: 14px;
    min-width: 190px;
}
.books-filters button {
    padding: 8px 16px;
    background: #1f4b99;
    color: #fff;
    font-weight: 600;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 14px;
}
.books-filters button:hover {
    background: #16366f;
}

/* Table container */
.books-table-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
    padding: 14px 18px 10px;
    overflow-x: auto;
}

/* Main table */
.books-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.books-table th,
.books-table td {
    padding: 9px 10px;
    border-bottom: 1px solid #f0f2f5;
    text-align: left;
    white-space: nowrap;
}
.books-table th {
    background: #f8fafc;
    font-weight: 600;
    font-size: 13px;
    color: #4b5563;
}
.books-table tbody tr:hover {
    background: #f9fbff;
}
.books-table tr.archived {
    background-color: #f5f5f5;
    color: #9ca3af;
}
.books-table tr.archived td,
.books-table tr.archived a,
.books-table tr.archived .aucun-stock {
    color: #9ca3af !important;
    pointer-events: none;
}

/* Text helpers */
.text-right   { text-align: right; }
.text-center  { text-align: center; }
.text-muted   { color: #6b7280; }

/* Title link */
.book-title-link {
    color: #1f4b99;
    font-weight: 500;
    text-decoration: none;
}
.book-title-link:hover {
    text-decoration: underline;
}

/* Stock badge */
.stock-badge {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
    padding: 4px 8px;
    border-radius: 999px;
    background: #ecfdf5;
    color: #065f46;
    font-size: 12px;
    font-weight: 500;
}
.stock-badge-line {
    font-size: 11px;
    color: #047857;
}

/* Placements badge + button */
.placement-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 8px;
    border-radius: 999px;
    background: #eef2ff;
    color: #3730a3;
    font-size: 12px;
    font-weight: 500;
}
.placement-count {
    font-weight: 600;
}
.placement-secondary {
    font-size: 11px;
    color: #6b7280;
}
.placement-more {
    margin-left: 4px;
    font-size: 11px;
    color: #6b7280;
}
.placement-view-btn {
    border: none;
    background: transparent;
    color: #2563eb;
    font-size: 11px;
    cursor: pointer;
    text-decoration: underline;
    padding: 0 0 0 4px;
}

/* Aucun stock */
.aucun-stock {
    color: #dc2626;
    font-size: 13px;
}

/* Action buttons */
.action-btn {
    padding: 4px 9px;
    border-radius: 6px;
    font-size: 13px;
    margin-right: 6px;
    cursor: pointer;
    border: none;
    font-weight: 500;
}
.edit-btn       { background:#1f4b99; color:#fff; text-decoration:none; }
.transfer-btn   { background:#10b981; color:#fff; text-decoration:none; }
.archiver-btn   { background:#facc15; color:#4b4200; }
.unarchive-btn  { background:#16a34a; color:#fff; }
.delete-btn     { background:#ef4444; color:#fff; }

/* Modal (CSV + placements) */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 50;
}
.modal-backdrop.is-open {
    display: flex;
}
.modal-panel {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.25);
    width: 100%;
    max-width: 520px;
    max-height: 80vh;
    padding: 18px 20px 16px;
    overflow: hidden;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}
.modal-close {
    border: none;
    background: transparent;
    font-size: 20px;
    cursor: pointer;
    color: #6b7280;
}
.modal-body {
    font-size: 14px;
    max-height: 60vh;
    overflow-y: auto;
}
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 14px;
}

/* Placements table in modal */
.placements-modal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.placements-modal-table th,
.placements-modal-table td {
    padding: 6px 8px;
    border-bottom: 1px solid #e5e7eb;
}
.placements-modal-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #4b5563;
}

@media (max-width: 1024px) {
    .books-page-wrapper { padding: 0 6px; }
    .books-table-card   { padding: 10px; }
    .books-table        { font-size: 13px; }
    .books-table th,
    .books-table td     { padding: 7px 8px; }
}
</style>

<div class="books-page-wrapper">

    {{-- Header + Import --}}
    <div class="books-header">
        <div>
            <h1>Gestion des livres</h1>
            <p class="books-header-subtitle">
                Catalogue complet avec aperçu des stocks Librairie / Magasinage et accès rapide aux transferts.
            </p>
        </div>
        @if($canCreateBooks)
    <div class="flex gap-2">
        <a href="{{ route($booksCreate) }}"
           class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
            Ajouter un livre
        </a>
        <button
            type="button"
            onclick="openImportModal()"
            class="px-3 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm">
            Importer livres (CSV)
        </button>
    </div>
@endif

    </div>

    {{-- Filtres --}}
    <form method="GET" class="books-filters">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Recherche titre, auteur..." />
        <input type="text" name="barcode" value="{{ request('barcode') }}" placeholder="Code-barres (recherche)" />
        <input type="text" id="barcode-live-filter" placeholder="Scan live code-barres (filtre instantané)..." />
        <select name="status">
            <option value="">Tous les états</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Actifs</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archivés</option>
        </select>
        <button type="submit">Rechercher</button>
        @if(request()->hasAny(['search','barcode','status']) && request()->anyFilled(['search','barcode','status']))
            <a href="{{ route($booksResetRoute) }}"
               class="px-3 py-2 bg-gray-100 text-gray-700 rounded border border-gray-300 text-sm"
               style="text-decoration:none;">
                Réinitialiser
            </a>
        @endif
    </form>

    {{-- Carte Table --}}
    <div class="books-table-card">
        <table class="books-table">
            <thead>
            <tr>
                <th>Code barres</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Catégorie</th>
                <th class="text-right">Prix</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Placements</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($books as $book)
                @php
                    $totalQty    = $book->stocks->sum('quantity');
                    $stocksCount = $book->stocks->count();
                    $firstStock  = $book->stocks->first();
                    $qtyLib      = $book->stocks
                        ->filter(fn($s) => $s->zone && $s->zone->type === 'librairie')
                        ->sum('quantity');
                    $qtyMag      = $book->stocks
                        ->filter(fn($s) => $s->zone && $s->zone->type === 'magasinage')
                        ->sum('quantity');
                @endphp

                <tr @if(!$book->is_active) class="archived" @endif>
                    <td>{{ $book->barcode }}</td>

                    <td>
                        <a href="{{ route($booksDetails, $book->id) }}" class="book-title-link">
                            {{ $book->title_ar ?? $book->title }}
                        </a>
                    </td>

                    <td>{{ $book->author->name ?? '-' }}</td>

                    <td>{{ $book->category->name ?? '-' }}</td>

                    <td class="text-right">{{ number_format($book->retail_price, 2) }} DH</td>

                    {{-- Stock badge --}}
                    <td class="text-center">
                        @if($totalQty > 0)
                            <span class="stock-badge">
                                <span>Total: {{ $totalQty }} ex.</span>
                                <span class="stock-badge-line">
                                    Lib: {{ $qtyLib }} • Mag: {{ $qtyMag }}
                                </span>
                            </span>
                        @else
                            <span class="aucun-stock">Aucun stock</span>
                        @endif
                    </td>

                    {{-- Placements --}}
                    <td class="text-center">
                        @if($stocksCount > 0)
                            <span class="placement-badge">
                                <span class="placement-count">{{ $totalQty }} ex.</span>
                                @if($firstStock)
                                    <span class="placement-secondary">
                                        Z: {{ $firstStock->zone->name ?? '-' }},
                                        SZ: {{ $firstStock->sousZone->name ?? '-' }}
                                    </span>
                                @endif
                            </span>

                            @if($stocksCount > 1)
                                <span class="placement-more">+{{ $stocksCount - 1 }} autres</span>
                            @endif

                            <button
                                type="button"
                                class="placement-view-btn"
                                onclick="openPlacementsModal({{ $book->id }})">
                                Voir détails
                            </button>

                            {{-- Données placements en JSON pour le JS --}}
                            <script>
                                window.bookPlacements = window.bookPlacements || {};
                                window.bookPlacements[{{ $book->id }}] = {!! $book->stocks->map(function ($s) {
                                    return [
                                        'zone'      => $s->zone->name         ?? '-',
                                        'sous_zone' => $s->sousZone->name     ?? '-',
                                        'sous_sous' => $s->sousSousZone->name ?? '-',
                                        'quantity'  => $s->quantity,
                                    ];
                                })->values()->toJson() !!};
                            </script>
                        @else
                            <span class="aucun-stock">Aucun stock</span>
                        @endif
                    </td>

                    {{-- Actions --}}
<td class="text-right">
    @if($book->is_active)
        @if($canEditBooks)
            <a href="{{ route($booksEditRoute, $book->id) }}"
               class="edit-btn action-btn">
                Éditer
            </a>

            <form action="{{ route($booksArchive, $book->id) }}"
                  method="POST"
                  style="display:inline;">
                @csrf
                <button type="submit" class="archiver-btn action-btn">
                    Archiver
                </button>
            </form>
        @endif

        <a href="{{ route($stocksTransfer, ['barcode' => $book->barcode]) }}"
           class="transfer-btn action-btn">
            Transférer
        </a>
    @else
        @if($canEditBooks)
            <form action="{{ route($booksUnarchive, $book->id) }}"
                  method="POST"
                  style="display:inline;">
                @csrf
                <button type="submit" class="unarchive-btn action-btn">
                    Désarchiver
                </button>
            </form>
        @endif
    @endif
</td>

                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top:14px;">
            {{ $books->links() }}
        </div>
    </div>
</div>

{{-- MODAL IMPORT CSV --}}
<div id="importModal" class="hidden modal-backdrop">
    <div class="modal-panel">
        <div class="modal-header">
            <h2 class="modal-title">Importer des livres via CSV</h2>
            <button type="button" class="modal-close" onclick="closeImportModal()">&times;</button>
        </div>

        <div class="modal-body">
            @if ($errors->any())
                <div class="mb-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded p-2">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST"
                  action="{{ route($booksImportCsv) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Fichier CSV</label>
                    <input type="file" name="file"
                           accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                           class="w-full border rounded px-3 py-2 text-sm" required>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            onclick="closeImportModal()"
                            class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PLACEMENTS --}}
<div id="placementsModal" class="hidden modal-backdrop">
    <div class="modal-panel">
        <div class="modal-header">
            <h2 class="modal-title">Placements du livre</h2>
            <button type="button" class="modal-close" onclick="closePlacementsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <table class="placements-modal-table">
                <thead>
                <tr>
                    <th>Zone</th>
                    <th>Sous‑zone</th>
                    <th>Sous‑sous‑zone</th>
                    <th>Quantité</th>
                </tr>
                </thead>
                <tbody id="placementsModalBody">
                {{-- rempli en JS --}}
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button"
                    onclick="closePlacementsModal()"
                    class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">
                Fermer
            </button>
        </div>
    </div>
</div>

<script>
// Filtre live par code-barres
document.addEventListener('DOMContentLoaded', function() {
    var filterInput = document.getElementById('barcode-live-filter');
    if (!filterInput) return;

    filterInput.addEventListener('keyup', function() {
        var filter = this.value.trim().toLowerCase();
        var rows = document.querySelectorAll('.books-table tbody tr');
        rows.forEach(function(row) {
            var barcodeCell = row.querySelector('td');
            if (!barcodeCell) return;
            var barcode = barcodeCell.textContent.trim().toLowerCase();
            row.style.display = (barcode.indexOf(filter) !== -1) ? '' : 'none';
        });
    });
});

// Import modal
function openImportModal() {
    document.getElementById('importModal').classList.add('is-open');
}
function closeImportModal() {
    document.getElementById('importModal').classList.remove('is-open');
}

// Placements modal
function openPlacementsModal(bookId) {
    var data = window.bookPlacements && window.bookPlacements[bookId];
    if (!data) return;

    var tbody = document.getElementById('placementsModalBody');
    tbody.innerHTML = '';
    data.forEach(function (item) {
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td>' + item.zone + '</td>' +
            '<td>' + item.sous_zone + '</td>' +
            '<td>' + item.sous_sous + '</td>' +
            '<td>' + item.quantity + '</td>';
        tbody.appendChild(tr);
    });

    document.getElementById('placementsModal').classList.add('is-open');
}
function closePlacementsModal() {
    document.getElementById('placementsModal').classList.remove('is-open');
}
</script>
