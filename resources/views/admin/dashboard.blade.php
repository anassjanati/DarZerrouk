@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<div class="min-h-[calc(100vh-80px)] -mx-4 px-4 py-6
            bg-gradient-to-br from-slate-50 via-white to-sky-50">

    <!-- Header + live status -->
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">
                    Tableau de bord
                </h1>
                <p class="text-sm text-slate-500">
                    Bienvenue, <span class="font-medium text-slate-700">{{ auth()->user()->name }}</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-emerald-700 border border-emerald-100">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    POS en ligne
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-3 py-1 text-sky-700 border border-sky-100">
                    {{ $activeCashiers }} caissiers actifs
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-3 py-1 text-slate-600 border border-slate-100">
                    Dernière activité :
                    <span class="font-medium">
                        {{ optional($lastActivityAt)->format('d/m/Y H:i') ?? 'N/A' }}
                    </span>
                </span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- KPI cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Today's Sales -->
            <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="absolute inset-0 opacity-60 bg-gradient-to-tr from-emerald-50 via-white to-sky-50"></div>
                <div class="relative p-5 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Ventes aujourd'hui
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            {{ number_format($todaySales, 2) }} DH
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $todaySalesCount }} transactions
                        </p>
                    </div>
                    <div class="shrink-0 bg-emerald-50 text-emerald-600 rounded-2xl p-3 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.7 0-3 .9-3 2s1.3 2 3 2 3 .9 3 2-1.3 2-3 2m0-8c1.1 0 2.1.4 2.6 1M12 8V7m0 1v8m0 0v1m0-1c-1.1 0-2.1-.4-2.6-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Month Sales -->
            <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-sky-50 via-white to-indigo-50"></div>
                <div class="relative p-5 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Ventes ce mois
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            {{ number_format($monthSales, 2) }} DH
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Total mensuel
                        </p>
                    </div>
                    <div class="shrink-0 bg-sky-50 text-sky-600 rounded-2xl p-3 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-amber-50 via-white to-rose-50"></div>
                <div class="relative p-5 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Stock faible
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-amber-700">
                            {{ $lowStockBooks }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $outOfStockBooks }} en rupture
                        </p>
                    </div>
                    <div class="shrink-0 bg-amber-50 text-amber-600 rounded-2xl p-3 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01M5.06 19h13.88c1.54 0 2.5-1.66 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.34.19 3 1.72 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-emerald-50 via-white to-slate-50"></div>
                <div class="relative p-5 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Utilisateurs actifs
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-700">
                            {{ $activeUsers }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $activeCashiers }} caissiers connectés
                        </p>
                    </div>
                    <div class="shrink-0 bg-emerald-50 text-emerald-600 rounded-2xl p-3 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.35a4 4 0 110 5.3M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.2M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lists -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Recent Sales -->
            <div class="rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Ventes récentes
                    </h2>
                    <a href="{{ route('pos.index') }}"
                       class="text-xs text-sky-700 hover:text-sky-900 font-medium">
                        Voir tout
                    </a>
                </div>
                <div class="px-5 pb-4">
                    <div class="divide-y divide-slate-100">
                        @forelse($recentSales as $sale)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">
                                        {{ $sale->invoice_number }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $sale->user->name }} • {{ $sale->sale_date->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <p class="text-sm font-semibold text-emerald-700">
                                    {{ number_format($sale->total_amount, 2) }} DH
                                </p>
                            </div>
                        @empty
                            <p class="py-6 text-center text-xs text-slate-400">
                                Aucune vente récente
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Books -->
            <div class="rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Livres les plus vendus (30 jours)
                    </h2>
                    <a href="{{ route('admin.books.manage') }}"
                       class="text-xs text-sky-700 hover:text-sky-900 font-medium">
                        Gérer les livres
                    </a>
                </div>
                <div class="px-5 pb-4">
                    <div class="divide-y divide-slate-100">
                        @forelse($topBooks as $book)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-900 line-clamp-1">
                                        {{ $book->title }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        {{ $book->total_sold }} exemplaires vendus
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="py-6 text-center text-xs text-slate-400">
                                Aucune donnée disponible
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
