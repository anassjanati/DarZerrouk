@extends('layouts.admin')

@section('title', 'Fournisseurs')

@section('content')
<div class="suppliers-wrapper max-w-6xl mx-auto mt-8 px-2">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Fournisseurs</h1>
            <p class="text-sm text-slate-600">
                Liste des fournisseurs et nombre de bons de commande associés.
            </p>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap gap-2 mb-4">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nom, code, téléphone, ville..."
               class="border rounded px-3 py-2 text-sm" />
        <input type="text" name="city" value="{{ request('city') }}"
               placeholder="Ville..."
               class="border rounded px-3 py-2 text-sm" />
        <select name="status" class="border rounded px-3 py-2 text-sm">
            <option value="">Tous les statuts</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Actifs</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
        </select>
        <button type="submit"
                class="px-4 py-2 bg-sky-600 text-white text-sm font-semibold rounded">
            Rechercher
        </button>
    </form>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left font-semibold text-slate-600">Code</th>
                <th class="px-4 py-2 text-left font-semibold text-slate-600">Nom</th>
                <th class="px-4 py-2 text-left font-semibold text-slate-600">Ville</th>
                <th class="px-4 py-2 text-left font-semibold text-slate-600">Téléphone</th>
                <th class="px-4 py-2 text-left font-semibold text-slate-600">Nb BDC</th>
                <th class="px-4 py-2 text-left font-semibold text-slate-600"></th>
            </tr>
            </thead>
            <tbody>
            @forelse($suppliers as $supplier)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $supplier->code ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $supplier->name }}</td>
                    <td class="px-4 py-2">{{ $supplier->city ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $supplier->phone ?? '—' }}</td>
                    <td class="px-4 py-2">
                        {{ $supplier->purchase_orders_count ?? $supplier->purchaseOrders()->count() }}
                    </td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.suppliers.show', $supplier) }}"
                           class="text-sky-700 text-xs font-semibold">
                            Voir
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-slate-500">
                        Aucun fournisseur trouvé.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
