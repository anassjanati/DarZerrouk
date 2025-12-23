@extends('layouts.admin')

@section('title', 'Détails du livre')

@section('content')
<style>
.book-show-wrapper {
    max-width: 1024px;
    margin: 32px auto 40px;
    padding: 0 10px;
}
.book-card {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    padding: 26px 30px 28px;
}
.book-header {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
}
.book-main-title {
    font-size: 24px;
    font-weight: 700;
    color: #1f4b99;
    margin-bottom: 4px;
}
.book-subtitle {
    font-size: 14px;
    color: #6b7280;
}
.meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #f9fafb;
    font-size: 12px;
    color: #4b5563;
}
.meta-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #10b981;
}
.meta-label {
    font-weight: 600;
}
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    background: #eef2ff;
    color: #1f2a4d;
    font-weight: 600;
    border: none;
    border-radius: 999px;
    padding: 6px 14px;
    cursor: pointer;
    font-size: 13px;
    text-decoration: none;
    transition: background 0.2s, transform 0.1s;
}
.back-btn:hover {
    background: #dbeafe;
    transform: translateY(-1px);
}

/* Layout two-column for main info */
.details-layout {
    display: grid;
    grid-template-columns: minmax(0,1.4fr) minmax(0,1fr);
    column-gap: 24px;
    row-gap: 12px;
    margin-bottom: 18px;
}

/* Detail lists */
.detail-section-title {
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .06em;
}
.detail-grid {
    display: grid;
    grid-template-columns: 160px 1fr;
    row-gap: 6px;
    column-gap: 10px;
    font-size: 14px;
}
.detail-row {
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
}
.detail-label {
    color: #6b7280;
    font-weight: 600;
}
.detail-value {
    color: #111827;
}
.detail-value-muted {
    color: #6b7280;
}

/* Stock sections */
.section-block {
    margin-top: 22px;
}
.section-title-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 6px;
}
.section-title {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
}
.section-subtitle {
    font-size: 12px;
    color: #6b7280;
}
.zone-table-wrapper {
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    background: #f9fafb;
}
.zone-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.zone-table th,
.zone-table td {
    padding: 7px 10px;
    border-bottom: 1px solid #e5e7eb;
}
.zone-table th {
    background: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
    text-align: left;
}
.zone-table tbody tr:nth-child(even) {
    background: #f9fafb;
}
.zone-table tbody tr:hover {
    background: #eef2ff;
}
.text-center { text-align: center; }

@media (max-width: 900px) {
    .book-card { padding: 20px 18px 22px; }
    .details-layout {
        grid-template-columns: 1fr;
    }
}
</style>

@php
    $totalLib = $book->stocks->where('zone.type', 'librairie')->sum('quantity');
    $totalMag = $book->stocks->where('zone.type', 'magasinage')->sum('quantity');
    $totalAll = $totalLib + $totalMag;
@endphp

<div class="book-show-wrapper">
    <a href="{{ route('admin.books.manage') }}" class="back-btn">
        ← Retour à la liste
    </a>

    <div class="book-card">
        <div class="book-header">
            <div>
                <div class="book-main-title">
                    {{ $book->title_ar ?? $book->title }}
                </div>
                @if($book->title_ar && $book->title)
                    <div class="book-subtitle">
                        {{ $book->title }} @if($book->language) – {{ $book->language }} @endif
                    </div>
                @endif>
            </div>

            <div class="flex flex-col items-end gap-2">
                <span class="meta-pill">
                    <span class="meta-dot"></span>
                    <span class="meta-label">
                        {{ $book->is_active ? 'Actif' : 'Archivé' }}
                    </span>
                    <span>• Stock : {{ $totalAll }} ex.</span>
                </span>
                <span class="meta-pill">
                    <span class="meta-label">Code‑barres</span>
                    <span>{{ $book->barcode }}</span>
                </span>
            </div>
        </div>

        {{-- Deux colonnes : Infos générales / Tarification --}}
        <div class="details-layout">
            {{-- Colonne 1 : informations générales --}}
            <div>
                <div class="detail-section-title">Informations générales</div>
                <div class="detail-grid">
                    <div class="detail-row detail-label">Titre / désignation</div>
                    <div class="detail-row detail-value">
                        {{ $book->title }}
                        @if($book->language)
                            <span class="detail-value-muted">– {{ $book->language }}</span>
                        @endif
                    </div>

                    <div class="detail-row detail-label">Catégorie</div>
                    <div class="detail-row detail-value">{{ $book->category->name ?? '-' }}</div>

                    <div class="detail-row detail-label">Auteur</div>
                    <div class="detail-row detail-value">{{ $book->author->name ?? '-' }}</div>

                    <div class="detail-row detail-label">Maison d’édition</div>
                    <div class="detail-row detail-value">{{ $book->publisher->name ?? '-' }}</div>

                    <div class="detail-row detail-label">Créé le</div>
                    <div class="detail-row detail-value">
                        {{ $book->created_at ? $book->created_at->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>
            </div>

            {{-- Colonne 2 : tarification --}}
            <div>
                <div class="detail-section-title">Tarification</div>
                <div class="detail-grid">
                    <div class="detail-row detail-label">Prix normal</div>
                    <div class="detail-row detail-value">
                        {{ number_format($book->retail_price, 2) }} DH
                    </div>

          {{--        <div class="detail-row detail-label">Remise</div>
                    <div class="detail-row detail-value">
                        {{ number_format($book->discount_percentage ?? 0, 0) }} %
                    </div>
--}}
                    <div class="detail-row detail-label">Prix après remise</div>
                    @php
                        $prixApresRemise = $book->retail_price * (1 - ($book->discount_percentage / 100));
                    @endphp
                    <div class="detail-row detail-value">
                        {{ number_format($prixApresRemise, 2) }} DH
                    </div>

                    <div class="detail-row detail-label">Prix gros</div>
                    <div class="detail-row detail-value">
                        {{ number_format($book->wholesale_price ?? 0, 2) }} DH
                    </div>

                    <div class="detail-row detail-label">Prix d’achat</div>
                    <div class="detail-row detail-value">
                        {{ number_format($book->cost_price, 2) }} DH
                    </div>
                </div>
            </div>
        </div>

        {{-- Stock en librairie --}}
        <div class="section-block">
            <div class="section-title-row">
                <div class="section-title">Stock en librairie</div>
                <div class="section-subtitle">Total : {{ $totalLib }} ex.</div>
            </div>
            <div class="zone-table-wrapper">
                <table class="zone-table">
                    <thead>
                    <tr>
                        <th>Zone</th>
                        <th>Sous‑zone</th>
                        <th>Sous‑sous‑zone</th>
                        <th class="text-center">Quantité</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($book->stocks->where('zone.type', 'librairie') as $stock)
                        <tr>
                            <td>{{ $stock->zone->name ?? '-' }}</td>
                            <td>{{ $stock->sousZone->name ?? '-' }}</td>
                            <td>{{ $stock->sousSousZone->name ?? '-' }}</td>
                            <td class="text-center">{{ $stock->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun stock en librairie.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Stock en magasinage --}}
        <div class="section-block">
            <div class="section-title-row">
                <div class="section-title">Stock en magasinage</div>
                <div class="section-subtitle">Total : {{ $totalMag }} ex.</div>
            </div>
            <div class="zone-table-wrapper">
                <table class="zone-table">
                    <thead>
                    <tr>
                        <th>Zone</th>
                        <th>Sous‑zone</th>
                        <th>Sous‑sous‑zone</th>
                        <th class="text-center">Quantité</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($book->stocks->where('zone.type', 'magasinage') as $stock)
                        <tr>
                            <td>{{ $stock->zone->name ?? '-' }}</td>
                            <td>{{ $stock->sousZone->name ?? '-' }}</td>
                            <td>{{ $stock->sousSousZone->name ?? '-' }}</td>
                            <td class="text-center">{{ $stock->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun stock en magasinage.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
