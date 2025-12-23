@extends('layouts.admin')
@section('title', 'Modifier le livre')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <h1 style="font-size: 28px; font-weight: bold; margin-bottom: 20px;">Modifier le livre</h1>

    {{-- Messages de succès --}}
    @if(session('success'))
        <div style="background:#d6f5d6; color:#25690d; padding:12px 16px; border-radius:5px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Erreurs de validation --}}
    @if ($errors->any())
        <div style="background:#ffe5e5; color:#991b1b; padding:12px 16px; border-radius:5px; margin-bottom:20px;">
            <strong>Erreurs détectées :</strong>
            <ul style="margin-left:18px; list-style:disc; margin-top:8px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.books.update', $book->id) }}">
        @csrf
        @method('PATCH')

        {{-- Ligne 1: Code-barres + Titre FR --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Code-barres <span style="color: red;">*</span></label>
                <input type="text" name="barcode"
                       value="{{ old('barcode', $book->barcode) }}"
                       required
                       style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                @error('barcode')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Titre (FR/EN) <span style="color: red;">*</span></label>
                <input type="text" name="title"
                       value="{{ old('title', $book->title) }}"
                       required
                       style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                @error('title')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- Ligne 2: Titre AR --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px;">Titre (AR)</label>
            <input type="text" name="title_ar"
                   value="{{ old('title_ar', $book->title_ar) }}"
                   style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
            @error('title_ar')
                <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
            @enderror
        </div>

        {{-- Ligne 3: Auteur + Catégorie + Éditeur --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Auteur</label>
                <select name="author_id" style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                    <option value="">-- Sélectionner --</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}"
                            @if(old('author_id', $book->author_id) == $author->id) selected @endif>
                            {{ $author->name }}
                        </option>
                    @endforeach
                </select>
                @error('author_id')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Catégorie</label>
                <select name="category_id" style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                    <option value="">-- Sélectionner --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            @if(old('category_id', $book->category_id) == $cat->id) selected @endif>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Éditeur</label>
                <select name="publisher_id" style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                    <option value="">-- Sélectionner --</option>
                    @foreach($publishers as $pub)
                        <option value="{{ $pub->id }}"
                            @if(old('publisher_id', $book->publisher_id) == $pub->id) selected @endif>
                            {{ $pub->name }}
                        </option>
                    @endforeach
                </select>
                @error('publisher_id')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- Ligne 4: Description --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px;">Description</label>
            <textarea name="description" rows="4"
                      style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">{{ old('description', $book->description) }}</textarea>
            @error('description')
                <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
            @enderror
        </div>

        {{-- Ligne 5: Infos techniques --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Pages</label>
                <input type="number" name="pages"
                       value="{{ old('pages', $book->pages) }}"
                       style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                @error('pages')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Format</label>
                <select name="format" style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                    <option value="">-- Sélectionner --</option>
                    <option value="hardcover" @if(old('format', $book->format) == 'hardcover') selected @endif>Hardcover</option>
                    <option value="paperback" @if(old('format', $book->format) == 'paperback') selected @endif>Paperback</option>
                    <option value="ebook" @if(old('format', $book->format) == 'ebook') selected @endif>Ebook</option>
                </select>
                @error('format')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Année Pub.</label>
                <input type="number" name="publication_year"
                       value="{{ old('publication_year', $book->publication_year) }}"
                       style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                @error('publication_year')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Édition</label>
                <input type="text" name="edition_number"
                       value="{{ old('edition_number', $book->edition_number) }}"
                       style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                @error('edition_number')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- Ligne 6: Condition + Prix --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Condition</label>
                <select name="book_condition" style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                    <option value="">-- Sélectionner --</option>
                    <option value="new" @if(old('book_condition', $book->book_condition) == 'new') selected @endif>Neuf</option>
                    <option value="used_like_new" @if(old('book_condition', $book->book_condition) == 'used_like_new') selected @endif>Quasi neuf</option>
                    <option value="used_good" @if(old('book_condition', $book->book_condition) == 'used_good') selected @endif>Bon état</option>
                </select>
                @error('book_condition')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>

            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Prix de vente (DH) <span style="color: red;">*</span></label>
                <input type="number" step="0.01" name="retail_price"
                       value="{{ old('retail_price', $book->retail_price) }}"
                       required
                       style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                @error('retail_price')
                    <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                @enderror
            </div>
        </div>

        {{-- Champs Admin-only --}}
        @if(auth()->user()->hasRole('admin'))
            <hr style="margin: 30px 0;">
            <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #666;">🔒 Paramètres Admin</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">Prix de gros (DH)</label>
                    <input type="number" step="0.01" name="wholesale_price"
                           value="{{ old('wholesale_price', $book->wholesale_price) }}"
                           style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                    @error('wholesale_price')
                        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">Prix coût (DH)</label>
                    <input type="number" step="0.01" name="cost_price"
                           value="{{ old('cost_price', $book->cost_price) }}"
                           style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                    @error('cost_price')
                        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">% Remise</label>
                    <input type="number" step="0.01" name="discount_percentage"
                           value="{{ old('discount_percentage', $book->discount_percentage) }}"
                           style="width: 100%; border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 14px;">
                    @error('discount_percentage')
                        <small style="color: red; display: block; margin-top: 5px;">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        @endif

        {{-- Boutons --}}
        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit"
                    style="background:#1f4b99; color:#fff; padding:10px 20px; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">
                Enregistrer les modifications
            </button>
            <a href="{{ route('admin.books.manage') }}"
               style="background:#6b7280; color:#fff; padding:10px 20px; border-radius:5px; text-decoration:none;">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
