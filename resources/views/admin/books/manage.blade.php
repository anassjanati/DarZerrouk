@extends('layouts.admin')
@section('title', 'Gestion des livres')

@section('content')
<style>
.books-table-container {
    width: 100%;
    max-width: 1400px;
    margin: 30px auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px #eee;
    padding: 16px 20px;
}
.books-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
}
.books-table th, .books-table td {
    border-bottom: 1px solid #f2f2f2;
    padding: 8px 10px;
    text-align: left;
}
.books-table th {
    background: #f7f8fa;
    font-weight: 600;
}
.books-table tbody tr:hover {
    background: #f4faff;
}
.books-table tr.archived {
    background-color: #f6f6f6;
    color: #aaaaaa;
}
.books-table tr.archived td, .books-table tr.archived a,
.books-table tr.archived .aucun-stock {
    color: #aaaaaa !important;
    pointer-events: none;
    text-decoration: none;
}
.placements-table {
    width: 100%;
    border-radius: 4px;
    margin-top: 7px;
    margin-bottom: 2px;
    background: #fbfcfd;
    font-size: 14px;
}
.placements-table th, .placements-table td {
    border: none;
    padding: 3px 6px;
}
.placements-table th {
    color: #3e629a;
    font-weight: 500;
}
.aucun-stock {
    color: #db2e2e;
    font-size: 14px;
}
.action-btn {
    padding:3px 10px;
    border-radius:5px;
    font-size:14px;
    margin-right:6px;
    cursor:pointer;
    border:none;
    font-weight: 500;
}
.archiver-btn { background:#ffcd38; color:#514200; }
.unarchive-btn { background:#28b465; color:#fff; }
.delete-btn { background:#e33d2f; color:#fff;}
.edit-btn { background:#2b4d90; color:#fff; text-decoration:none; }
@media (max-width: 1024px) {
    .books-table-container {
        padding: 4px;
    }
    .books-table, .placements-table {
        font-size: 13px;
    }
}
</style>

<div class="books-table-container">
    <form method="GET" style="margin-bottom:18px;display:flex;gap:10px;align-items:center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Recherche générale..." style="padding:6px 10px;border-radius:5px;border:1px solid #c3d1e3;">
        <input type="text" name="barcode" value="{{ request('barcode') }}" placeholder="Code-barres exact" style="padding:6px 10px;border-radius:5px;border:1px solid #c3d1e3;">
        <button type="submit" style="padding:7px 14px;background:#2b4d90;color:#fff;font-weight:600;border-radius:5px;border:none;cursor:pointer;">Rechercher</button>
    </form>

    <table class="books-table">
        <thead>
            <tr>
                <th>Code barres</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Désignation</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Total Disponible</th>
                <th>Placements</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($books as $book)
            <tr @if(!$book->is_active) class="archived" @endif>
                <td>{{ $book->barcode }}</td>
                <td>
                    <a href="{{ route('admin.books.show', $book->id) }}" style="color: #2b4d90; text-decoration: underline; font-weight: 500;">
                        {{ $book->title }}
                    </a>
                </td>
                <td>{{ $book->author->name ?? '-' }}</td>
                <td>{{ $book->designation ?? '-' }}</td>
                <td>{{ $book->category->name ?? '-' }}</td>
                <td>{{ number_format($book->price_1, 2) }} DH</td>
                <td>{{ $book->stocks->sum('quantity') }}</td>
                <td>
                    @if($book->stocks->count())
                        <table class="placements-table">
                            <thead>
                                <tr>
                                    <th>Zone</th>
                                    <th>Sous-zone</th>
                                    <th>Sous-sous-zone</th>
                                    <th>Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($book->stocks as $stock)
                                <tr>
                                    <td>{{ $stock->zone->name ?? '-' }}</td>
                                    <td>{{ $stock->sousZone->name ?? '-' }}</td>
                                    <td>{{ $stock->sousSousZone->name ?? '-' }}</td>
                                    <td>{{ $stock->quantity }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <span class="aucun-stock">Aucun stock</span>
                    @endif
                </td>
                <td>
                    @if($book->is_active)
                        <a href="{{ route('admin.books.edit', $book->id) }}"
                           class="edit-btn action-btn">Éditer</a>
                        <form action="{{ route('admin.books.archive', $book->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="archiver-btn action-btn">
                                Archiver
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.books.unarchive', $book->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="unarchive-btn action-btn">
                                Désarchiver
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce livre ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn action-btn">Supprimer</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:16px;">
        {{ $books->links() }}
    </div>
</div>
@endsection
