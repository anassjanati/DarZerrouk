@extends('layouts.admin')
@section('title', 'Livres archivés')

@section('content')
<h1 class="text-2xl font-bold mb-4">Livres archivés</h1>
@if(session('success'))
    <div style="background:#d6f5d6;color:#25690d;padding:7px 14px;border-radius:5px;">{{ session('success') }}</div>
@endif
<table class="books-table">
    <thead>
        <tr>
            <th>Code-barres</th>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Catégorie</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($books as $book)
        <tr>
            <td>{{ $book->barcode }}</td>
            <td>{{ $book->title }}</td>
            <td>{{ $book->author->name ?? '-' }}</td>
            <td>{{ $book->category->name ?? '-' }}</td>
            <td>
                <form action="{{ route('admin.books.unarchive', $book->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="padding:3px 10px;background:#28b465;color:#fff;border-radius:5px;font-size:14px;cursor:pointer;">
                        Désarchiver
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $books->links() }}
<a href="{{ route('admin.books.manage') }}" class="px-4 py-2 bg-teal-700 text-white rounded hover:bg-teal-800 font-semibold mt-6 inline-block">Retour à la liste principale</a>
@endsection
