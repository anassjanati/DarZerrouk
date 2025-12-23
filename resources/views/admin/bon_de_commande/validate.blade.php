@extends('layouts.admin')
@section('title', 'Valider Bon de Commande')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Valider Bon de Commande</h1>
    <p class="text-gray-600 mb-6">
        Saisissez le prix d'achat pour chaque livre ou appliquez un pourcentage global de remise.
        Vous pouvez aussi retourner le bon au créateur avec une note de correction.
    </p>

    {{-- Messages --}}
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-800">
            {{ session('error') }}
        </div>
    @endif
    @if (session('info'))
        <div class="mb-4 p-3 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-900">
            {{ session('info') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-800 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Infos Bon --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-600">Référence</p>
                <p class="font-semibold">{{ $bon_de_commande->ref }}</p>
            </div>
            <div>
                <p class="text-gray-600">Fournisseur</p>
                <p class="font-semibold">{{ $bon_de_commande->supplier->name }}</p>
            </div>
            <div>
                <p class="text-gray-600">Créé par</p>
                <p class="font-semibold">{{ $bon_de_commande->user->name }}</p>
            </div>
            <div>
                <p class="text-gray-600">Date</p>
                <p class="font-semibold">{{ $bon_de_commande->date }}</p>
            </div>
        </div>
    </div>

    {{-- FORMULAIRE DE VALIDATION (remise + prix d'achat) --}}
    <form id="validate-form"
          method="POST"
          action="{{ route('admin.bon_de_commande.update', $bon_de_commande->id) }}"
          class="bg-white rounded-lg shadow p-6 mb-6">
        @csrf
        @method('PUT')

        {{-- Remise Globale --}}
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Remise Globale (%) - Optionnel
            </label>
            <input type="number"
                   name="discount_percentage"
                   step="0.01"
                   min="0" max="100"
                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                   value="{{ old('discount_percentage') }}"
                   placeholder="Ex: 25 pour -25% sur tous les prix de vente">
            <p class="text-xs text-gray-600 mt-2">
                Si rempli, le prix d'achat = prix de vente × (1 - remise%) pour toutes les lignes.
            </p>
        </div>

        {{-- Tableau des Livres --}}
        <div class="mb-6 overflow-x-auto">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Détails des Livres</h2>
            <table class="w-full border text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-2">Livre</th>
                        <th class="text-left px-4 py-2">Quantité</th>
                        <th class="text-left px-4 py-2">Prix de Vente</th>
                        <th class="text-left px-4 py-2">Prix d'Achat *</th>
                        <th class="text-left px-4 py-2">Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bon_de_commande->lines as $line)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $line->book->title }}</div>
                                <div class="text-xs text-gray-500">Code: {{ $line->book->barcode }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-center">
                                {{ $line->quantity }}
                            </td>
                            <td class="px-4 py-3 font-semibold">
                                {{ number_format($line->selling_price, 2) }} DH
                            </td>
                            <td class="px-4 py-3">
                                <input type="number"
                                       name="lines[{{ $line->id }}][cost_price]"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('lines.' . $line->id . '.cost_price', 0) }}"
                                       class="w-full px-2 py-1 border rounded focus:ring-2 focus:ring-green-500"
                                       placeholder="0.00">
                            </td>
                            <td class="px-4 py-3 font-semibold text-right">
                                <span class="subtotal"
                                      data-selling="{{ $line->selling_price }}"
                                      data-qty="{{ $line->quantity }}">
                                    0.00 DH
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Résumé --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-2">Résumé Commercial</h3>
                <div class="text-sm space-y-1">
                    <div class="flex justify-between">
                        <span>Total Prix de Vente :</span>
                        <span class="font-semibold">
                            {{ number_format($bon_de_commande->lines->sum(fn($l) => $l->selling_price * $l->quantity), 2) }} DH
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-2">Résumé Achat</h3>
                <div class="text-sm space-y-1">
                    <div class="flex justify-between">
                        <span>Total Prix d'Achat :</span>
                        <span class="font-semibold" id="total-cost">0.00 DH</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bouton de validation --}}
        <div class="flex gap-3">
            <button type="submit"
                    name="action"
                    value="validate"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                Valider et Ajouter Stock
            </button>
            <a href="{{ route('admin.bon_de_commande.show', $bon_de_commande->id) }}"
               class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Annuler
            </a>
        </div>
    </form>

    {{-- FORMULAIRE : retourner au créateur --}}
    <form method="POST"
          action="{{ route('admin.bon_de_commande.update', $bon_de_commande->id) }}"
          class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <input type="hidden" name="action" value="send_back">

        <h2 class="text-lg font-semibold text-gray-900 mb-3">Retourner au créateur pour correction</h2>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Message pour le créateur *
            </label>
            <textarea name="admin_note"
                      rows="3"
                      class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500"
                      required>{{ old('admin_note', $bon_de_commande->admin_note) }}</textarea>
        </div>

        <button type="submit"
                class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 font-semibold">
            Retourner pour modification
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountInput = document.querySelector('input[name="discount_percentage"]');
    const costInputs = document.querySelectorAll('input[name*="[cost_price]"]');
    const validateForm = document.getElementById('validate-form');
    const validateButton = validateForm.querySelector('button[name="action"][value="validate"]');

    function updateSubtotals() {
        let totalCost = 0;
        const discount = parseFloat(discountInput.value || 0);

        costInputs.forEach((input) => {
            const row = input.closest('tr');
            const subtotalSpan = row.querySelector('.subtotal');
            const selling = parseFloat(subtotalSpan.dataset.selling);
            const qty = parseInt(subtotalSpan.dataset.qty);

            let costPrice = parseFloat(input.value || 0);

            // Si remise globale est saisie ET aucun prix d'achat manuel n'est mis,
            // calculer le prix d'achat proposé mais laisser l'admin libre de le changer.
            if (discount > 0 && !input.dataset.manual) {
                costPrice = selling * (1 - discount / 100);
                input.value = costPrice.toFixed(2);
            }

            const subtotal = costPrice * qty;
            subtotalSpan.textContent = subtotal.toFixed(2) + ' DH';
            totalCost += subtotal;
        });

        document.getElementById('total-cost').textContent = totalCost.toFixed(2) + ' DH';
    }

    // Quand l'admin modifie un prix d'achat à la main, marquer le champ comme "manuel"
    costInputs.forEach(input => {
        input.addEventListener('input', function () {
            input.dataset.manual = '1';
            updateSubtotals();
        });
    });

    discountInput.addEventListener('input', function () {
        // Quand on change la remise, on enlève le flag "manuel" seulement si le champ est vide
        costInputs.forEach(input => {
            if (!input.value) {
                delete input.dataset.manual;
            }
        });
        updateSubtotals();
    });

    // Calcul initial
    updateSubtotals();

    // Popup de confirmation avant validation finale
    validateButton.addEventListener('click', function (e) {
        e.preventDefault();
        updateSubtotals(); // s'assurer que les montants sont à jour

        const totalCostText = document.getElementById('total-cost').textContent;
        const discount = discountInput.value || '0';

        const confirmMessage =
            'Vous êtes sur le point de VALIDER ce bon de commande.\n\n' +
            '- Remise globale appliquée : ' + discount + ' %\n' +
            '- Total Prix d\'Achat : ' + totalCostText + '\n\n' +
            'Confirmez-vous la validation et l\'ajout du stock en librairie ?';

        if (confirm(confirmMessage)) {
            validateForm.submit();
        }
    });
});
</script>
@endsection
