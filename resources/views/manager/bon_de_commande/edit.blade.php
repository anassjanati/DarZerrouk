@extends('layouts.manager')
@section('title', 'Modifier Bon de Commande')

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">
        Modifier le Bon de Commande {{ $bon_de_commande->ref }}
    </h1>

    {{-- Admin message (already shown on show page, but useful here aussi) --}}
    @if($bon_de_commande->admin_note)
        <div class="mb-4 p-3 bg-yellow-50 border-l-4 border-yellow-500 text-sm text-yellow-900">
            <strong>Message de l'administrateur :</strong>
            {{ $bon_de_commande->admin_note }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ route('manager.bon_de_commande.updateCreator', $bon_de_commande->id) }}">
        @csrf
        @method('PUT')

        {{-- Header info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Fournisseur</label>
                <select name="supplier_id"
                        class="w-full border rounded px-3 py-2 text-sm">
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            @selected(old('supplier_id', $bon_de_commande->supplier_id) == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Date</label>
                <input type="date" name="date"
                       value="{{ old('date', $bon_de_commande->date) }}"
                       class="w-full border rounded px-3 py-2 text-sm">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Commentaires</label>
            <textarea name="comments" rows="2"
                      class="w-full border rounded px-3 py-2 text-sm">{{ old('comments', $bon_de_commande->comments) }}</textarea>
        </div>

        {{-- Lines table (very simple version, you can adapt to your JS UI) --}}
        <div class="mb-4">
            <table class="w-full text-sm border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-1 border">Livre</th>
                        <th class="px-2 py-1 border">Qté</th>
                        <th class="px-2 py-1 border">Prix de vente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bon_de_commande->lines as $line)
                        <tr>
                            <td class="px-2 py-1 border">
                                <select name="lines[{{ $line->id }}][book_id]"
                                        class="w-full border rounded px-2 py-1 text-xs">
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}"
                                            @selected(old("lines.$line->id.book_id", $line->book_id) == $book->id)>
                                            {{ $book->barcode }} - {{ $book->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="number" min="1"
                                       name="lines[{{ $line->id }}][quantity]"
                                       value="{{ old("lines.$line->id.quantity", $line->quantity) }}"
                                       class="w-20 border rounded px-2 py-1 text-xs">
                            </td>
                            <td class="px-2 py-1 border">
                                <input type="number" step="0.01" min="0"
                                       name="lines[{{ $line->id }}][selling_price]"
                                       value="{{ old("lines.$line->id.selling_price", $line->selling_price) }}"
                                       class="w-24 border rounded px-2 py-1 text-xs">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex gap-2 mt-4">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-semibold">
                Enregistrer les modifications et renvoyer à l'admin
            </button>
            <a href="{{ route('manager.bon_de_commande.show', $bon_de_commande->id) }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
