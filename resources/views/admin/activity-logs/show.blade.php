@extends('layouts.admin')

@section('title', 'Détails de l\'activité')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('admin.activity-logs.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Détails de l'activité</h1>
            <p class="text-gray-600">{{ $activityLog->created_at->format('d/m/Y à H:i:s') }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-3xl">
    <dl class="divide-y divide-gray-200">
        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">Date et heure</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ $activityLog->created_at->format('d/m/Y à H:i:s') }}
            </dd>
        </div>

        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">Utilisateur</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ $activityLog->user->name }} ({{ $activityLog->user->email }})
            </dd>
        </div>

        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">Action</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ $activityLog->action === 'login' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $activityLog->action === 'logout' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $activityLog->action === 'create' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $activityLog->action === 'update' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $activityLog->action === 'delete' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($activityLog->action) }}
                </span>
            </dd>
        </div>

        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">Description</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ $activityLog->description }}
            </dd>
        </div>

        @if($activityLog->model)
        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">Modèle affecté</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ $activityLog->model }} #{{ $activityLog->model_id }}
            </dd>
        </div>
        @endif

        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">Adresse IP</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ $activityLog->ip_address }}
            </dd>
        </div>

        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">Navigateur</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ $activityLog->user_agent }}
            </dd>
        </div>

        @if($activityLog->properties)
        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
            <dt class="text-sm font-medium text-gray-500">Données supplémentaires</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <pre class="bg-gray-100 p-4 rounded-lg overflow-auto">{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </dd>
        </div>
        @endif
    </dl>
</div>
@endsection
