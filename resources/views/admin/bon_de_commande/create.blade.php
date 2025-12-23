@extends('layouts.admin')
@section('title', 'Créer un Bon de Commande')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Créer un Bon de Commande</h1>

    <!-- Afficher les erreurs de validation -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <h3 class="text-red-800 font-semibold mb-2">❌ Erreurs de validation :</h3>
            <ul class="text-red-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Afficher les messages de succès -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <p class="text-green-800 font-semibold">✓ {{ session('success') }}</p>
        </div>
    @endif

    <!-- Afficher les messages d'erreur -->
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <p class="text-red-800 font-semibold">✗ {{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.bon_de_commande.store') }}" class="bg-white rounded-lg shadow p-6">
        @csrf

        <!-- Fournisseur -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Fournisseur *</label>
            <select name="supplier_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('supplier_id') border-red-500 @enderror">
                <option value="">— Choisir un fournisseur —</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            @error('supplier_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Date -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Date *</label>
            <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('date') border-red-500 @enderror">
            @error('date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Commentaires -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Commentaires</label>
            <textarea name="comments" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('comments') }}</textarea>
        </div>

        <!-- Livres -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Livres Commandés *</h2>
            @error('lines')
                <p class="text-red-600 text-sm mb-3">{{ $message }}</p>
            @enderror
            <div class="overflow-x-auto">
                <table class="w-full border" id="lines-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-3 py-2 font-semibold text-sm">Code-barres</th>
                            <th class="text-left px-3 py-2 font-semibold text-sm">Titre du Livre</th>
                            <th class="text-left px-3 py-2 font-semibold text-sm">Quantité</th>
                            <th class="text-left px-3 py-2 font-semibold text-sm">Prix de Vente</th>
                            <th class="text-left px-3 py-2 font-semibold text-sm">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (old('lines'))
                            @foreach (old('lines') as $index => $line)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <input type="text" class="barcode-input w-full px-2 py-1 border rounded @error('lines.' . $index . '.book_id') border-red-500 @enderror" placeholder="Scanner/Saisir code-barres" autocomplete="off" value="">
                                    <input type="hidden" name="lines[{{ $index }}][book_id]" class="book-id-field" value="{{ old('lines.' . $index . '.book_id') }}">
                                </td>
                                <td class="px-3 py-2">
                                    <span class="book-title text-gray-700">
                                        @if (old('lines.' . $index . '.book_id'))
                                            {{ \App\Models\Book::find(old('lines.' . $index . '.book_id'))?->title ?? '- Livre non trouvé -' }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="lines[{{ $index }}][quantity]" min="1" class="w-full px-2 py-1 border rounded @error('lines.' . $index . '.quantity') border-red-500 @enderror" value="{{ $line['quantity'] ?? '' }}" required>
                                    @error('lines.' . $index . '.quantity') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" name="lines[{{ $index }}][selling_price]" min="0" class="w-full px-2 py-1 border rounded @error('lines.' . $index . '.selling_price') border-red-500 @enderror" value="{{ $line['selling_price'] ?? '' }}" readonly>
                                    @error('lines.' . $index . '.selling_price') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-900 font-bold">×</button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <input type="text" class="barcode-input w-full px-2 py-1 border rounded" placeholder="Scanner/Saisir code-barres" autocomplete="off">
                                    <input type="hidden" name="lines[0][book_id]" class="book-id-field">
                                </td>
                                <td class="px-3 py-2">
                                    <span class="book-title text-gray-700"></span>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="lines[0][quantity]" min="1" class="w-full px-2 py-1 border rounded" required>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" name="lines[0][selling_price]" min="0" class="w-full px-2 py-1 border rounded" readonly>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-900 font-bold">×</button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <button type="button" onclick="addRow()" class="mt-3 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">+ Ajouter une ligne</button>
        </div>

        <!-- Boutons -->
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">Créer le Bon</button>
            <a href="{{ route('admin.bon_de_commande.index') }}" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Annuler</a>
        </div>
    </form>
</div>

<script>
window.bookLookup = @json($booksForJs);

function setupBarcodeRow(row) {
    const input = row.querySelector('.barcode-input');
    const titleSpan = row.querySelector('.book-title');
    const idField = row.querySelector('.book-id-field');
    const priceInput = row.querySelector("[name$='[selling_price]']");

    input.addEventListener('change', function() {
        const barcode = input.value.trim();
        
        if (!barcode) {
            titleSpan.textContent = '';
            idField.value = '';
            priceInput.value = '';
            input.style.borderColor = '';
            return;
        }

        // Search for book by barcode
        const found = window.bookLookup.find(b => b.barcode === barcode);

        if (found) {
            titleSpan.textContent = found.title;
            idField.value = found.id;
            priceInput.value = found.selling_price || '';
            input.style.borderColor = '';
        } else {
            titleSpan.textContent = '- Livre non trouvé -';
            idField.value = '';
            priceInput.value = '';
            input.style.borderColor = 'red';
        }
    });

    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            input.dispatchEvent(new Event('change'));
            e.preventDefault();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#lines-table tbody tr').forEach(setupBarcodeRow);
});

let rowIdx = 1;

function addRow() {
    const table = document.getElementById('lines-table').querySelector('tbody');
    const newRow = table.rows[0].cloneNode(true);

    Array.from(newRow.querySelectorAll('input')).forEach((input) => {
        if (input.classList.contains('barcode-input')) input.value = '';
        if (input.classList.contains('book-id-field')) input.value = '';
        if (input.type === 'number') input.value = '';

        const name = input.name.replace(/\d+/, rowIdx);
        input.name = name;
    });

    newRow.querySelector('.book-title').textContent = '';
    table.appendChild(newRow);
    setupBarcodeRow(newRow);
    rowIdx++;
}

function removeRow(btn) {
    const row = btn.closest('tr');
    const table = row.parentNode;
    if (table.rows.length > 1) {
        table.removeChild(row);
    } else {
        alert('Vous devez avoir au moins une ligne');
    }
}
</script>

@endsection
