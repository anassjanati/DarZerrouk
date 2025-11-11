@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Modifier l'utilisateur</h1>
            <p class="text-gray-600">{{ $user->name }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-3xl">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nom complet <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name', $user->name) }}"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('name') border-red-500 @enderror"
            />
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email <span class="text-red-500">*</span>
            </label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="{{ old('email', $user->email) }}"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('email') border-red-500 @enderror"
            />
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div class="mb-6">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                Téléphone
            </label>
            <input 
                type="text" 
                name="phone" 
                id="phone" 
                value="{{ old('phone', $user->phone) }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('phone') border-red-500 @enderror"
            />
            @error('phone')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role -->
        <div class="mb-6">
            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                Rôle <span class="text-red-500">*</span>
            </label>
            <select 
                name="role_id" 
                id="role_id" 
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('role_id') border-red-500 @enderror"
            >
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->display_name }} - {{ $role->description }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password (Optional) -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Nouveau mot de passe <span class="text-gray-500">(laisser vide pour ne pas modifier)</span>
            </label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent @error('password') border-red-500 @enderror"
                placeholder="••••••••"
            />
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active Status -->
        <div class="mb-6">
            <label class="flex items-center">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    value="1"
                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                />
                <span class="ml-2 text-sm text-gray-700">Utilisateur actif</span>
            </label>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <button 
                type="submit"
                class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 font-semibold"
            >
                Mettre à jour
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
@endsection
