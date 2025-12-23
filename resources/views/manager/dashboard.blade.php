@extends('layouts.manager')

@section('title', 'Tableau de bord')

@section('content')
<div class="min-h-[calc(100vh-80px)] -mx-4 px-4 py-6
            bg-gradient-to-br from-slate-50 via-white to-sky-50">

    <div class="max-w-6xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-2">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">
                    Tableau de bord Manager
                </h1>
                <p class="text-sm text-slate-500">
                    Vue rapide des actions de gestion pour {{ auth()->user()->name }}.
                </p>
            </div>
        </div>

        {{-- Action cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- View books (read-only) --}}
            <a href="{{ route('admin.books.manage') }}"
               class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm
                      hover:shadow-md hover:border-sky-100 transition">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-sky-50 via-white to-emerald-50"></div>
                <div class="relative p-6 flex flex-col h-full">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Catalogue des livres
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Consulter les stocks, emplacements et performances des titres.
                            </p>
                        </div>
                        <div class="shrink-0 bg-white/80 rounded-xl p-2 text-sky-600 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.25v13m0-13C10.8 5.5 9.25 5 7.5 5S4.2 5.5 3 6.25v13C4.2 18.5 5.8 18 7.5 18s3.3.5 4.5 1.25m0-13C13.2 5.5 14.75 5 16.5 5s3.3.5 4.5 1.25v13C19.8 18.5 18.2 18 16.5 18s-3.3.5-4.5 1.25"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">
                        Gestion en lecture seule : création, archivage et suppression restent réservés à l’administrateur.
                    </p>
                    <span class="mt-auto inline-flex items-center text-sm font-medium text-sky-700">
                        Accéder aux livres
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </a>

            {{-- Create bon de commande --}}
            <a href="{{ route('manager.bon_de_commande.create') }}"
               class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm
                      hover:shadow-md hover:border-emerald-100 transition">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-emerald-50 via-white to-sky-50"></div>
                <div class="relative p-6 flex flex-col h-full">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">
                                Nouveau bon de commande
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Préparer les réassorts avec choix du fournisseur, des livres et des quantités.
                            </p>
                        </div>
                        <div class="shrink-0 bg-white/80 rounded-xl p-2 text-emerald-600 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">
                        Idéal pour traiter les alertes stock et préparer les commandes fournisseurs.
                    </p>
                    <span class="mt-auto inline-flex items-center text-sm font-medium text-emerald-700">
                        Créer un bon de commande
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
