<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query()
            ->withCount('purchaseOrders');

        if ($search = trim($request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($city = trim($request->get('city', ''))) {
            $query->where('city', 'like', "%{$city}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $suppliers = $query
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function show(Supplier $supplier)
    {
        $supplier->load([
            'purchaseOrders.lines',
            'purchaseOrders.user',
        ]);

        $orders = $supplier->purchaseOrders()
            ->with('lines')
            ->orderByDesc('date')
            ->paginate(15);

        $totalAmount = $orders->getCollection()->sum->total;
        $lastOrder   = $supplier->purchaseOrders()->orderByDesc('date')->first();

        return view('admin.suppliers.show', compact(
            'supplier',
            'orders',
            'totalAmount',
            'lastOrder'
        ));
    }
}
