@extends('layouts.admin')
@section('title', 'Détail Bon de Commande')

@section('content')
<div class="container mx-auto px-6 py-4">
    <h2 class="text-xl font-bold mb-4">Bon de commande : {{ $bon_de_commande->ref }}</h2>
    <p><b>Fournisseur</b> : {{ $bon_de_commande->supplier->name ?? '' }} <br>
       <b>Date</b> : {{ $bon_de_commande->date }} <br>
       <b>Commentaires</b> : {{ $bon_de_commande->comments }}</p>

    <h3 class="font-semibold mt-6 mb-2">Livres commandés :</h3>
    <table class="w-full border text-sm mb-5">
        <thead>
            <tr>
                <th>Livre</th>
                <th>Quantité</th>
                <th>Prix achat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bon_de_commande->lines as $line)
            <tr>
                <td>{{ $line->book->title }}</td>
                <td>{{ $line->quantity }}</td>
                <td>{{ number_format($line->cost_price, 2) }} DH</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('admin.bon_de_commande.print', $bon_de_commande->id) }}" target="_blank" class="bg-teal-700 text-white px-4 py-2 rounded">Imprimer</a>
</div>
@endsection
