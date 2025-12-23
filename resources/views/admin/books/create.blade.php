@extends('layouts.admin')

@section('title', 'Ajouter un livre')

@section('content')
<h1 class="text-2xl font-bold mb-4">Ajouter un livre</h1>
<form method="POST" action="{{ route('admin.books.store') }}">
    @csrf

    <!-- Titre -->
    <div class="mb-4">
        <label class="block mb-2 font-bold">Titre</label>
        <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <!-- Auteur -->
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

    <!-- Catégorie -->
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

    <!-- Editeur -->
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

    <!-- Zone principale -->
    <div class="mb-4">
        <label class="block mb-2 font-bold">Zone principale</label>
        <select name="zone_id" id="zone_id" class="w-full border rounded px-3 py-2" required onchange="fetchSousZones(this.value)">
            <option value="">Sélectionnez...</option>
            @foreach($zones as $zone)
                <option value="{{ $zone->id }}" @if(old('zone_id') == $zone->id) selected @endif>
                    {{ $zone->name }}
                </option>
            @endforeach
        </select>
    </div>
    <!-- Sous-zone -->
    <div class="mb-4">
        <label class="block mb-2 font-bold">Sous-zone</label>
        <select name="sous_zone_id" id="sous_zone_id" class="w-full border rounded px-3 py-2" required onchange="fetchSousSousZones(this.value)">
            <option value="">Sélectionnez une zone d’abord...</option>
            <!-- options filled by JS -->
        </select>
    </div>
    <!-- Sous-sous-zone (facultatif) -->
    <div class="mb-4">
        <label class="block mb-2 font-bold">Sous-sous-zone (facultatif)</label>
        <select name="sous_sous_zone_id" id="sous_sous_zone_id" class="w-full border rounded px-3 py-2">
            <option value="">Sélectionnez une sous-zone d’abord...</option>
            <!-- options filled by JS -->
        </select>
    </div>

    <!-- Prix d’achat -->
    <div class="mb-4">
        <label class="block mb-2 font-bold">Prix d’achat (Cost Price)</label>
        <input type="number" name="cost_price" step="0.01" value="{{ old('cost_price', '0.00') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <!-- Prix Normal -->
    <div class="mb-4">
        <label class="block mb-2 font-bold">Prix Normal</label>
        <input type="number" name="price_1" step="0.01" value="{{ old('price_1', '0.00') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <!-- Langue -->
    <div class="mb-4">
        <label class="block mb-2 font-bold">Langue</label>
        <input type="text" name="language" value="{{ old('language') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <!-- Stock initial -->
    <div class="mb-4">
        <label class="block mb-2 font-bold">Stock initial (Quantité de produit)</label>
        <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', '0') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <!-- Notes / Désignation -->
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

<script>
// Dynamic loading for sous_zone and sous_sous_zone
function fetchSousZones(zoneId) {
    fetch('/admin/zones/' + zoneId + '/sous-zones')
        .then(res => res.json())
        .then(data => {
            let select = document.getElementById('sous_zone_id');
            select.innerHTML = '<option value="">Sélectionnez...</option>';
            data.forEach(sz => {
                select.innerHTML += `<option value="${sz.id}">${sz.name}</option>`;
            });
            document.getElementById('sous_sous_zone_id').innerHTML = '<option value="">Sélectionnez une sous-zone d’abord...</option>';
        });
}
function fetchSousSousZones(sousZoneId) {
    fetch('/admin/sous-zones/' + sousZoneId + '/sous-sous-zones')
        .then(res => res.json())
        .then(data => {
            let select = document.getElementById('sous_sous_zone_id');
            select.innerHTML = '<option value="">Sélectionnez...</option>';
            data.forEach(ssz => {
                select.innerHTML += `<option value="${ssz.id}">${ssz.name}</option>`;
            });
        });
}
</script>
@endsection
