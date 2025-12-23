@extends('layouts.admin')

@section('title', 'Point de vente – Vue admin')

@section('content')
<div class="min-h-[calc(100vh-80px)] -mx-4 px-4 py-6
            bg-gradient-to-br from-slate-50 via-white to-sky-50">

    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Header + filters --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">
                    Activité Point de vente
                </h1>
                <p class="text-sm text-slate-500">
                    Suivi temps réel des ventes en caisse et de la performance des caissiers.
                </p>
            </div>

            <form method="GET" class="flex flex-wrap gap-3 text-xs">
                <select name="period"
                        class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-700">
                    <option value="today">Aujourd’hui</option>
                    <option value="7d">7 derniers jours</option>
                    <option value="30d">30 derniers jours</option>
                    <option value="custom">Personnalisé</option>
                </select>

                <select name="cashier"
                        class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-700">
                    <option value="">Tous les caissiers</option>
                    {{-- inject options from controller later --}}
                </select>

                <button type="submit"
                        class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-sky-600 text-white font-medium hover:bg-sky-700">
                    Filtrer
                </button>
            </form>
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Tickets du jour --}}
            <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-sky-50 via-white to-emerald-50"></div>
                <div class="relative p-5 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Tickets POS aujourd’hui
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            {{ $todayPosTickets }}
                        </p>
                    </div>
                    <div class="shrink-0 bg-white/80 rounded-xl p-2 text-sky-600 shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 7h6m-6 4h3m-7 7h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v9a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- CA POS du jour --}}
            <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-emerald-50 via-white to-sky-50"></div>
                <div class="relative p-5 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            CA POS aujourd’hui
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            {{ number_format($todayPosSalesAmount, 2) }} DH
                        </p>
                    </div>
                    <div class="shrink-0 bg-white/80 rounded-xl p-2 text-emerald-600 shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.7 0-3 .9-3 2s1.3 2 3 2 3 .9 3 2-1.3 2-3 2m0-8c1.1 0 2.1.4 2.6 1M12 8V7m0 1v8m0 0v1m0-1c-1.1 0-2.1-.4-2.6-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Ticket moyen --}}
            <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-indigo-50 via-white to-sky-50"></div>
                <div class="relative p-5 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Ticket moyen POS
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            {{ number_format($todayAvgTicket, 2) }} DH
                        </p>
                    </div>
                    <div class="shrink-0 bg-white/80 rounded-xl p-2 text-indigo-600 shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 10h16M10 14h10M10 18h10M4 14h.01M4 18h.01"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Caissiers actifs --}}
            <div class="relative overflow-hidden rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="absolute inset-0 opacity-70 bg-gradient-to-tr from-slate-50 via-white to-emerald-50"></div>
                <div class="relative p-5 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                            Caissiers actifs
                        </p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-700">
                            {{ $activeCashiers }}
                        </p>
                    </div>
                    <div class="shrink-0 bg-white/80 rounded-xl p-2 text-emerald-600 shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.35a4 4 0 110 5.3M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.2M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts placeholders (you can hook Chart.js or similar) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Ventes POS par heure
                    </h2>
                    <span class="text-[11px] text-slate-500">Aujourd’hui</span>
                </div>
                <div class="h-56 flex items-center justify-center text-xs text-slate-400">
                    Zone graphique (Chart.js) – ventes par heure
                </div>
            </div>

            <div class="rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Ventes par caissier
                    </h2>
                    <span class="text-[11px] text-slate-500">CA ou nombre de tickets</span>
                </div>
                <div class="h-56 flex items-center justify-center text-xs text-slate-400">
                    Zone graphique (Chart.js) – ventes par caissier
                </div>
            </div>
        </div>

        {{-- Tables --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Derniers tickets POS --}}
            <div class="rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Derniers tickets POS
                    </h2>
                </div>
                <div class="px-5 pb-4">
                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse($recentTickets as $sale)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900">
                                        {{ $sale->invoice_number }}
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $sale->user->name ?? '—' }} • {{ $sale->sale_date->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <p class="font-semibold text-emerald-700">
                                    {{ number_format($sale->total_amount, 2) }} DH
                                </p>
                            </div>
                        @empty
                            <p class="py-6 text-center text-[11px] text-slate-400">
                                Aucun ticket pour la période sélectionnée.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Placeholder: Performance des caissiers --}}
            <div class="rounded-2xl bg-white/80 backdrop-blur border border-slate-100 shadow-sm">
                <div class="px-5 pt-5 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Performance des caissiers
                    </h2>
                </div>
                <div class="px-5 pb-4 text-xs text-slate-400 flex items-center justify-center h-40">
                    Tableau à compléter : tickets / CA / ticket moyen par caissier.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
