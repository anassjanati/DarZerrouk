<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;

class PosDashboardController extends Controller
{
    public function index()
    {
        // For now, simple aggregates – you can refine later.
        $todayPosSalesAmount = Sale::today()->completed()->sum('total_amount');
        $todayPosTickets     = Sale::today()->completed()->count();
        $todayAvgTicket      = $todayPosTickets > 0
            ? $todayPosSalesAmount / $todayPosTickets
            : 0;

        $activeCashiers = User::whereHas('role', fn ($q) => $q->where('name', 'cashier'))
            ->where('is_active', true)
            ->count();

        $recentTickets = Sale::with('user')
            ->completed()
            ->latest()
            ->limit(10)
            ->get();

        // You can later add: salesByHour, salesByCashier, paymentsBreakdown, cashierPerformance...
        $salesByHour    = [];
        $salesByCashier = [];
        $paymentsSplit  = [];

        return view('admin.pos-dashboard', compact(
            'todayPosSalesAmount',
            'todayPosTickets',
            'todayAvgTicket',
            'activeCashiers',
            'recentTickets',
            'salesByHour',
            'salesByCashier',
            'paymentsSplit'
        ));
    }
}
