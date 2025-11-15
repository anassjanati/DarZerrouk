<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
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
            $query->where(function($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhereHas('author', fn($a) => $a->where('name', 'like', "%{$term}%"))
                  ->orWhereHas('translator', fn($t) => $t->where('name', 'like', "%{$term}%"))
                  ->orWhereHas('publisher', fn($p) => $p->where('name', 'like', "%{$term}%"))
                  ->orWhereHas('corrector', fn($c) => $c->where('name', 'like', "%{$term}%"));
            });
        }

        $books = $query->paginate(50);
        return view('admin.books.index', compact('books'));
    }

    public function show(Book $book)
    {
        $book->load([
            'author', 'category', 'publisher', 'stocks.zone', 'stocks.sousZone', 'stocks.sousSousZone'
        ]);
        return view('admin.books.show', compact('book'));
    }

    public function archive(Book $book)
{
    $book->is_active = false;
    $book->save();
    return redirect()->route('admin.books.archiveList')->with('success', 'Livre archivé.');
}

public function unarchive(Book $book)
{
    $book->is_active = true;
    $book->save();
    return redirect()->route('admin.books.manage')->with('success', 'Livre désarchivé et activé.');
}

// Archive listing page
public function archiveList(Request $request)
{
    $books = Book::where('is_active', false)->with([
        'author', 'category', 'publisher'
    ])->paginate(50);
    return view('admin.books.archive', compact('books'));
}


    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->back()->with('success', 'Livre supprimé.');
    }

    public function edit(Book $book)
    {
        $book->load(['author', 'category', 'publisher']);
        $authors    = \App\Models\Author::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $publishers = \App\Models\Publisher::orderBy('name')->get();
        return view('admin.books.edit', compact('book', 'authors', 'categories', 'publishers'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'barcode'         => 'required|max:255|unique:books,barcode,'.$book->id,
            'title'           => 'required|max:255',
            'author_id'       => 'nullable|exists:authors,id',
            'category_id'     => 'nullable|exists:categories,id',
            'publisher_id'    => 'nullable|exists:publishers,id',
            'price_1'         => 'nullable|numeric',
            'price_2'         => 'nullable|numeric',
            'wholesale_price' => 'nullable|numeric',
            'cost_price'      => 'nullable|numeric',
        ]);

        $book->update($request->only([
            'barcode', 'title', 'author_id', 'category_id', 'publisher_id',
            'price_1', 'price_2', 'wholesale_price', 'cost_price'
        ]));

        return redirect()->route('admin.books.edit', $book->id)
            ->with('success', 'Livre mis à jour avec succès.');
    }
}
