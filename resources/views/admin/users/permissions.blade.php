@extends('layouts.admin')

@section('title', 'Gérer les permissions')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gérer les permissions</h1>
            <p class="text-gray-600">{{ $user->name }} - {{ $user->role->display_name }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-4xl">
    <div class="mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-blue-800 font-medium">Information</p>
                    <p class="text-sm text-blue-700 mt-1">Les permissions sont appliquées au <strong>rôle</strong> de l'utilisateur ({{ $user->role->display_name }}), donc tous les utilisateurs avec ce rôle auront les mêmes permissions.</p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.users.permissions.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Module
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Voir
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Créer
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Supprimer
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $moduleLabels = [
                            'books' => '📚 Livres',
                            'sales' => '💰 Ventes',
                            'customers' => '👥 Clients',
                            'suppliers' => '📦 Fournisseurs',
                            'reports' => '📊 Rapports',
                            'settings' => '⚙️ Paramètres',
                        ];
                    @endphp

                    @foreach($modules as $module)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $moduleLabels[$module] ?? ucfirst($module) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input 
                                type="checkbox" 
                                name="permissions[{{ $module }}][view]" 
                                value="1"
                                {{ $permissions->where('module', $module)->first()?->can_view ? 'checked' : '' }}
                                class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input 
                                type="checkbox" 
                                name="permissions[{{ $module }}][create]" 
                                value="1"
                                {{ $permissions->where('module', $module)->first()?->can_create ? 'checked' : '' }}
                                class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input 
                                type="checkbox" 
                                name="permissions[{{ $module }}][edit]" 
                                value="1"
                                {{ $permissions->where('module', $module)->first()?->can_edit ? 'checked' : '' }}
                                class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <input 
                                type="checkbox" 
                                name="permissions[{{ $module }}][delete]" 
                                value="1"
                                {{ $permissions->where('module', $module)->first()?->can_delete ? 'checked' : '' }}
                                class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 flex gap-4 items-center">
            <button 
                type="button" 
                id="select-all"
                class="text-sm text-teal-600 hover:text-teal-800 font-medium"
            >
                ✓ Tout sélectionner
            </button>
            <button 
                type="button" 
                id="deselect-all"
                class="text-sm text-gray-600 hover:text-gray-800 font-medium"
            >
                ✗ Tout désélectionner
            </button>
        </div>

        <!-- Buttons -->
        <div class="mt-8 flex gap-4">
            <button 
                type="submit"
                class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 font-semibold"
            >
                Enregistrer les permissions
            </button>
            <a 
                href="{{ route('admin.users.index') }}"
                class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 font-semibold"
            >
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
    // Select all checkboxes
    document.getElementById('select-all').addEventListener('click', function() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = true;
        });
    });

    // Deselect all checkboxes
    document.getElementById('deselect-all').addEventListener('click', function() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    });
</script>
@endsection
