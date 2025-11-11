<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Zone;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\SousZone;

class MagasinageController extends Controller
{
public function index(Request $request)
{
    $stocks = Stock::with(['book', 'zone', 'sousZone', 'sousSousZone'])->orderBy('zone_id')->orderBy('book_id')->get();
    $books = Book::orderBy('title')->get();
    $zones = Zone::orderBy('name')->get();
    $sousZones = SousZone::orderBy('name')->get(); // if needed
    $sousSousZones = SousSousZone::orderBy('name')->get(); // if needed

    return view('admin.magasinage', compact('stocks', 'books', 'zones', 'sousZones', 'sousSousZones'));
}



    public function transfer(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'from_zone_id' => 'required|exists:zones,id',
            'to_zone_id' => 'required|exists:zones,id|different:from_zone_id',
            'quantity' => 'required|integer|min:1'
        ]);
        DB::transaction(function () use ($request) {
            $stockFrom = Stock::where('book_id', $request->book_id)
                              ->where('zone_id', $request->from_zone_id)->lockForUpdate()->firstOrFail();
            if ($stockFrom->quantity < $request->quantity) {
                abort(400, "Stock insuffisant pour le transfert");
            }
            $stockFrom->quantity -= $request->quantity;
            $stockFrom->save();

            $stockTo = Stock::firstOrCreate([
                'book_id' => $request->book_id,
                'zone_id' => $request->to_zone_id,
            ]);
            $stockTo->quantity += $request->quantity;
            $stockTo->save();

            StockTransfer::create([
                'book_id' => $request->book_id,
                'from_zone_id' => $request->from_zone_id,
                'to_zone_id' => $request->to_zone_id,
                'quantity' => $request->quantity,
                'user_id' => auth()->id(),
            ]);
        });
        return redirect()->route('admin.magasinage.index')->with('success', 'Transfert effectué avec succès.');
    }
}
