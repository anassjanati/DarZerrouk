@extends('layouts.admin')
@section('title', 'Détails du livre')

@section('content')
<style>
.card {
    background: #fff;
    max-width: 650px;
    margin: 40px auto;
    padding: 32px 24px;
    border-radius: 10px;
    box-shadow: 0 2px 8px #eee;
}
.card h2 { font-size: 26px; margin-bottom: 10px; color: #2b4d90;}
.detail-row { display: flex; padding: 7px 0; border-bottom: 1px solid #f1f1f1; align-items: center;}
.detail-label { width:180px; color: #444; font-weight: 600;}
.detail-value { flex:1; color: #333; }
.zone-table { width: 100%; background: #fbfcfd; margin-top: 7px; border-radius:6px;}
.zone-table th, .zone-table td { padding:4px 7px; font-size:14px;}
.zone-table th { background: #f7faff; color:#356ab3; font-weight:500;}
.stock-section { margin-top: 18px; }
.section-title { font-weight:600; margin-bottom:5px;}
.back-btn {
    display: inline-block;
    margin-bottom: 28px;
    background: #eeedf3;
    color: #1a2e4d;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    padding: 7px 18px;
    cursor: pointer;
    font-size: 15px;
    text-decoration: none;
    transition: background 0.2s;
}
.back-btn:hover {
    background: #d3e2f4;
}
</style>

<div class="card">
    <a href="{{ route('admin.books.manage') }}" class="back-btn">&larr; Retour à la liste</a>
    <h2>{{ $book->title }}</h2>
    <div class="detail-row">
        <div class="detail-label">Code-barres</div>
        <div class="detail-value">{{ $book->barcode }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Désignation</div>
        <div class="detail-value">{{ $book->designation }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Catégorie</div>
        <div class="detail-value">{{ $book->category->name ?? '-' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Auteur</div>
        <div class="detail-value">{{ $book->author->name ?? '-' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Fournisseur</div>
        <div class="detail-value">{{ $book->publisher->name ?? '-' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Date de création</div>
        <div class="detail-value">{{ $book->created_at ? $book->created_at->format('d/m/Y H:i') : '-' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Prix normal</div>
        <div class="detail-value">{{ number_format($book->price_1, 2) }} DH</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Prix après remise</div>
        <div class="detail-value">{{ number_format($book->price_2, 2) }} DH</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Prix gros</div>
        <div class="detail-value">{{ number_format($book->wholesale_price, 2) }} DH</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Prix d'achat (BL)</div>
        <div class="detail-value">{{ number_format($book->cost_price, 2) }} DH</div>
    </div>
    <div class="stock-section">
        <div class="section-title">Stock en Librairie</div>
        <table class="zone-table">
            <thead>
                <tr>
                    <th>Zone</th>
                    <th>Sous-zone</th>
                    <th>Sous-sous-zone</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody>
            @foreach($book->stocks->where('location_type', 'librairie') as $stock)
                <tr>
                    <td>{{ $stock->zone->name ?? '-' }}</td>
                    <td>{{ $stock->sousZone->name ?? '-' }}</td>
                    <td>{{ $stock->sousSousZone->name ?? '-' }}</td>
                    <td>{{ $stock->quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="stock-section">
        <div class="section-title">Stock en Magasinage</div>
        <table class="zone-table">
            <thead>
                <tr>
                    <th>Zone</th>
                    <th>Sous-zone</th>
                    <th>Sous-sous-zone</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody>
            @foreach($book->stocks->where('location_type', 'magasinage') as $stock)
                <tr>
                    <td>{{ $stock->zone->name ?? '-' }}</td>
                    <td>{{ $stock->sousZone->name ?? '-' }}</td>
                    <td>{{ $stock->sousSousZone->name ?? '-' }}</td>
                    <td>{{ $stock->quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
