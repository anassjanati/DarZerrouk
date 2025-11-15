<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Commande Nº {{ $bon_de_commande->ref }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin:30px;}
        h2 { font-size:24px; font-weight:700; color:#666;}
        table { width:100%; border-collapse:collapse; margin-top:18px;}
        th, td { border:1px solid #bbb; padding:8px; font-size:14px;}
        th { background:#f4f4f4;}
        .headerlogo { height:64px; }
        .company, .demandeur { margin-bottom:14px; }
        .total-table td { font-weight:600; }
        .infos-table th, .infos-table td { font-size:13px;}
        .notes {font-size: 13px; margin-top:18px;}
    </style>
</head>
<body onload="window.print()">
    <!-- Logo and document title -->
    <table style="width:100%; border:none; margin-bottom:24px;">
        <tr>
            <td style="width:80px;">
                {{-- If you have a logo URL, use it here --}}
                <img src="{{ asset('images/dz.png') }}" alt="Logo" class="headerlogo">
            </td>
            <td style="text-align:right;">
                <span style="font-size:24px; font-weight:600;">BON DE COMMANDE</span>
            </td>
        </tr>
    </table>

    <div class="company">
        <b>{{ config('app.name') }}</b><br>
        {{-- Adapt company info from your config or user/supplier --}}
        {{ $settings->address ?? 'Adresse société' }}<br>
        Téléphone: {{ $settings->phone ?? '------' }}
    </div>

    <div class="demandeur">
        <b>À :</b> {{ $bon_de_commande->demandeur ?? $bon_de_commande->supplier->name ?? '' }}<br>
        {{ $bon_de_commande->supplier->address ?? '' }}
    </div>

    <table class="infos-table" style="margin-bottom:6px;">
        <tr>
            <th style="width:170px;">DATE DU BON DE COMMANDE</th>
            <th>DEMANDEUR</th>
            <th>EXPÉDIÉ PAR</th>
            <th>FRANCO DÉPART</th>
            <th>CONDITIONS</th>
        </tr>
        <tr>
            <td>{{ $bon_de_commande->date }}</td>
            <td>{{ $bon_de_commande->demandeur ?? $bon_de_commande->supplier->name ?? '' }}</td>
            <td>{{ $bon_de_commande->user->name ?? '' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>QTÉ</th>
                <th>UNITÉ</th>
                <th>DESCRIPTION</th>
                <th>PRIX UNITAIRE</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($bon_de_commande->lines as $line)
            @php $line_total = $line->quantity * $line->cost_price; $total += $line_total; @endphp
            <tr>
                <td>{{ $line->quantity }}</td>
                <td>Unité</td>
                <td>{{ $line->book->title }}</td>
                <td>{{ number_format($line->cost_price, 2) }}</td>
                <td>{{ number_format($line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width:310px; float:right; margin-top:10px;" class="total-table">
        <tr>
            <td>Sous-total</td>
            <td style="text-align:right;">{{ number_format($total,2) }}</td>
        </tr>
        <tr>
            <td>TVA</td>
            <td style="text-align:right;">{{ number_format($total*0.2,2) }}</td>
        </tr>
        <tr>
            <td>Port & manutention</td>
            <td style="text-align:right;">{{ number_format(0,2) }}</td>
        </tr>
        <tr>
            <td>Autre</td>
            <td style="text-align:right;">{{ number_format(0,2) }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">TOTAL</td>
            <td style="text-align:right; font-weight:bold;">{{ number_format($total*1.2,2) }}</td>
        </tr>
    </table>

    <div style="clear:both;"></div>

    <div class="notes">
        1. Veuillez envoyer deux copies de votre facture.<br>
        2. Entrez cette commande conformément aux tarifs, aux conditions, à la méthode de livraison et aux spécifications répertoires ci-dessus.<br>
        3. Veuillez nous informer immédiatement si vous n'êtes pas en mesure d'exécuter la commande telle que spécifiée.<br>
        4. Adressez toutes les correspondances à :<br>
        &nbsp;&nbsp;{{ $settings->company_contact ?? 'Nom société/adresse' }}
    </div>

    <div style="margin-top:30px;">
        <small>Généré le {{ now()->format('d/m/Y H:i') }}</small>
    </div>
</body>
</html>
