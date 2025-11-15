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
    public function index()
    {
        $books = Book::where('is_active', true)->orderBy('title')->get();
        $zones = Zone::orderBy('name')->get();
        $sousZones = SousZone::orderBy('name')->get();
        $sousSousZones = SousSousZone::orderBy('name')->get();

        // Prepare zoneStocks for table: [zone => [book stocks in this zone]]
        $zoneStocks = [];
        foreach ($zones as $zone) {
            $stocks = Stock::with('book')
                ->where('zone_id', $zone->id)
                ->where('location_type', 'magasinage')
                ->get();
            $zoneStocks[] = [
                'zone'  => $zone,
                'books' => $stocks,
            ];
        }

        return view('admin.magasinage.index', compact('books', 'zones', 'sousZones', 'sousSousZones', 'zoneStocks'));
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
        Stock::create([
            'book_id'          => $request->book_id,
            'zone_id'          => $request->zone_id,
            'sous_zone_id'     => $request->sous_zone_id,
            'sous_sous_zone_id'=> $request->sous_sous_zone_id,
            'quantity'         => $request->quantity,
            'location_type'    => 'magasinage',
        ]);
        return back()->with('success', 'Stock ajouté.');
    }

    // Stock transfer
    public function transfer(Request $request)
    {
        $request->validate([
            'book_id'      => 'required|exists:books,id',
            'from_zone_id' => 'required|exists:zones,id',
            'to_zone_id'   => 'required|exists:zones,id|different:from_zone_id',
            'quantity'     => 'required|integer|min:1',
        ]);
        // Reduce stock from origin
        $stock = Stock::where([
            'book_id' => $request->book_id,
            'zone_id' => $request->from_zone_id,
            'location_type' => 'magasinage'
        ])->first();
        if (!$stock || $stock->quantity < $request->quantity) {
            return back()->with('error', 'Stock insuffisant.');
        }
        $stock->quantity -= $request->quantity;
        $stock->save();

        // Add stock to destination (or create new entry)
        $toStock = Stock::firstOrNew([
            'book_id' => $request->book_id,
            'zone_id' => $request->to_zone_id,
            'location_type' => 'magasinage'
        ]);
        $toStock->quantity = ($toStock->quantity ?? 0) + $request->quantity;
        $toStock->save();

        return back()->with('success', 'Stock transféré.');
    }
}
