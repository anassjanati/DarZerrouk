@extends('layouts.admin')

@section('title', 'Journal d\'activité')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Journal d'activité</h1>
    <p class="text-gray-600">Historique de toutes les actions des utilisateurs</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- User Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
            <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                <option value="">Tous les utilisateurs</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Action Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
            <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                <option value="">Toutes les actions</option>
                @foreach($actions as $key => $label)
                    <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date From -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date de</label>
            <input 
                type="date" 
                name="date_from" 
                value="{{ request('date_from') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
            />
        </div>

        <!-- Date To -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date à</label>
            <input 
                type="date" 
                name="date_to" 
                value="{{ request('date_to') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
            />
        </div>

        <!-- Buttons -->
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 font-semibold">
                Filtrer
            </button>
            <a href="{{ route('admin.activity-logs.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Activity Logs Table -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Heure</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Détails</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-teal-100 flex items-center justify-center mr-2">
                            <span class="text-teal-600 text-xs font-semibold">{{ substr($log->user->name, 0, 1) }}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $log->action === 'login' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $log->action === 'logout' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $log->action === 'create' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $log->action === 'update' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $log->action === 'delete' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $log->action === 'update_permissions' ? 'bg-purple-100 text-purple-800' : '' }}">
                        {{ $actions[$log->action] ?? $log->action }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ $log->description }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $log->ip_address }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.activity-logs.show', $log) }}" class="text-teal-600 hover:text-teal-900">
                        Voir détails
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Aucune activité trouvée
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $logs->withQueryString()->links() }}
</div>
@endsection
