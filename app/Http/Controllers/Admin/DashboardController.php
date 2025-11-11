<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Book;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's statistics
        $todaySales = Sale::today()->completed()->sum('total_amount');
        $todaySalesCount = Sale::today()->completed()->count();
        
        // This month statistics
        $monthSales = Sale::whereMonth('sale_date', date('m'))
                          ->whereYear('sale_date', date('Y'))
                          ->completed()
                          ->sum('total_amount');
        
        // Inventory alerts
        $lowStockBooks = Book::active()->lowStock()->count();
        $outOfStockBooks = Book::active()->outOfStock()->count();
        
        // Recent sales
        $recentSales = Sale::with(['user', 'customer'])
                           ->latest()
                           ->limit(10)
                           ->get();
        
        // Top selling books
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
        
        // Active users count
        $activeUsers = User::where('is_active', true)->count();
        $activeCashiers = User::whereHas('role', function($q) {
            $q->where('name', 'cashier');
        })->where('is_active', true)->count();
        
        // Total customers
        $totalCustomers = Customer::active()->count();
        
        return view('admin.dashboard', compact(
            'todaySales',
            'todaySalesCount',
            'monthSales',
            'lowStockBooks',
            'outOfStockBooks',
            'recentSales',
            'topBooks',
            'activeUsers',
            'activeCashiers',
            'totalCustomers'
        ));
    }
}
