@extends('layouts.manager')

@section('title', 'Bons de commande')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    {{-- En‑tête --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Bons de commande</h1>
            <p class="text-sm text-slate-500">
                Suivre et créer les demandes d’approvisionnement de votre point de vente.
            </p>
        </div>
{{--
        <div class="flex items-center gap-3">
            <div class="hidden md:flex flex-col text-right text-xs text-slate-500">
                <span>Aujourd’hui : {{ now()->format('d/m/Y') }}</span>
                <span>Manager : {{ auth()->user()->name }}</span>
            </div> --}}

            <a href="{{ route('manager.bon_de_commande.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow-sm hover:bg-emerald-700">
                <span>+ Nouveau bon</span>
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET"
          action="{{ route('manager.bon_de_commande.index') }}"
          class="bg-white rounded-xl shadow-sm border border-slate-100 px-4 py-3 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Statut</label>
                <select name="status"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
                    <option value="">Tous</option>
                    <option value="pending"   @selected(request('status') === 'pending')>En attente</option>
                    <option value="validated" @selected(request('status') === 'validated')>Validés</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Fournisseur</label>
                <input type="text"
                       name="supplier"
                       value="{{ request('supplier') }}"
                       placeholder="Nom fournisseur"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Référence</label>
                <input type="text"
                       name="ref"
                       value="{{ request('ref') }}"
                       placeholder="Ex : BC-2025-001"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                    Filtrer
                </button>
                <a href="{{ route('manager.bon_de_commande.index') }}"
                   class="px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Réinitialiser
                </a>
            </div>
        </div>
    </form>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>{{ $commands->total() }} bon(s) trouvé(s)</span>
        </div>

        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Référence</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Fournisseur</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Date</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Créé par</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Statut</th>
                <th class="px-4 py-2 text-right text-xs font-semibold text-slate-600">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($commands as $bc)
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-2 font-mono text-slate-900">{{ $bc->ref }}</td>
                    <td class="px-4 py-2 text-slate-700">{{ $bc->supplier->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $bc->date }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $bc->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">
                        @if($bc->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-semibold">
                                En attente
                            </span>
                        @elseif($bc->status === 'validated')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-semibold">
                                Validé
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right space-x-3">
                        <a href="{{ route('manager.bon_de_commande.show', $bc) }}"
                           class="text-sky-600 hover:text-sky-800 text-xs font-semibold">
                            Détails
                        </a>

                        @if($bc->status === 'pending')
                            <a href="{{ route('manager.bon_de_commande.editCreator', $bc) }}"
                               class="text-amber-600 hover:text-amber-800 text-xs font-semibold">
                                Modifier
                            </a>
                        @endif

                        <a href="{{ route('manager.bon_de_commande.print', $bc) }}"
                           target="_blank"
                           class="text-violet-600 hover:text-violet-800 text-xs font-semibold">
                            Imprimer
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                        Aucun bon de commande pour le moment.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        @if($commands->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
                {{ $commands->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
