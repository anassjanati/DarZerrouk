@extends('layouts.admin')

@section('title', 'Ajouter un livre')

@section('content')
<h1 class="text-2xl font-bold mb-4">Ajouter un livre</h1>
<form method="POST" action="{{ route('admin.books.store') }}">
    @csrf

    <div class="mb-4">
        <label class="block mb-2 font-bold">Titre</label>
        <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block mb-2 font-bold">Auteur</label>
        <select name="author_id" class="w-full border rounded px-3 py-2">
            <option value="">Sélectionnez...</option>
            @foreach($authors as $author)
                <option value="{{ $author->id }}" @if(old('author_id') == $author->id) selected @endif>
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
                <option value="{{ $cat->id }}" @if(old('category_id') == $cat->id) selected @endif>
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
                <option value="{{ $pub->id }}" @if(old('publisher_id') == $pub->id) selected @endif>
                    {{ $pub->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label class="block mb-2 font-bold">Zone</label>
        <select name="zone_id" class="w-full border rounded px-3 py-2">
            <option value="">Sélectionnez...</option>
            @foreach($zones as $zone)
                <option value="{{ $zone->id }}" @if(old('zone_id') == $zone->id) selected @endif>
                    {{ $zone->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
    <label class="block mb-2 font-bold">Prix d’achat (Cost Price)</label>
    <input type="number" name="cost_price" step="0.01" value="{{ old('cost_price', '0.00') }}" class="w-full border rounded px-3 py-2" required>
</div>

   <div class="mb-4">
    <label class="block mb-2 font-bold">Prix Normal</label>
    <input type="number" name="price_1" step="0.01" value="{{ old('price_1', '0.00') }}" class="w-full border rounded px-3 py-2" required>
</div>

    <div class="mb-4">
    <label class="block mb-2 font-bold">Langue</label>
    <input type="text" name="language" value="{{ old('language') }}" class="w-full border rounded px-3 py-2" required>
</div>
<div class="mb-4">
    <label class="block mb-2 font-bold">Stock initial (Quantité de produit)</label>
    <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', '0') }}" class="w-full border rounded px-3 py-2" required>
</div>


    <div class="mb-4">
    <label class="block mb-2 font-bold">Notes / Désignation (optionnel)</label>
    <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2" placeholder="Commentaires ou désignation...">{{ old('notes') }}</textarea>
</div>


    <div>
        <button type="submit" class="px-4 py-2 bg-teal-700 text-white rounded hover:bg-teal-800 font-semibold">
            Enregistrer
        </button>
    </div>
</form>
@endsection
