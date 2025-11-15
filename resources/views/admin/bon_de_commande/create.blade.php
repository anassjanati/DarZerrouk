@extends('layouts.admin')
@section('title', 'Nouveau Bon de Commande')

@section('content')
<div class="container mx-auto px-6 py-4">
    <h2 class="text-xl font-bold mb-6">Créer un bon de commande</h2>
    <form method="POST" action="{{ route('admin.bon_de_commande.store') }}">
        @csrf
        <div class="mb-3">
            <label for="supplier_id" class="font-semibold">Fournisseur :</label>
            <select id="supplier_id" name="supplier_id" required class="border rounded px-3 py-2 w-full">
                <option value="">Choisissez...</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="date" class="font-semibold">Date :</label>
            <input type="date" id="date" name="date" required class="border rounded px-3 py-2 w-full" value="{{ date('Y-m-d') }}">
        </div>
        <div class="mb-3">
            <label for="comments" class="font-semibold">Commentaires :</label>
            <textarea name="comments" class="border rounded px-3 py-2 w-full"></textarea>
        </div>

        <h3 class="font-semibold mt-6 mb-2">Livres commandés :</h3>
        <table class="w-full border mb-3" id="lines-table">
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Quantité</th>
                    <th>Prix achat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="lines[0][book_id]" class="border rounded px-2 py-1 w-full" required>
                            <option value="">Choisissez...</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}">{{ $book->title }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="lines[0][quantity]" min="1" class="border rounded px-2 py-1 w-full" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="lines[0][cost_price]" min="0" class="border rounded px-2 py-1 w-full" required>
                    </td>
                    <td>
                        <button type="button" onclick="removeRow(this)" class="text-red-600 px-2">x</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="bg-gray-200 px-3 py-1 text-sm rounded" onclick="addRow()">Ajouter livre</button>
        <br><br>
        <button type="submit" class="bg-teal-700 text-white px-5 py-2 rounded font-bold">Valider bon de commande</button>
    </form>
</div>
<script>
let rowIdx = 1;
function addRow() {
    let table = document.getElementById('lines-table').getElementsByTagName('tbody')[0];
    let newRow = table.rows[0].cloneNode(true);
    Array.from(newRow.getElementsByTagName('input')).forEach(input => {
        let name = input.name.replace(/\d+/, rowIdx);
        input.name = name;
        if (input.type === 'number') input.value = '';
    });
    Array.from(newRow.getElementsByTagName('select')).forEach(select => {
        let name = select.name.replace(/\d+/, rowIdx);
        select.name = name;
        select.selectedIndex = 0;
    });
    table.appendChild(newRow);
    rowIdx++;
}
function removeRow(btn) {
    let row = btn.parentNode.parentNode;
    let table = row.parentNode;
    if (table.rows.length > 1) table.removeChild(row);
}
</script>
@endsection
