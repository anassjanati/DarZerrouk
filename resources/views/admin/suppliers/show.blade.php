@extends('layouts.admin')

@section('title', 'Fournisseur : '.$supplier->name)

@section('content')
<div class="max-w-5xl mx-auto mt-8 px-2 space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $supplier->name }}</h1>
            @if($supplier->contact_person)
                <p class="text-sm text-slate-600">
                    Contact : {{ $supplier->contact_person }}
                </p>
            @endif
            @if($supplier->city || $supplier->country)
                <p class="text-xs text-slate-500 mt-1">
                    {{ $supplier->city }}{{ $supplier->city && $supplier->country ? ', ' : '' }}{{ $supplier->country }}
                </p>
            @endif
        </div>

        <div class="text-right space-y-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                         {{ $supplier->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                {{ $supplier->is_active ? 'Actif' : 'Inactif' }}
            </span>
            <div class="text-xs text-slate-500">
                Nb BDC : {{ $supplier->purchaseOrders()->count() }}
            </div>
        </div>
    </div>

    {{-- Info + synthèse --}}
    <div class="grid md:grid-cols-2 gap-6">
        {{-- Infos fournisseur --}}
        <div class="bg-white rounded-xl shadow-sm p-4">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">
                Informations fournisseur
            </h2>
            <dl class="text-sm space-y-2">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Code</dt>
                    <dd class="text-slate-900">{{ $supplier->code ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Téléphone</dt>
                    <dd class="text-slate-900">{{ $supplier->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Email</dt>
                    <dd class="text-slate-900">{{ $supplier->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Adresse</dt>
                    <dd class="text-slate-900 text-right max-w-[220px]">
                        {{ $supplier->address ?? '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Conditions de paiement</dt>
                    <dd class="text-slate-900 text-right max-w-[220px]">
                        {{ $supplier->payment_terms ?? '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Synthèse achats --}}
        <div class="bg-white rounded-xl shadow-sm p-4">
            @php
                $totalAmount = $supplier->purchaseOrders->sum->total;
            @endphp
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">
                Synthèse des achats
            </h2>
            <dl class="text-sm space-y-2">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Nombre de BDC</dt>
                    <dd class="text-slate-900">{{ $supplier->purchaseOrders->count() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Montant total BDC</dt>
                    <dd class="text-slate-900">
                        {{ number_format($totalAmount, 2) }} DH
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Plafond de crédit</dt>
                    <dd class="text-slate-900">
                        {{ $supplier->credit_limit ? number_format($supplier->credit_limit, 2).' DH' : '—' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Solde courant</dt>
                    <dd class="text-slate-900">
                        {{ $supplier->current_balance ? number_format($supplier->current_balance, 2).' DH' : '—' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Historique des bons de commande --}}
    <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-sm font-semibold text-slate-800">
                Bons de commande de ce fournisseur
            </h2>
            <p class="text-xs text-slate-500">
                {{ $orders->total() }} BDC au total
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs md:text-sm">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Réf.</th>
                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Date</th>
                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Statut</th>
                    <th class="px-3 py-2 text-right font-semibold text-slate-600">Montant</th>
                    <th class="px-3 py-2 text-center font-semibold text-slate-600">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $order->ref }}</td>
                        <td class="px-3 py-2">{{ $order->date->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">{{ ucfirst($order->status) }}</td>
                        <td class="px-3 py-2 text-right">
                            {{ number_format($order->total, 2) }} DH
                        </td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('admin.bon_de_commande.show', $order) }}"
                               class="text-sky-700 text-xs font-semibold mr-2">
                                Voir
                            </a>
                            <a href="{{ route('admin.bon_de_commande.print', $order) }}"
                               class="text-slate-600 text-xs font-semibold">
                                Imprimer
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-slate-500">
                            Aucun bon de commande pour ce fournisseur.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
