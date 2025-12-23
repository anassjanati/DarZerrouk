<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    /**
     * Display POS interface
     */
    public function index()
    {
        $clients = Client::orderBy('name')->limit(200)->get();

        return view('pos.index', compact('clients'));
    }

    /**
     * Search books (for AJAX autocomplete)
     */
    public function searchBooks(Request $request)
    {
        $term = $request->get('term');

        $books = Book::query()
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                      ->orWhere('barcode', 'like', "%{$term}%");
            })
            ->with(['author', 'category'])
            ->limit(10)
            ->get()
            ->map(function ($book) {
                return [
                    'id'      => $book->id,
                    'title'   => $book->title,
                    'author'  => optional($book->author)->name ?? 'Inconnu',
                    'price'   => $book->retail_price,
                    'barcode' => $book->barcode,
                ];
            });

        return response()->json($books);
    }

    /**
     * Get book by barcode
     */
    public function getBook($code)
    {
        $book = Book::query()
            ->where('barcode', $code)
            ->with(['author', 'category'])
            ->first();

        if (! $book) {
            return response()->json(['error' => 'Livre non trouvé'], 404);
        }

        return response()->json([
            'id'      => $book->id,
            'title'   => $book->title,
            'author'  => optional($book->author)->name ?? 'Inconnu',
            'price'   => $book->retail_price,
            'barcode' => $book->barcode,
        ]);
    }
    public function receipt(Sale $sale)
{
    $sale->load(['items.book', 'client']);

    return view('pos.receipt', compact('sale'));
}

    /**
     * Process sale
     */
    public function processSale(Request $request)
    {
        $validated = $request->validate([
            'items'               => 'required|array',
            'items.*.book_id'     => 'required|exists:books,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
            'client_id'           => 'nullable|exists:clients,id',
            'payment_method'      => 'required|in:espece,tpe,virement,sans_reglement',
            'amount_paid'         => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });

            $taxRate     = 20; // TVA 20%
            $taxAmount   = $subtotal * ($taxRate / 100);
            $totalAmount = $subtotal + $taxAmount;
            $changeAmount = $validated['amount_paid'] - $totalAmount;

            // Create sale
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'user_id'        => auth()->id(),
                'sale_date'      => now(),
                'customer_id'      => $validated['client_id'] ?? null,
                'subtotal'       => $subtotal,
                'tax_percentage' => $taxRate,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $totalAmount,
                'paid_amount'    => $validated['amount_paid'],
                'change_amount'  => max(0, $changeAmount),
                'payment_status' => 'completed',
            ]);

            // Create sale items and update stock table
            foreach ($validated['items'] as $item) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'book_id'    => $item['book_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal'   => $item['quantity'] * $item['price'],
                ]);

                // Decrement stock in stocks table (adjust column name if needed)
                DB::table('stocks')
                    ->where('book_id', $item['book_id'])
                    ->decrement('quantity', $item['quantity']);
            }

            // Create payment record
            Payment::create([
                'sale_id'        => $sale->id,
                'payment_method' => $validated['payment_method'],
                'amount'         => $validated['amount_paid'],
            ]);

            DB::commit();

            return response()->json([
                'success'        => true,
                'invoice_number' => $sale->invoice_number,
                'total'          => $totalAmount,
                'change'         => max(0, $changeAmount),
                'sale_id'        => $sale->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Erreur lors de la vente: ' . $e->getMessage(),
            ], 500);
        }
    }
}
