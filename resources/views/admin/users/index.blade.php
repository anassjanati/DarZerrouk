@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Utilisateurs</h1>
        <p class="text-sm text-slate-500">
            Gestion des caissiers, gérants et administrateurs de la plateforme.
        </p>
        <div class="mt-2 flex flex-wrap gap-3 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Actifs : {{ $users->where('is_active', true)->count() }}
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                Total : {{ $users->total() ?? $users->count() }}
            </span>
        </div>
    </div>

    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm
              hover:bg-teal-700 hover:shadow-md transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4"/>
        </svg>
        Nouvel utilisateur
    </a>
</div>

{{-- Table utilisateurs --}}
<div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-100">
        <thead class="bg-slate-50/80">
        <tr>
            <th class="px-6 py-3 text-left text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
                Utilisateur
            </th>
            <th class="px-6 py-3 text-left text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
                Email
            </th>
            <th class="px-6 py-3 text-left text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
                Téléphone
            </th>
            <th class="px-6 py-3 text-left text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
                Rôle
            </th>
            <th class="px-6 py-3 text-left text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
                Statut
            </th>
            <th class="px-6 py-3 text-left text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
                Créé le
            </th>
            <th class="px-6 py-3 text-right text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
                Actions
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">
        @forelse($users as $user)
            <tr class="hover:bg-slate-50/80 transition-colors">
                {{-- Utilisateur --}}
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 h-9 w-9 rounded-full bg-gradient-to-br from-teal-100 to-sky-100
                                    flex items-center justify-center text-xs font-semibold text-teal-700">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-900">
                                {{ $user->name }}
                            </div>
                            <div class="text-xs text-slate-500">
                                @if($user->id === auth()->id())
                                    Vous-même
                                @else
                                    ID #{{ $user->id }}
                                @endif
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Email --}}
                <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-800">
                    {{ $user->email }}
                </td>

                {{-- Téléphone --}}
                <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-800">
                    {{ $user->phone ?? '—' }}
                </td>

                {{-- Rôle --}}
                <td class="px-6 py-3 whitespace-nowrap">
                    @php
                        $role = $user->role->name ?? null;
                        $roleLabel = $user->role->display_name ?? $role;
                        $roleClasses = match($role) {
                            'admin'   => 'bg-purple-100 text-purple-800',
                            'manager' => 'bg-sky-100 text-sky-800',
                            'cashier' => 'bg-emerald-100 text-emerald-800',
                            default   => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $roleClasses }}">
                        {{ $roleLabel }}
                    </span>
                </td>

                {{-- Statut --}}
                <td class="px-6 py-3 whitespace-nowrap">
    <td class="px-6 py-3 whitespace-nowrap">
    <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="inline-flex items-center gap-2">
        @csrf
        @method('PATCH')

        <button type="submit"
                class="relative inline-flex h-5 w-9 items-center rounded-full transition
                       {{ $user->is_active ? 'bg-emerald-500/80' : 'bg-slate-300' }}">
            <span class="sr-only">Basculer le statut</span>
            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow
                         transition
                         {{ $user->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
        </button>

        <span class="text-[11px] font-medium {{ $user->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
            {{ $user->is_active ? 'Actif' : 'Inactif' }}
        </span>
    </form>
</td>


                {{-- Date de création --}}
                <td class="px-6 py-3 whitespace-nowrap text-sm text-slate-500">
                    {{ $user->created_at?->format('d/m/Y') }}
                </td>

                {{-- Actions --}}
                <td class="px-6 py-3 whitespace-nowrap text-right text-sm">
                    <div class="inline-flex items-center gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-800"
                           title="Modifier">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>

                        <a href="{{ route('admin.users.permissions', $user) }}"
                           class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-sky-50 text-sky-700 hover:bg-sky-100 hover:text-sky-900"
                           title="Permissions">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </a>

                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-800"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-6 text-center text-sm text-slate-500">
                    Aucun utilisateur trouvé.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endif
@endsection
