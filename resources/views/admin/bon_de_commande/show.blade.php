@extends('layouts.admin')
@section('title', 'Détail Bon de Commande')

@section('content')
@php
    $user    = auth()->user();
    $isAdmin = $user->isAdmin();
    $status  = $bon_de_commande->status;
    $isCreator = $bon_de_commande->user_id === $user->id;
@endphp

<div class="max-w-5xl mx-auto">

    {{-- Header + actions --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $bon_de_commande->ref }}</h1>
        <div class="flex gap-2">

            {{-- Validate button (admin) --}}
            @if(($status === 'pending' || $status === 'needs_correction') && $isAdmin)
                <a href="{{ route('admin.bon_de_commande.edit', $bon_de_commande->id) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Valider
                </a>
            @endif

            {{-- Admin edit after validation --}}
            @if($status === 'validated' && $isAdmin)
                <a href="{{ route('admin.bon_de_commande.edit', $bon_de_commande->id) }}"
                   class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Modifier (admin)
                </a>
            @endif

            {{-- Creator edit only when returned for modification --}}
            @if($status === 'needs_correction' && $isCreator && ! $isAdmin)
                <a href="{{ route('manager.bon_de_commande.editCreator', $bon_de_commande->id) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Modifier le bon
                </a>
            @endif

            {{-- Print --}}
            <a href="{{ route('admin.bon_de_commande.print', $bon_de_commande->id) }}"
               target="_blank"
               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                Imprimer
            </a>

            {{-- Back --}}
            <a href="{{ route('admin.bon_de_commande.index') }}"
               class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                Retour
            </a>
        </div>
    </div>

    {{-- Statut --}}
    <div class="mb-6">
        @if($status === 'pending')
            <span class="px-4 py-2 bg-orange-100 text-orange-800 rounded-full font-semibold">
                ⏳ En attente de validation
            </span>
        @elseif($status === 'validated')
            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-semibold">
                ✓ Validé - Stock ajouté
            </span>
        @elseif($status === 'needs_correction')
            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full font-semibold">
                ⤺ Retourné pour modification
            </span>
        @endif
    </div>

    {{-- Message admin quand retourné --}}
    @if($status === 'needs_correction' && $bon_de_commande->admin_note)
        <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
            <p class="text-sm text-yellow-900">
                <strong>Message de l'administrateur :</strong>
                {{ $bon_de_commande->admin_note }}
            </p>
        </div>
    @endif

    {{-- Infos Bon --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-600">Fournisseur</p>
                    <p class="font-semibold">{{ $bon_de_commande->supplier->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Date</p>
                    <p class="font-semibold">{{ $bon_de_commande->date }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Créé par</p>
                    <p class="font-semibold">{{ $bon_de_commande->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Commentaires</p>
                    <p class="font-semibold">{{ $bon_de_commande->comments ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b pb-2">
                    <span>Nombre de livres</span>
                    <span class="font-semibold">{{ $bon_de_commande->lines->count() }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span>Quantité totale</span>
                    <span class="font-semibold">
                        {{ $bon_de_commande->lines->sum('quantity') }} unités
                    </span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span>Total Prix de Vente</span>
                    <span class="font-semibold">
                        {{ number_format($bon_de_commande->lines->sum(fn($l) => $l->selling_price * $l->quantity), 2) }} DH
                    </span>
                </div>

                @if($status === 'validated' && $isAdmin)
                    <div class="flex justify-between bg-green-50 p-2 rounded">
                        <span>Total Prix d'Achat</span>
                        <span class="font-semibold text-green-700">
                            {{ number_format($bon_de_commande->lines->sum(fn($l) => $l->cost_price * $l->quantity), 2) }} DH
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tableau Livres --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold">Livre</th>
                    <th class="text-left px-4 py-3 font-semibold">Quantité</th>
                    <th class="text-left px-4 py-3 font-semibold">Prix de Vente</th>
                    @if($status === 'validated' && $isAdmin)
                        <th class="text-left px-4 py-3 font-semibold">Prix d'Achat</th>
                        <th class="text-left px-4 py-3 font-semibold">Sous-total Achat</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($bon_de_commande->lines as $line)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $line->book->title }}</div>
                            <div class="text-xs text-gray-500">{{ $line->book->barcode }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold">{{ $line->quantity }}</td>
                        <td class="px-4 py-3">
                            {{ number_format($line->selling_price, 2) }} DH
                        </td>

                        @if($status === 'validated' && $isAdmin)
                            <td class="px-4 py-3">
                                {{ number_format($line->cost_price, 2) }} DH
                            </td>
                            <td class="px-4 py-3 font-semibold">
                                {{ number_format($line->cost_price * $line->quantity, 2) }} DH
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
