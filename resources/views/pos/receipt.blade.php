<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
{{--    <title>Ticket #{{ $sale->invoice_number }}</title>--}}
    <style>
        * { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        body { margin: 0; padding: 8px; font-size: 11px; }
        .ticket { width: 260px; }
        .center { text-align: center; }
        .bold { font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        td { padding: 2px 0; }
        .totals td { padding-top: 3px; }
        .line { border-top: 1px dashed #999; margin: 4px 0; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
<div class="ticket">
    <div class="center bold">Dar Zerrouk</div>
    <div class="center">Ticket #{{ $sale->invoice_number }}</div>
    <div class="center">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
    <div class="center">Caissier : {{ $sale->user->name ?? '-' }}</div>

    @if($sale->client)
        <div class="line"></div>
        <div>Client : {{ $sale->client->name }}</div>
    @endif

    <div class="line"></div>

    <table>
        @foreach($sale->items as $item)
            <tr>
                <td colspan="2">
                    {{ $item->book->title ?? 'Article' }}
                </td>
            </tr>
            <tr>
                <td>{{ $item->quantity }} x {{ number_format($item->unit_price, 2) }} DH</td>
      {{--           <td style="text-align:right;">
                    {{ number_format($item->subtotal, 2) }} DH
                </td> --}}
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table class="totals">
 {{--        <tr>
            <td>Sous-total</td>
            <td style="text-align:right;">{{ number_format($sale->subtotal, 2) }} DH</td>
        </tr>
        <tr>
            <td>TVA {{ $sale->tax_percentage }}%</td>
            <td style="text-align:right;">{{ number_format($sale->tax_amount, 2) }} DH</td>
        </tr>--}}
        <tr class="bold">
            <td>Total TTC</td>
            <td style="text-align:right;">{{ number_format($sale->subtotal, 2) }} DH</td>
        </tr>
        <tr>
            <td>Payé</td>
            <td style="text-align:right;">{{ number_format($sale->paid_amount, 2) }} DH</td>
        </tr>
        <tr>
            <td>Monnaie</td>
            <td style="text-align:right;">{{ number_format($sale->change_amount, 2) }} DH</td>
        </tr>
    </table>

    <div class="line"></div>
    <div class="center">Merci pour votre achat</div>

    <button class="no-print" onclick="window.print()" style="margin-top:8px;">Imprimer</button>
</div>
</body>
</html>
