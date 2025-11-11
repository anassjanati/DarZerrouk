@extends('layouts.admin')

@section('title', 'Magasinage & Stock')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
        @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
        @endforeach
    </div>
@endif

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Magasinage</h1>
        <p class="text-gray-600">Visualisez le stock par zone, gérez vos transferts et ajouts, et assurez l’approvisionnement.</p>
    </div>
    <div class="flex gap-3">
        <button onclick="openAddStockModal()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold">
            ➕ Ajouter du stock
        </button>
        <button onclick="openTransferModal()" class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 font-semibold">
            📦 Transférer du stock
        </button>
    </div>
</div>

<!-- Stock Table -->
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h2 class="text-xl font-bold mb-4">Stock actuel par zone</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2">Zone</th>
                <th class="px-4 py-2">Sous-zone</th>
                <th class="px-4 py-2">Sous-sous-zone</th>
                <th class="px-4 py-2">Livre</th>
                <th class="px-4 py-2">Quantité</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($stocks as $stock)
            <tr>
                <td class="px-4 py-2">{{ $stock->zone->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $stock->sousZone->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $stock->sousSousZone->name ?? '-' }}</td>
                <td class="px-4 py-2">{{ $stock->book->title ?? '-' }}</td>
                <td class="px-4 py-2">{{ $stock->quantity }}</td>
                <td class="px-4 py-2">
                    <button onclick="openTransferModal({{ $stock->book_id }}, {{ $stock->zone_id }}, {{ $stock->sous_zone_id ?? 'null' }}, {{ $stock->sous_sous_zone_id ?? 'null' }}, '{{ $stock->book->title }}')" class="text-blue-700 hover:text-blue-900">Transférer</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Stock Modal -->
<div id="addStockModal" class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg max-w-lg w-full mx-4 p-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Ajouter du stock</h2>
            <button onclick="closeAddStockModal()" class="text-gray-500 hover:text-gray-700 text-lg">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.stocks.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-bold">Livre</label>
                <select name="book_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Sélectionnez...</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Zone</label>
                <select name="zone_id" class="w-full border rounded px-3 py-2" required>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Sous-zone</label>
                <select name="sous_zone_id" class="w-full border rounded px-3 py-2">
                    <option value="">Aucune</option>
                    @foreach($sousZones as $sz)
                        <option value="{{ $sz->id }}">{{ $sz->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Sous-sous-zone</label>
                <select name="sous_sous_zone_id" class="w-full border rounded px-3 py-2">
                    <option value="">Aucune</option>
                    @foreach($sousSousZones as $ssz)
                        <option value="{{ $ssz->id }}">{{ $ssz->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Quantité</label>
                <input type="number" name="quantity" min="1" max="9999" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-green-700 text-white px-5 py-2 rounded hover:bg-green-800 font-semibold">Ajouter</button>
                <button type="button" onclick="closeAddStockModal()" class="bg-gray-200 text-gray-700 px-5 py-2 rounded hover:bg-gray-300 font-semibold">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Transfer Modal -->
<div id="transferModal" class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg max-w-lg w-full mx-4 p-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Transférer du stock</h2>
            <button onclick="closeTransferModal()" class="text-gray-500 hover:text-gray-700 text-lg">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.stock.transfer') }}">
            @csrf
            <input type="hidden" name="book_id" id="transferBookId">
            <input type="hidden" name="from_zone_id" id="transferFromZoneId">
            <input type="hidden" name="from_sous_zone_id" id="transferFromSousZoneId">
            <input type="hidden" name="from_sous_sous_zone_id" id="transferFromSousSousZoneId">

            <div class="mb-4">
                <label class="block mb-2 font-bold">Livre</label>
                <input type="text" id="transferBookTitle" class="w-full border rounded px-3 py-2" readonly>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Zone de destination</label>
                <select name="to_zone_id" class="w-full border rounded px-3 py-2" required>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Sous-zone destination</label>
                <select name="to_sous_zone_id" class="w-full border rounded px-3 py-2">
                    <option value="">Aucune</option>
                    @foreach($sousZones as $sz)
                        <option value="{{ $sz->id }}">{{ $sz->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Sous-sous-zone destination</label>
                <select name="to_sous_sous_zone_id" class="w-full border rounded px-3 py-2">
                    <option value="">Aucune</option>
                    @foreach($sousSousZones as $ssz)
                        <option value="{{ $ssz->id }}">{{ $ssz->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Quantité à transférer</label>
                <input type="number" name="quantity" min="1" max="9999" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-teal-700 text-white px-5 py-2 rounded hover:bg-teal-800 font-semibold">Valider le transfert</button>
                <button type="button" onclick="closeTransferModal()" class="bg-gray-200 text-gray-700 px-5 py-2 rounded hover:bg-gray-300 font-semibold">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddStockModal() {
    document.getElementById('addStockModal').classList.remove('hidden');
}
function closeAddStockModal() {
    document.getElementById('addStockModal').classList.add('hidden');
}
function openTransferModal(bookId=null, zoneId=null, sousZoneId=null, sousSousZoneId=null, bookTitle='') {
    document.getElementById('transferModal').classList.remove('hidden');
    document.getElementById('transferBookId').value = bookId ?? '';
    document.getElementById('transferFromZoneId').value = zoneId ?? '';
    document.getElementById('transferFromSousZoneId').value = sousZoneId ?? '';
    document.getElementById('transferFromSousSousZoneId').value = sousSousZoneId ?? '';
    document.getElementById('transferBookTitle').value = bookTitle ?? '';
}
function closeTransferModal() {
    document.getElementById('transferModal').classList.add('hidden');
}
</script>

@endsection
