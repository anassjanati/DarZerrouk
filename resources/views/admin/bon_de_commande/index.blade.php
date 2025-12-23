@extends(
    auth()->user()->isCashier()
        ? 'layouts.cashier'
        : (auth()->user()->isManager()
            ? 'layouts.manager'
            : 'layouts.admin')
)

@section('title', 'Bons de Commande')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Bons de Commande</h1>
        @if(auth()->user()->isManager() || auth()->user()->isSuperviseur())
            <a href="{{ route('admin.bon_de_commande.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Nouveau Bon
            </a>
        @endif
    </div>

    <!-- Filtres -->
    <form method="GET" action="{{ route('admin.bon_de_commande.index') }}" class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex gap-4">
            <select name="status" class="px-3 py-2 border rounded-lg">
                <option value="">— Tous les statuts —</option>
                <option value="pending" @selected(request('status') === 'pending')>En attente</option>
                <option value="validated" @selected(request('status') === 'validated')>Validés</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">Filtrer</button>
        </div>
    </form>

    <!-- Tableau -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Référence</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Fournisseur</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Date</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Créé par</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Statut</th>
                    <th class="text-left px-6 py-3 font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($commands as $bc)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-900 font-mono">{{ $bc->ref }}</td>
                        <td class="px-6 py-3">{{ $bc->supplier->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3">{{ $bc->date }}</td>
                        <td class="px-6 py-3">{{ $bc->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3">
                            @if($bc->status === 'pending')
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-semibold">En attente</span>
                            @elseif($bc->status === 'validated')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Validé</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 flex gap-2">
                            <a href="{{ route('admin.bon_de_commande.show', $bc->id) }}" class="text-blue-600 hover:text-blue-900 underline">Voir</a>
                            
                            @if($bc->status === 'pending' && auth()->user()->isAdmin())
                                <a href="{{ route('admin.bon_de_commande.edit', $bc->id) }}" class="text-green-600 hover:text-green-900 underline">Valider</a>
                            @endif

                            <a href="{{ route('admin.bon_de_commande.print', $bc->id) }}" target="_blank" class="text-purple-600 hover:text-purple-900 underline">Imprimer</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucun bon de commande trouvé</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($commands->hasPages())
            <div class="px-6 py-3 bg-gray-50 border-t">
                {{ $commands->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
