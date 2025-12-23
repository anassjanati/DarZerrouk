<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Commande {{ $bon_de_commande->ref }}</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { font-size: 28px; margin-bottom: 5px; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .info-label { font-weight: bold; width: 150px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #f0f0f0; padding: 10px; text-align: left; font-weight: bold; border: 1px solid #ddd; }
        td { padding: 10px; border: 1px solid #ddd; }
        .total-section { margin-top: 30px; }
        .total-row { display: flex; justify-content: flex-end; margin-bottom: 10px; font-size: 14px; }
        .total-label { width: 200px; font-weight: bold; }
        .total-value { width: 100px; text-align: right; }
        .grand-total { font-size: 18px; font-weight: bold; border-top: 2px solid #333; padding-top: 10px; }
        .footer { margin-top: 40px; font-size: 12px; border-top: 1px solid #ddd; padding-top: 15px; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body onload="window.print()">
    @php
    $isAdmin = auth()->user() && auth()->user()->isAdmin();
@endphp
    <div class="container">
        <div class="header">
            <h1>BON DE COMMANDE</h1>
            <p>Ref: {{ $bon_de_commande->ref }} | Date: {{ $bon_de_commande->date }}</p>
        </div>

        <div class="info-row">
            <div>
                <div class="info-label">Fournisseur:</div>
                <p>{{ $bon_de_commande->supplier->name }}</p>
                <p>{{ $bon_de_commande->supplier->address ?? '' }}</p>
            </div>
            <div style="text-align: right;">
                <div class="info-label">Créé par:</div>
                <p>{{ $bon_de_commande->user->name }}</p>
                <p style="font-size: 12px;">{{ $bon_de_commande->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <p style="margin: 20px 0; color: #666;">
            <strong>Statut:</strong> 
            @if($bon_de_commande->status === 'pending')
                En attente de validation
            @else
                Validé
            @endif
        </p>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Livre</th>
                    <th style="width: 15%; text-align: center;">Quantité</th>
                    <th style="width: 15%; text-align: right;">Prix de Vente</th>
                    <th style="width: 20%; text-align: right;">Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bon_de_commande->lines as $line)
                <tr>
                    <td>
                        <strong>{{ $line->book->title }}</strong><br>
                        <span style="font-size: 12px; color: #666;">{{ $line->book->barcode }}</span>
                    </td>
                    <td style="text-align: center;">{{ $line->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($line->selling_price, 2) }} DH</td>
                    <td style="text-align: right;">{{ number_format($line->selling_price * $line->quantity, 2) }} DH</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Total Quantité:</div>
                <div class="total-value">{{ $bon_de_commande->lines->sum('quantity') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Total Prix de Vente:</div>
                <div class="total-value">{{ number_format($bon_de_commande->lines->sum(fn($l) => $l->selling_price * $l->quantity), 2) }} DH</div>
            </div>
            
            @if($bon_de_commande->status === 'validated' && $isAdmin)
            <div class="total-row" style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px;">
                <div class="total-label">Total Prix d'Achat:</div>
                <div class="total-value grand-total">{{ number_format($bon_de_commande->lines->sum(fn($l) => $l->cost_price * $l->quantity), 2) }} DH</div>
            </div>
            @endif
        </div>

        @if($bon_de_commande->comments)
        <div class="footer">
            <p><strong>Commentaires:</strong></p>
            <p>{{ $bon_de_commande->comments }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Généré le: {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>
