@extends('layouts.admin')
@section('title', 'Liste des Bons de Commande')
@section('content')

<style>
.table-bc th, .table-bc td { padding:8px 10px; }
.table-bc th { background: #f6f8fa; font-weight: 600; }
.badge { padding: 2px 9px; border-radius: 6px; font-size: 13px;}
.bg-green-500 { background: #10b981; color: #fff;}
.bg-gray-300 { background: #d1d5db; color: #333;}
</style>

<div class="container mx-auto px-6 py-4">
    <h2 class="text-2xl font-bold mb-5">Liste des Bons de Commande</h2>
    <a href="{{ route('admin.bon_de_commande.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-5 inline-block">Nouveau bon de commande</a>
    <table class="w-full border text-sm mb-8 table-bc">
        <thead>
            <tr>
                <th>Ref</th>
                <th>Fournisseur</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Crée par</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($commands as $bc)
            <tr>
                <td>{{ $bc->ref }}</td>
                <td>{{ $bc->supplier->name ?? '' }}</td>
                <td>{{ $bc->date }}</td>
                <td>
                    <span class="badge {{ $bc->status == 'completed' ? 'bg-green-500 text-white' : 'bg-gray-300' }}">{{ $bc->status }}</span>
                </td>
                <td>{{ $bc->user->name ?? '' }}</td>
                <td>
                    <a href="{{ route('admin.bon_de_commande.show', $bc->id) }}" class="text-blue-600 underline">Voir</a>
                    <a href="{{ route('admin.bon_de_commande.print', $bc->id) }}" class="text-teal-700 ml-2 underline" target="_blank">Imprimer</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-3 text-gray-600">Aucun bon de commande trouvé.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div>
        {{ $commands->links() }}
    </div>
</div>
@endsection
