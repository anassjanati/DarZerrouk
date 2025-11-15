@extends('layouts.admin')
@section('title', 'Historique des actions Bons de Commande')
@section('content')
<div class="container mx-auto px-6 py-4">
    <h2 class="text-xl font-bold mb-4">Historique Bon de Commande</h2>
    <table class="w-full border text-sm mb-6">
        <thead>
            <tr>
                <th>Date action</th>
                <th>User</th>
                <th>Description</th>
                <th>BC ref</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->causer->name ?? '' }}</td>
                <td>{{ $item->description }}</td>
                <td>
                    @if($item->properties['command_id'] ?? null)
                    <a href="{{ route('admin.bon_de_commande.show', $item->properties['command_id']) }}" class="text-blue-600 underline">{{ $item->properties['command_id'] }}</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
