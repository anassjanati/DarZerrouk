@extends('layouts.admin')

@section('title', 'Clients')

@section('content')
<style>
.clients-wrapper {
    max-width: 1200px;
    margin: 24px auto 40px;
    padding: 0 10px;
}
.clients-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
}
.clients-header h1 {
    font-size: 22px;
    font-weight: 600;
    color: #1f2933;
}
.clients-subtitle {
    font-size: 13px;
    color: #6b7280;
}
.clients-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 14px;
}
.clients-filters input,
.clients-filters select {
    padding: 7px 10px;
    border-radius: 6px;
    border: 1px solid #c3d1e3;
    font-size: 14px;
    min-width: 190px;
}
.clients-filters button {
    padding: 8px 16px;
    background: #1f4b99;
    color: #fff;
    font-weight: 600;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 14px;
}
.clients-table-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
    padding: 14px 18px 10px;
    overflow-x: auto;
}
.clients-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.clients-table th,
.clients-table td {
    padding: 9px 10px;
    border-bottom: 1px solid #f0f2f5;
    text-align: left;
    white-space: nowrap;
}
.clients-table th {
    background: #f8fafc;
    font-weight: 600;
    font-size: 13px;
    color: #4b5563;
}
.badge-active {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    background: #ecfdf5;
    color: #047857;
}
.badge-inactive {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    background: #fef2f2;
    color: #b91c1c;
}
.action-link {
    font-size: 13px;
    color: #1f4b99;
    text-decoration: none;
    margin-right: 8px;
}
.action-link:hover {
    text-decoration: underline;
}
</style>

<div class="clients-wrapper">
    @if (session('success'))
        <div class="mb-3 p-3 bg-green-100 text-green-800 rounded text-sm max-w-4xl mx-auto">
            {{ session('success') }}
        </div>
    @endif

    <div class="clients-header">
        <div>
            <h1>Clients</h1>
            <p class="clients-subtitle">
                Fichier clients pour le suivi des ventes et des contacts.
            </p>
        </div>
        <a href="{{ route('admin.clients.create') }}"
           class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
            Ajouter un client
        </a>
    </div>

    <form method="GET" class="clients-filters">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, entreprise, téléphone, code..." />
        <input type="text" name="city" value="{{ request('city') }}" placeholder="Ville..." />
        <select name="status">
            <option value="">Tous les statuts</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Actifs</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
        </select>
        <button type="submit">Rechercher</button>
    </form>

    <div class="clients-table-card">
        <table class="clients-table">
            <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Entreprise / Page</th>
                <th>Téléphone</th>
                <th>Ville</th>
                <th>Statut</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>{{ $client->id ?? '—' }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->company_name ?? '—' }}</td>
                    <td>{{ $client->phone ?? '—' }}</td>
                    <td>{{ $client->city ?? '—' }}</td>
                    <td>
                        @if($client->is_active)
                            <span class="badge-active">Actif</span>
                        @else
                            <span class="badge-inactive">Inactif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.clients.show', $client) }}" class="action-link">Voir</a>
                        <a href="{{ route('admin.clients.edit', $client) }}" class="action-link">Éditer</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucun client trouvé.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:14px;">
            {{ $clients->links() }}
        </div>
    </div>
</div>
@endsection
