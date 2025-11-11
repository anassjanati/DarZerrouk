<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Zone;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Show create form
    public function create()
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();

        return view('admin.books.create', compact(
            'authors', 'categories', 'publishers', 'zones'
        ));
    }

    // Store a new book
    public function store(Request $request)
    {
        $book = Book::create($this->validateData($request));

        return redirect()->route('admin.books.show', $book->id)
            ->with('success', 'Livre ajouté avec succès!');
    }

    // Display book details
    public function show(Book $book)
    {
        $book->load(['author', 'category', 'publisher', 'zone']);
        return view('admin.books.show', compact('book'));
    }

    // Show edit form
    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();

        return view('admin.books.edit', compact(
            'book', 'authors', 'categories', 'publishers', 'zones'
        ));
    }

    // Save (update) edited book
    public function update(Request $request, Book $book)
    {
        $book->update($this->validateData($request));

        return redirect()->route('admin.books.show', $book->id)
            ->with('success', 'Livre mis à jour avec succès!');
    }

    public function importStore(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt'
    ]);
    // use Laravel Excel or native parse
    // ...
    // flash success/error
    return redirect()->route('admin.books.manage')->with('success', 'Livres importés avec succès!');
}

    // Validate data fields for store and update
    protected function validateData(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'nullable|exists:authors,id',
            'category_id' => 'nullable|exists:categories,id',
            'publisher_id' => 'nullable|exists:publishers,id',
            'zone_id' => 'nullable|exists:zones,id',
            'cost_price' => 'required|numeric',
            'price_1' => 'required|numeric',
            'language' => 'required|string|max:20',
            'stock_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
            
        ]);
    }
}
