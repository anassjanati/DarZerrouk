<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Zone;
use App\Models\SousZone;
use App\Models\SousSousZone;
use App\Models\Stock;

class MagasinageController extends Controller
{
public function index(Request $request)
{
    $zones = Zone::magasinage()->active()->orderBy('code')->get();

    $stocks = Stock::with(['book', 'zone', 'sousZone', 'sousSousZone'])
        ->whereHas('zone', fn($q) => $q->where('type', 'magasinage'))
        ->orderBy('zone_id')
        ->orderBy('book_id')
        ->get();

    $totalUnits    = (int) $stocks->sum('quantity');
    $totalTitles   = $stocks->groupBy('book_id')->count();
    $lowReserve    = $stocks->filter(fn($s) =>
        $s->book && $s->book->reorder_level && $s->quantity < $s->book->reorder_level
    );
    $lowReserveCnt = $lowReserve->groupBy('book_id')->count();

    $data = compact('zones', 'stocks', 'totalUnits', 'totalTitles', 'lowReserveCnt');

    if ($request->routeIs('manager.magasinage.index')) {
        return view('manager.magasinage.index', $data);
    }

    return view('admin.magasinage.index', $data);
}



    // Stock add
    public function store(Request $request)
    {
        $request->validate([
            'book_id'          => 'required|exists:books,id',
            'zone_id'          => 'required|exists:zones,id',
            'quantity'         => 'required|integer|min:1',
            'sous_zone_id'     => 'nullable|exists:sous_zones,id',
            'sous_sous_zone_id'=> 'nullable|exists:sous_sous_zones,id',
        ]);

        // Either create a new stock line or increment existing one for same place
        $stock = Stock::firstOrNew([
            'book_id'          => $request->book_id,
            'zone_id'          => $request->zone_id,
            'sous_zone_id'     => $request->sous_zone_id,
            'sous_sous_zone_id'=> $request->sous_sous_zone_id,
        ]);

        $stock->quantity = ($stock->quantity ?? 0) + $request->quantity;
        $stock->save();

        return back()->with('success', 'Stock ajouté.');
    }

    // Stock transfer between zones (same book)
    public function transfer(Request $request)
    {
        $request->validate([
            'book_id'      => 'required|exists:books,id',
            'from_zone_id' => 'required|exists:zones,id',
            'to_zone_id'   => 'required|exists:zones,id|different:from_zone_id',
            'quantity'     => 'required|integer|min:1',
        ]);

        // Reduce stock from origin
        $fromStock = Stock::where([
            'book_id' => $request->book_id,
            'zone_id' => $request->from_zone_id,
        ])->first();

        if (!$fromStock || $fromStock->quantity < $request->quantity) {
            return back()->with('error', 'Stock insuffisant.');
        }

        $fromStock->quantity -= $request->quantity;
        $fromStock->save();

        // Add stock to destination (or create new entry)
        $toStock = Stock::firstOrNew([
            'book_id' => $request->book_id,
            'zone_id' => $request->to_zone_id,
        ]);
        $toStock->quantity = ($toStock->quantity ?? 0) + $request->quantity;
        $toStock->save();

        return back()->with('success', 'Stock transféré.');
    }
}
