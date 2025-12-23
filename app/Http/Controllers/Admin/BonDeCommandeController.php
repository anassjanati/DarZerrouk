<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BonDeCommande;
use App\Models\BonDeCommandeLine;
use App\Models\Book;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class BonDeCommandeController extends Controller
{
    /**
     * Display list of bon de commande
     */
    public function index(Request $request)
    {
        $query = BonDeCommande::with('supplier', 'user')->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commands = $query->paginate(30);
        
        if ($request->routeIs('manager.bon_de_commande.*')) {
        return view('manager.bon_de_commande.index', compact('commands'));
    }

        return view('admin.bon_de_commande.index', compact('commands'));
    }

    /**
     * Form for creating new bon de commande (Manager / Superviseur)
     */
    public function create()
    {
        $books = Book::where('is_active', true)->get();

        $booksForJs = $books->map(function ($book) {
            return [
                'id'            => $book->id,
                'barcode'       => $book->barcode,
                'title'         => $book->title,
                'selling_price' => $book->retail_price,
            ];
        })->values();

        $suppliers = Supplier::where('is_active', true)->get();

        return view('admin.bon_de_commande.create', compact('books', 'suppliers', 'booksForJs'));
    }

    /**
     * Store new bon de commande with status 'pending' (Manager creates)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'           => 'required|exists:suppliers,id',
            'date'                  => 'required|date',
            'comments'              => 'nullable|string',
            'lines'                 => 'required|array|min:1',
            'lines.*.book_id'       => 'required|integer|exists:books,id',
            'lines.*.quantity'      => 'required|integer|min:1',
            'lines.*.selling_price' => 'required|numeric|min:0',
        ], [
            'lines.required'            => 'Vous devez ajouter au moins un livre.',
            'lines.min'                 => 'Vous devez ajouter au moins un livre.',
            'lines.*.book_id.required'  => 'Chaque ligne doit avoir un livre sélectionné.',
            'lines.*.quantity.required' => 'La quantité est requise.',
            'lines.*.quantity.min'      => 'La quantité doit être au moins 1.',
        ]);

        DB::beginTransaction();

        try {
            $bon = BonDeCommande::create([
                'ref'         => 'BC-' . time(),
                'supplier_id' => $validated['supplier_id'],
                'date'        => $validated['date'],
                'status'      => 'pending',
                'comments'    => $validated['comments'] ?? null,
                'user_id'     => Auth::id(),
            ]);

            foreach ($validated['lines'] as $line) {
                BonDeCommandeLine::create([
                    'bon_de_commande_id' => $bon->id,
                    'book_id'            => $line['book_id'],
                    'quantity'           => $line['quantity'],
                    'selling_price'      => $line['selling_price'],
                    'cost_price'         => 0,
                ]);
            }

            ActivityLog::log(
                'bon_de_commande_created',
                'Bon de commande créé (en attente de validation)',
                BonDeCommande::class,
                $bon->id,
                [
                    'ref'         => $bon->ref,
                    'supplier_id' => $bon->supplier_id,
                    'status'      => $bon->status,
                ]
            );

            DB::commit();

            return redirect()
                ->route('admin.bon_de_commande.show', $bon->id)
                ->with('success', 'Bon de commande créé avec succès. En attente de validation admin.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Erreur création bon:', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Show bon de commande details
     */
    public function show(BonDeCommande $bon_de_commande)
    {
        $bon_de_commande->load('lines.book', 'supplier', 'user');

        return view('admin.bon_de_commande.show', [
            'bon_de_commande' => $bon_de_commande,
        ]);
    }

    /**
     * Form for Admin to validate or edit (cost + stock)
     */
    public function edit(BonDeCommande $bon_de_commande)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Seul l\'administrateur peut valider');
        }

        $bon_de_commande->load('lines.book', 'supplier');

        return view('admin.bon_de_commande.validate', compact('bon_de_commande'));
    }

    /**
     * Update bon de commande with cost_price and manage stock (Admin)
     */
    public function update(Request $request, BonDeCommande $bon_de_commande)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Seul l\'administrateur peut valider');
        }

        $action = $request->input('action', 'validate');

        if ($action === 'send_back') {
            $request->validate([
                'admin_note' => 'required|string|max:2000',
            ]);

            $bon_de_commande->status     = 'needs_correction';
            $bon_de_commande->admin_note = $request->admin_note;
            $bon_de_commande->save();

            ActivityLog::log(
                'bon_de_commande_returned',
                'Bon de commande retourné pour correction',
                BonDeCommande::class,
                $bon_de_commande->id,
                ['admin_note' => $bon_de_commande->admin_note]
            );

            return redirect()
                ->route('admin.bon_de_commande.show', $bon_de_commande->id)
                ->with('info', 'Bon retourné au créateur avec votre message.');
        }

        // Validation for cost prices
        $request->validate([
            'lines.*.cost_price'   => 'nullable|numeric|min:0',
            'discount_percentage'  => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $discountPercentage = $request->discount_percentage ?? 0;

            // 1) If already validated once, rollback previous stock
            if ($bon_de_commande->status === 'validated') {
                $bon_de_commande->load('lines.book');

                foreach ($bon_de_commande->lines as $oldLine) {
                    $this->addStockToLibrary($oldLine->book, -$oldLine->quantity);
                }
            }

            // 2) Apply new prices and re-add stock
            foreach ($request->lines as $lineId => $lineData) {
                $line = BonDeCommandeLine::find($lineId);
                if (! $line) {
                    continue;
                }

                if ($discountPercentage > 0) {
                    $line->cost_price = $line->selling_price * (1 - $discountPercentage / 100);
                } else {
                    $line->cost_price = $lineData['cost_price'] ?? 0;
                }

                $line->save();

                $this->addStockToLibrary($line->book, $line->quantity);
            }

            // 3) keep / set status validated
            $bon_de_commande->status     = 'validated';
            $bon_de_commande->admin_note = null;
            $bon_de_commande->save();

            ActivityLog::log(
                'bon_de_commande_validated',
                'Bon de commande validé et stocks ajustés',
                BonDeCommande::class,
                $bon_de_commande->id,
                ['discount_percentage' => $discountPercentage]
            );

            DB::commit();

            return redirect()
                ->route('admin.bon_de_commande.show', $bon_de_commande->id)
                ->with('success', 'Bon validé. Stock mis à jour en Librairie.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Erreur validation bon:', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Creator edit form (manager) for returned bons only
     */
    public function editCreator(BonDeCommande $bon_de_commande)
    {
        $user = Auth::user();

        // Only creator, only when returned for correction
        if ($bon_de_commande->user_id !== $user->id || $bon_de_commande->status !== 'needs_correction') {
            abort(403);
        }

        $bon_de_commande->load('lines.book', 'supplier');

        $books = Book::where('is_active', true)->get();
        $booksForJs = $books->map(function ($book) {
            return [
                'id'            => $book->id,
                'barcode'       => $book->barcode,
                'title'         => $book->title,
                'selling_price' => $book->retail_price,
            ];
        })->values();

        $suppliers = Supplier::where('is_active', true)->get();

        // You can reuse the same Blade as create() or a dedicated one under /manager
        return view('manager.bon_de_commande.edit', compact(
            'bon_de_commande',
            'books',
            'suppliers',
            'booksForJs'
        ));
    }

    /**
     * Creator update: modify header + lines, send back to admin (pending)
     */
    public function updateCreator(Request $request, BonDeCommande $bon_de_commande)
    {
        $user = Auth::user();

        if ($bon_de_commande->user_id !== $user->id || $bon_de_commande->status !== 'needs_correction') {
            abort(403);
        }

        $validated = $request->validate([
            'supplier_id'           => 'required|exists:suppliers,id',
            'date'                  => 'required|date',
            'comments'              => 'nullable|string',
            'lines'                 => 'required|array|min:1',
            'lines.*.book_id'       => 'required|integer|exists:books,id',
            'lines.*.quantity'      => 'required|integer|min:1',
            'lines.*.selling_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Update header
            $bon_de_commande->update([
                'supplier_id' => $validated['supplier_id'],
                'date'        => $validated['date'],
                'comments'    => $validated['comments'] ?? null,
                'status'      => 'pending',   // back to waiting for admin
                'admin_note'  => null,
            ]);

            // Reset lines
            $bon_de_commande->lines()->delete();

            foreach ($validated['lines'] as $line) {
                BonDeCommandeLine::create([
                    'bon_de_commande_id' => $bon_de_commande->id,
                    'book_id'            => $line['book_id'],
                    'quantity'           => $line['quantity'],
                    'selling_price'      => $line['selling_price'],
                    'cost_price'         => 0,
                ]);
            }

            ActivityLog::log(
                'bon_de_commande_updated_by_creator',
                'Bon de commande corrigé par le créateur et renvoyé en attente de validation',
                BonDeCommande::class,
                $bon_de_commande->id
            );

            DB::commit();

            // Manager area route
            return redirect()
                ->route('manager.bon_de_commande.show', $bon_de_commande->id)
                ->with('success', 'Bon corrigé et renvoyé à l’administrateur pour validation.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Print bon de commande
     */
    public function print(BonDeCommande $bon_de_commande)
    {
        $bon_de_commande->load('lines.book', 'supplier', 'user');

        return view('admin.bon_de_commande.print', ['bon_de_commande' => $bon_de_commande]);
    }

    /**
     * Activity history
     */
    public function history()
    {
        $history = ActivityLog::where('user_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin.bon_de_commande.history', compact('history'));
    }

    /**
     * Add stock to Library (accepts positive or negative quantity)
     */
    private function addStockToLibrary(Book $book, int $quantity): void
    {
        if ($quantity === 0) {
            return;
        }

        $librarieZone = Zone::where('type', 'librairie')->first();

        if (! $librarieZone) {
            throw new \Exception('Zone Librairie non trouvée');
        }

        $zoneId = $book->zone_id ?? $librarieZone->id;

        $stock = Stock::firstOrCreate(
            [
                'book_id'          => $book->id,
                'zone_id'          => $zoneId,
                'sous_zone_id'     => null,
                'sous_sous_zone_id'=> null,
            ],
            ['quantity' => 0]
        );

        $stock->quantity += $quantity;
        $stock->save();
    }
}
