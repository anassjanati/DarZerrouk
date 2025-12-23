<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Zone;
use Illuminate\Http\Request;

class BookManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with([
            'author', 'category', 'publisher', 'translator', 'corrector',
            'stocks.zone', 'stocks.sousZone', 'stocks.sousSousZone'
        ]);

        if ($request->filled('barcode') && is_numeric($request->barcode)) {
            $query->where('barcode', $request->barcode);
        } elseif ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhereHas('author', fn($a) => $a->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('translator', fn($t) => $t->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('publisher', fn($p) => $p->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('corrector', fn($c) => $c->where('name', 'like', "%{$term}%"));
            });
        }

        // Sorting (optional)
        $sortBy = $request->get('sort', 'title');
        $sortDir = $request->get('dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $books      = $query->paginate(50);
        $categories = Category::orderBy('name')->get();
        $authors    = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $zones      = Zone::orderBy('name')->get();

        $data = compact('books', 'categories', 'authors', 'publishers', 'zones');

        // If the current route is the manager one, use the manager wrapper view
        if ($request->routeIs('manager.books.manage')) {
            return view('manager.books.manage', $data);
        }

        // Default: admin wrapper view
        return view('admin.books.manage', $data);
    }
    public function stockAlerts(Request $request)
{
    $books = Book::with(['author', 'category'])
        ->where('is_active', 1)
        ->whereRaw("
            COALESCE((SELECT SUM(quantity) FROM stocks WHERE stocks.book_id = books.id), 0)
                <= IFNULL(books.min_stock_level, books.reorder_level)
        ")
        ->paginate(50);

    // mêmes listes que dans index()
    $categories = Category::orderBy('name')->get();
    $authors    = Author::orderBy('name')->get();
    $publishers = Publisher::orderBy('name')->get();
    $zones      = Zone::orderBy('name')->get();

    return view('admin.books.stock-alerts', compact(
        'books',
        'categories',
        'authors',
        'publishers',
        'zones'
    ));
}

}
