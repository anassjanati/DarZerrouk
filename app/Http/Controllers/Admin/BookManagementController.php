<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Zone;
use App\Models\Translator;
use App\Models\Editor;
use Illuminate\Http\Request;

class BookManagementController extends Controller
{
    /**
     * Main unified books management page
     */
    public function index(Request $request)
    {
        $query = Book::with(['category', 'author', 'publisher', 'zone', 'translator', 'editor']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by author
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Filter by publisher
        if ($request->filled('publisher_id')) {
            $query->where('publisher_id', $request->publisher_id);
        }

        // Filter by zone
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'out':
                    $query->outOfStock();
                    break;
                case 'low':
                    $query->lowStock();
                    break;
                case 'in':
                    $query->where('stock_quantity', '>', 0);
                    break;
            }
        }

        // Filter by price range
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->inPriceRange($request->min_price, $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'title');
        $sortDir = $request->get('dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $books = $query->paginate(50);

        // Get filter options
        $categories = Category::active()->orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();

        return view('admin.books.manage', compact(
            'books',
            'categories',
            'authors',
            'publishers',
            'zones'
        ));
    }

    public function stockAlerts(Request $request)
{
    $query = Book::with(['category', 'author', 'publisher', 'zone']);

    // Filter by stock status
    $stockStatus = $request->get('status', 'all');
    
    switch ($stockStatus) {
        case 'out':
            $query->outOfStock();
            break;
        case 'low':
            $query->lowStock();
            break;
        case 'all':
            // Show both low and out of stock
            $query->where(function($q) {
                $q->where('stock_quantity', '<=', 0)
                  ->orWhere(function($subq) {
                      $subq->where('stock_quantity', '>', 0)
                           ->where(function($innerq) {
                               $innerq->whereColumn('stock_quantity', '<=', 'min_stock_level')
                                      ->orWhereColumn('stock_quantity', '<=', 'reorder_level');
                           });
                  });
            });
            break;
    }

    // Search
    if ($request->filled('search')) {
        $query->search($request->search);
    }

    // Filter by category
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Sort
    $sortBy = $request->get('sort', 'stock_quantity');
    $sortDir = $request->get('dir', 'asc');
    $query->orderBy($sortBy, $sortDir);

    $books = $query->paginate(50);

    // Get filter options
    $categories = Category::active()->orderBy('name')->get();

    return view('admin.books.stock-alerts', compact('books', 'categories'));
}

    public function updateZone(Request $request, Book $book)
{
    $request->validate(['zone_id' => 'required|exists:zones,id']);
    $book->zone_id = $request->zone_id;
    $book->save();

    return response()->json([
        'success' => true,
        'zone_name' => $book->zone->name,
        'book_id' => $book->id,
    ]);
}
// Show import form
public function importForm()
{
    return view('admin.books.import');
}

// Handle import POST



    /**
     * Get book details (AJAX)
     */
    public function show(Book $book)
    {
        $book->load(['category', 'author', 'publisher', 'zone', 'translator', 'editor']);
        return response()->json($book);
    }
}
