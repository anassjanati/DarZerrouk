<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::withoutGlobalScope('active')
            ->with([
                'author', 'category', 'publisher',
                'stocks.zone', 'stocks.sousZone', 'stocks.sousSousZone',
            ]);

        if ($request->filled('barcode')) {
            $barcode = trim($request->barcode);
            $query->where('barcode', 'like', '%' . $barcode . '%');
        } elseif ($request->filled('search')) {
            $term = trim($request->search);

            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('title_ar', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%")
                    ->orWhereHas('author', function ($a) use ($term) {
                        $a->where('name', 'like', "%{$term}%");
                    })
                    ->orWhereHas('publisher', function ($p) use ($term) {
                        $p->where('name', 'like', "%{$term}%");
                    });
            });
        }

        $books = $query
            ->orderBy('title')
            ->paginate(50)
            ->withQueryString();

        return view('admin.books.index', compact('books'));
    }

    public function show(Book $book)
    {
        $book->load([
            'author', 'category', 'publisher',
            'stocks.zone', 'stocks.sousZone', 'stocks.sousSousZone',
        ]);

        return view('admin.books.show', compact('book'));
    }

    public function archive(Book $book)
    {
        $book->is_active = false;
        $book->save();

        return redirect()
            ->route('admin.books.archiveList')
            ->with('success', 'Livre archivé.');
    }

    public function unarchive(Book $book)
    {
        $book->is_active = true;
        $book->save();

        return redirect()
            ->route('admin.books.manage')
            ->with('success', 'Livre désarchivé et activé.');
    }

    public function archiveList(Request $request)
    {
        $books = Book::withoutGlobalScope('active')
            ->where('is_active', false)
            ->with(['author', 'category', 'publisher'])
            ->orderBy('title')
            ->paginate(50)
            ->withQueryString();

        return view('admin.books.archive', compact('books'));
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->back()
            ->with('success', 'Livre supprimé.');
    }

    /**
     * Formulaire d’édition (contrôlé par permissions module "books").
     */
    public function edit(Book $book)
    {
        $user = auth()->user();

        \Log::info('books.edit check', [
            'user_id' => $user?->id,
            'can'     => $user?->canModule('books', 'edit'),
        ]);

        if (! $user || ! $user->canModule('books', 'edit')) {
            abort(403);
        }

        $book->load(['author', 'category', 'publisher']);

        $authors    = Author::orderBy('name')->get(['id', 'name']);
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $publishers = Publisher::orderBy('name')->get(['id', 'name']);

        return view('admin.books.edit', compact('book', 'authors', 'categories', 'publishers'));
    }

    /**
     * Mise à jour du livre (formulaire backoffice).
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $user = auth()->user();

        if (! $user || ! $user->canModule('books', 'edit')) {
            abort(403);
        }

        $validated = $request->validated();

        \Log::info('Book Update Validated Data (form):', $validated);

        $book->update($validated);

        return redirect()
            ->route('admin.books.manage')
            ->with('success', 'Livre mis à jour avec succès.');
    }
}
