@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Tableau de bord</h1>
    <p class="text-gray-600">Bienvenue, {{ auth()->user()->name }}</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Today's Sales -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Ventes aujourd'hui</p>
                <p class="text-2xl font-bold text-teal-600">{{ number_format($todaySales, 2) }} DH</p>
                <p class="text-xs text-gray-500">{{ $todaySalesCount }} transactions</p>
            </div>
            <div class="bg-teal-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Month Sales -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Ventes ce mois</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($monthSales, 2) }} DH</p>
                <p class="text-xs text-gray-500">Total mensuel</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Stock faible</p>
                <p class="text-2xl font-bold text-orange-600">{{ $lowStockBooks }}</p>
                <p class="text-xs text-gray-500">{{ $outOfStockBooks }} en rupture</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Users -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Utilisateurs actifs</p>
                <p class="text-2xl font-bold text-green-600">{{ $activeUsers }}</p>
                <p class="text-xs text-gray-500">{{ $activeCashiers }} caissiers</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Sales -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-4">Ventes récentes</h2>
        <div class="space-y-3">
            @forelse($recentSales as $sale)
            <div class="flex justify-between items-center border-b pb-3">
                <div>
                    <p class="font-semibold">{{ $sale->invoice_number }}</p>
                    <p class="text-sm text-gray-600">{{ $sale->user->name }} • {{ $sale->sale_date->format('d/m/Y H:i') }}</p>
                </div>
                <p class="font-bold text-teal-600">{{ number_format($sale->total_amount, 2) }} DH</p>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4">Aucune vente récente</p>
            @endforelse
        </div>
    </div>

    <!-- Top Books -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-4">Livres les plus vendus (30 jours)</h2>
        <div class="space-y-3">
            @forelse($topBooks as $book)
            <div class="flex justify-between items-center border-b pb-3">
                <div class="flex-1">
                    <p class="font-semibold">{{ $book->title }}</p>
                    <p class="text-sm text-gray-600">{{ $book->total_sold }} exemplaires</p>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-center py-4">Aucune donnée disponible</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
