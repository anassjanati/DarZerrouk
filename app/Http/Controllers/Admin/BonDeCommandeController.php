<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BonDeCommande;
use App\Models\BonDeCommandeLine;
use App\Models\Book;
use App\Models\Supplier;
use App\Models\Stock;
use Auth;
use Spatie\Activitylog\Facades\Activity;

class BonDeCommandeController extends Controller
{
    // List all bons de commande
    public function index() {
        $commands = BonDeCommande::with('supplier','user')->orderByDesc('id')->paginate(30);
        return view('admin.bon_de_commande.index', compact('commands'));
    }

    // Form for creating new bon de commande
    public function create() {
        $books = Book::where('is_active', true)->get();
        $suppliers = Supplier::all();
        return view('admin.bon_de_commande.create', compact('books', 'suppliers'));
    }

    // Store new bon de commande and update stocks
    public function store(Request $request) {
        $request->validate([
            'supplier_id'      => 'required|exists:suppliers,id',
            'date'             => 'required|date',
            'lines.*.book_id'  => 'required|exists:books,id',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.cost_price' => 'required|numeric|min:0',
        ]);
        $bon = BonDeCommande::create([
            'ref'         => 'BC-' . time(),
            'supplier_id' => $request->supplier_id,
            'date'        => $request->date,
            'status'      => 'completed',
            'comments'    => $request->comments,
            'user_id'     => Auth::id(),
        ]);
        foreach($request->lines as $line){
            BonDeCommandeLine::create([
                'bon_de_commande_id' => $bon->id,
                'book_id'            => $line['book_id'],
                'quantity'           => $line['quantity'],
                'cost_price'         => $line['cost_price'],
            ]);
            // Find each book's assigned main zone
            $book = Book::find($line['book_id']);
            $zoneId = $book->zone_id; // Assumes direct zone_id column

            if ($zoneId) {
                $stock = Stock::firstOrNew([
                    'book_id'      => $line['book_id'],
                    'zone_id'      => $zoneId,
                    'location_type'=> 'library'
                ]);
                $stock->quantity = ($stock->quantity ?? 0) + $line['quantity'];
                $stock->save();
            }
        }
        // Log history (tracabilite) using facade
        Activity::log("Bon de commande créé et stocks mis à jour.")
            ->causedBy(Auth::user());

        return redirect()->route('admin.bon_de_commande.show', $bon->id);
    }

    // Show one bon de commande
    public function show(BonDeCommande $bon_de_commande) {
    $bon_de_commande->load('lines.book','supplier','user');
    return view('admin.bon_de_commande.show', ['bon_de_commande' => $bon_de_commande]);
}

public function print(BonDeCommande $bon_de_commande) {
    $bon_de_commande->load('lines.book','supplier','user');
    return view('admin.bon_de_commande.print', ['bon_de_commande' => $bon_de_commande]);
}


    // Tracabilité/historique
    public function history() {
        $history = Activity::where('causer_id', Auth::id())->orderByDesc('id')->get();
        return view('admin.bon_de_commande.history', compact('history'));
    }
}
