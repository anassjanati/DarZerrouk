<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Stock;
use App\Models\Zone;
use App\Models\SousZone;
use App\Models\SousSousZone;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Facades\Activity;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $librarieZones   = Zone::librairie()->active()->with('sousZones')->orderBy('code')->get();
        $magasinageZones = Zone::magasinage()->active()->with('sousZones')->orderBy('code')->get();

        $data = compact('librarieZones', 'magasinageZones');

        if ($request->routeIs('manager.stocks.transfer')) {
            return view('manager.stocks.transfer', $data);
        }

        return view('admin.stocks.transfer', $data);
    }

    public function searchBook(Request $request)
    {
        $barcode = trim($request->input('barcode'));

        if ($barcode === '') {
            return response()->json(['error' => 'Code-barres vide'], 422);
        }

        $book = Book::where('barcode', $barcode)
            ->orWhere('barcode', 'like', $barcode . '%')
            ->with(['author', 'category', 'stocks.zone', 'stocks.sousZone', 'stocks.sousSousZone'])
            ->first();

        if (! $book) {
            return response()->json(['error' => 'Livre non trouvé'], 404);
        }

        $librarieStock   = [];
        $magasinageStock = [];

        foreach ($book->stocks as $stock) {
            $location = $this->formatLocation($stock);

            if ($stock->zone && $stock->zone->type === 'librairie') {
                $librarieStock[] = [
                    'stock_id'          => $stock->id,
                    'location'          => $location,
                    'quantity'          => (int) $stock->quantity,
                    'zone_id'           => $stock->zone_id,
                    'sous_zone_id'      => $stock->sous_zone_id,
                    'sous_sous_zone_id' => $stock->sous_sous_zone_id,
                ];
            } elseif ($stock->zone && $stock->zone->type === 'magasinage') {
                $magasinageStock[] = [
                    'stock_id'          => $stock->id,
                    'location'          => $location,
                    'quantity'          => (int) $stock->quantity,
                    'zone_id'           => $stock->zone_id,
                    'sous_zone_id'      => $stock->sous_zone_id,
                    'sous_sous_zone_id' => $stock->sous_sous_zone_id,
                ];
            }
        }

        return response()->json([
            'book' => [
                'id'      => $book->id,
                'barcode' => $book->barcode,
                'title'   => $book->display_title,
                'author'  => $book->author->name ?? 'N/A',
            ],
            'librairie_stock'  => $librarieStock,
            'magasinage_stock' => $magasinageStock,
            'total_librairie'  => collect($librarieStock)->sum('quantity'),
            'total_magasinage' => collect($magasinageStock)->sum('quantity'),
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'book_id'                => 'required|exists:books,id',
            'from_zone_id'           => 'required|exists:zones,id',
            'to_zone_id'             => 'required|exists:zones,id',
            'from_sous_zone_id'      => 'nullable|exists:sous_zones,id',
            'to_sous_zone_id'        => 'nullable|exists:sous_zones,id',
            'from_sous_sous_zone_id' => 'nullable|exists:sous_sous_zones,id',
            'to_sous_sous_zone_id'   => 'nullable|exists:sous_sous_zones,id',
            'quantity'               => 'required|integer|min:1',
            'notes'                  => 'nullable|string',
        ]);

        $fromZone = Zone::findOrFail($request->from_zone_id);
        $toZone   = Zone::findOrFail($request->to_zone_id);

        // only allow librairie <-> magasinage
        if ($fromZone->type === $toZone->type) {
            return response()->json([
                'error' => 'Transfert uniquement entre Librairie et Magasinage',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Find source stock: exact tuple (nulls allowed)
            $sourceStock = Stock::where('book_id', $request->book_id)
                ->where('zone_id', $request->from_zone_id)
                ->where('sous_zone_id', $request->from_sous_zone_id)
                ->where('sous_sous_zone_id', $request->from_sous_sous_zone_id)
                ->first();

            if (! $sourceStock || $sourceStock->quantity < $request->quantity) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Stock insuffisant dans la zone source',
                ], 422);
            }

            // Decrement source
            $sourceStock->quantity -= (int) $request->quantity;
            $sourceStock->save();

            // Increment destination (null sous_zones allowed)
            $destStock = Stock::firstOrCreate(
                [
                    'book_id'           => $request->book_id,
                    'zone_id'           => $request->to_zone_id,
                    'sous_zone_id'      => $request->to_sous_zone_id,
                    'sous_sous_zone_id' => $request->to_sous_sous_zone_id,
                ],
                ['quantity' => 0]
            );

            $destStock->quantity += (int) $request->quantity;
            $destStock->save();

            // Log movement
            $movement = StockMovement::create([
                'book_id'                => $request->book_id,
                'user_id'                => Auth::id(),
                'from_zone_id'           => $request->from_zone_id,
                'to_zone_id'             => $request->to_zone_id,
                'from_sous_zone_id'      => $request->from_sous_zone_id,
                'to_sous_zone_id'        => $request->to_sous_zone_id,
                'from_sous_sous_zone_id' => $request->from_sous_sous_zone_id,
                'to_sous_sous_zone_id'   => $request->to_sous_sous_zone_id,
                'quantity'               => (int) $request->quantity,
                'notes'                  => $request->notes,
            ]);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($movement)
                ->log("Transfert de stock: {$request->quantity} unités transférées");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$request->quantity} unités transférées avec succès",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erreur interne: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $query = StockMovement::with([
            'book.author',
            'user',
            'fromZone', 'toZone',
            'fromSousZone', 'toSousZone',
            'fromSousSousZone', 'toSousSousZone',
        ]);

        if ($request->filled('direction')) {
            if ($request->direction === 'to_librairie') {
                $query->whereHas('toZone', fn($q) => $q->where('type', 'librairie'));
            } elseif ($request->direction === 'to_magasinage') {
                $query->whereHas('toZone', fn($q) => $q->where('type', 'magasinage'));
            }
        }

        if ($request->filled('barcode')) {
            $query->whereHas('book', fn($q) => $q->where('barcode', $request->barcode));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->orderByDesc('created_at')->paginate(50);

        $data = compact('movements');

        if ($request->routeIs('manager.stocks.history')) {
            return view('manager.stocks.history', $data);
        }

        return view('admin.stocks.history', $data);
    }

    public function getSousZones(Request $request)
    {
        $zoneId    = $request->input('zone_id');
        $sousZones = SousZone::where('zone_id', $zoneId)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return response()->json($sousZones);
    }

    public function getSousSousZones(Request $request)
    {
        $sousZoneId    = $request->input('sous_zone_id');
        $sousSousZones = SousSousZone::where('sous_zone_id', $sousZoneId)
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name']);

        return response()->json($sousSousZones);
    }

    private function formatLocation(Stock $stock): string
    {
        if ($stock->sousSousZone) return $stock->sousSousZone->code;
        if ($stock->sousZone)     return $stock->sousZone->code;
        if ($stock->zone)         return $stock->zone->code;

        return 'N/A';
    }
}
