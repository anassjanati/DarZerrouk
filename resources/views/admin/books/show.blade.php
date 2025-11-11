@extends('layouts.admin')

@section('title', "Détail du livre: $book->title")

@section('content')
<h1 class="text-2xl font-bold mb-4">Détail du livre</h1>
<table class="mb-8">
    <tr><th class="pr-3 text-right">Titre :</th><td>{{ $book->title }}</td></tr>
    <tr><th class="pr-3 text-right">Auteur :</th><td>{{ $book->author->name ?? '-' }}</td></tr>
    <tr><th class="pr-3 text-right">Catégorie :</th><td>{{ $book->category->name ?? '-' }}</td></tr>
    <tr><th class="pr-3 text-right">Zone :</th><td>{{ $book->zone->name ?? '-' }}</td></tr>
    <tr><th class="pr-3 text-right">Editeur :</th><td>{{ $book->publisher->name ?? '-' }}</td></tr>
    <!-- Repeat for other details you want displayed -->
</table>
<a href="{{ route('admin.books.edit', $book->id) }}"
   class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800 font-semibold mr-2">
    Modifier
</a>
<a href="{{ route('admin.books.list') }}"
   class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-800 font-semibold">
    Retour à la liste
</a>
@endsection
