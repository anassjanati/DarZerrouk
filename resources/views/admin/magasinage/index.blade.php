@extends('layouts.admin')
@section('title', 'Gestion du Stock (Magasinage)')

@section('content')
<style>
.stock-container {
    max-width: 1200px;
    margin: 30px auto;
    padding: 18px 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px #e4e9f0;
}
.section-title {
    font-size: 21px;
    font-weight: 700;
    margin: 16px 0 10px;
    color: #275088;
}
.form-inline { display:flex; gap:20px; margin-bottom:12px; align-items: flex-end;}
.form-inline > * { min-width:180px;}
.form-inline label { font-weight:600; margin-bottom:4px;}
input, select {
    padding:7px 8px;
    border-radius:5px;
    border:1px solid #b7c7e2;
    margin-bottom:4px;
}
.submit-btn {
    background: #24905d;
    color:#fff;
    font-weight:600;
    border-radius:7px;
    padding:8px 18px;
    border:none;
    cursor:pointer;
    margin-left:12px;
}
.stocks-table {
    width:100%; border-collapse:collapse; font-size:15px; margin-top:10px;
}
.stocks-table th, .stocks-table td { border-bottom:1px solid #f3f3f3; padding:8px 10px;}
.stocks-table th { background:#f4f7fa; font-weight:600;}
.stocks-table tr.zone-row {background:#f9fafb; font-weight:600; color:#2a4280;}
</style>

<div class="stock-container">
    <div class="section-title">Ajouter du stock au Magasinage</div>
    <form class="form-inline" method="POST" action="{{ route('admin.stocks.store') }}">
        @csrf
        <div>
            <label>Livre</label>
            <select name="book_id" required>
                <option value="">Choisissez...</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}">{{ $book->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Zone</label>
            <select name="zone_id" required>
                <option value="">Choisissez...</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Sous-zone</label>
            <select name="sous_zone_id">
                <option value="">---</option>
                @foreach($sousZones as $sz)
                    <option value="{{ $sz->id }}">{{ $sz->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Sous-sous-zone</label>
            <select name="sous_sous_zone_id">
                <option value="">---</option>
                @foreach($sousSousZones as $ssz)
                    <option value="{{ $ssz->id }}">{{ $ssz->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Quantité</label>
            <input type="number" name="quantity" min="1" required>
        </div>
        <button class="submit-btn">Ajouter</button>
    </form>

    <div class="section-title" style="margin-top:30px;">Transférer du stock vers la Librairie ou autre zone</div>
    <form class="form-inline" method="POST" action="{{ route('admin.stock.transfer') }}">
        @csrf
        <div>
            <label>Livre</label>
            <select name="book_id" required>
                <option value="">Choisissez...</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}">{{ $book->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Zone de départ</label>
            <select name="from_zone_id" required>
                <option value="">Choisissez...</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Zone d'arrivée</label>
            <select name="to_zone_id" required>
                <option value="">Choisissez...</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Quantité</label>
            <input type="number" name="quantity" min="1" required>
        </div>
        <button class="submit-btn">Transférer</button>
    </form>

    <div class="section-title">Etat du stock par zone</div>
    <table class="stocks-table">
        <thead>
            <tr>
                <th>Zone</th>
                <th>Livre</th>
                <th>Quantité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($zoneStocks as $zoneStock)
                <tr class="zone-row">
                    <td colspan="3">{{ $zoneStock['zone']->name }}</td>
                </tr>
                @foreach($zoneStock['books'] as $stock)
                    <tr>
                        <td></td>
                        <td>{{ $stock->book->title }}</td>
                        <td>{{ $stock->quantity }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@endsection
