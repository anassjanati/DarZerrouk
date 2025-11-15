@extends('layouts.admin')
@section('title', "Modifier le livre")

@section('content')
<h1 class="text-2xl font-bold mb-4">Modifier le livre</h1>
@if(session('success'))
    <div style="background:#d6f5d6;color:#25690d;padding:7px 14px;border-radius:5px;">{{ session('success') }}</div>
@endif
<form method="POST" action="{{ route('admin.books.update', $book->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="block mb-2 font-bold">Code-barres</label>
        <input type="text" name="barcode" value="{{ old('barcode', $book->barcode) }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block mb-2 font-bold">Titre</label>
        <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block mb-2 font-bold">Auteur</label>
        <select name="author_id" class="w-full border rounded px-3 py-2">
            <option value="">Sélectionnez...</option>
            @foreach($authors as $author)
                <option value="{{ $author->id }}"
                 @if(old('author_id', $book->author_id) == $author->id) selected @endif>
                    {{ $author->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block mb-2 font-bold">Catégorie</label>
        <select name="category_id" class="w-full border rounded px-3 py-2">
            <option value="">Sélectionnez...</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                 @if(old('category_id', $book->category_id) == $cat->id) selected @endif>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block mb-2 font-bold">Editeur</label>
        <select name="publisher_id" class="w-full border rounded px-3 py-2">
            <option value="">Sélectionnez...</option>
            @foreach($publishers as $pub)
                <option value="{{ $pub->id }}"
                 @if(old('publisher_id', $book->publisher_id) == $pub->id) selected @endif>
                    {{ $pub->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block mb-2 font-bold">Prix normal</label>
        <input type="number" step="0.01" name="price_1" value="{{ old('price_1', $book->price_1) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block mb-2 font-bold">Prix après remise</label>
        <input type="number" step="0.01" name="price_2" value="{{ old('price_2', $book->price_2) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block mb-2 font-bold">Prix gros</label>
        <input type="number" step="0.01" name="wholesale_price" value="{{ old('wholesale_price', $book->wholesale_price) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block mb-2 font-bold">Prix d'achat (BL)</label>
        <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $book->cost_price) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <button type="submit" class="px-4 py-2 bg-teal-700 text-white rounded hover:bg-teal-800 font-semibold">
            Enregistrer les modifications
        </button>
    </div>
</form>
@endsection
