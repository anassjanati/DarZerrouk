<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Book;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's statistics (manager cares about the same sales KPIs)
        $todaySales = Sale::today()->completed()->sum('total_amount');
        $todaySalesCount = Sale::today()->completed()->count();

        // This month statistics
        $monthSales = Sale::whereMonth('sale_date', date('m'))
                          ->whereYear('sale_date', date('Y'))
                          ->completed()
                          ->sum('total_amount');

        // Inventory alerts (overview only)
        $lowStockBooks = Book::active()->lowStock()->count();
        $outOfStockBooks = Book::active()->outOfStock()->count();

        // Recent sales (for oversight)
        $recentSales = Sale::with(['user', 'client'])
                           ->latest()
                           ->limit(10)
                           ->get();

        // Top selling books (last 30 days)
        $topBooks = DB::table('sale_items')
            ->join('books', 'sale_items.book_id', '=', 'books.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.payment_status', 'completed')
            ->whereDate('sales.sale_date', '>=', now()->subDays(30))
            ->select('books.title', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('books.id', 'books.title')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Active cashiers only (what manager cares about most)
        $activeCashiers = User::whereHas('role', function ($q) {
                $q->where('name', 'cashier');
            })
            ->where('is_active', true)
            ->count();

        // Total customers (for context)
        $totalCustomers = Customer::active()->count();

        return view('manager.dashboard', compact(
            'todaySales',
            'todaySalesCount',
            'monthSales',
            'lowStockBooks',
            'outOfStockBooks',
            'recentSales',
            'topBooks',
            'activeCashiers',
            'totalCustomers'
        ));
    }
}
