@extends('layouts.admin')
@section('title', 'Historique des mouvements')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Historique des mouvements de stock</h1>

    <form method="GET" action="{{ route('admin.stocks.history') }}" class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Direction</label>
                <select name="direction" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">— Toutes —</option>
                    <option value="to_librairie" @selected(request('direction')==='to_librairie')>Vers Librairie</option>
                    <option value="to_magasinage" @selected(request('direction')==='to_magasinage')>Vers Magasinage</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code-barres</label>
                <input type="text" name="barcode" value="{{ request('barcode') }}" class="w-full px-3 py-2 border rounded-lg" placeholder="978...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
        </div>
        <div class="mt-4">
            <button class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filtrer</button>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-2">Date</th>
                    <th class="text-left px-4 py-2">Livre</th>
                    <th class="text-left px-4 py-2">De</th>
                    <th class="text-left px-4 py-2">Vers</th>
                    <th class="text-left px-4 py-2">Qté</th>
                    <th class="text-left px-4 py-2">Par</th>
                    <th class="text-left px-4 py-2">Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">
                            {{ $m->book?->display_title }}
                            <div class="text-xs text-gray-500">{{ $m->book?->barcode }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $m->from_location }}</td>
                        <td class="px-4 py-2">{{ $m->to_location }}</td>
                        <td class="px-4 py-2 font-semibold">{{ $m->quantity }}</td>
                        <td class="px-4 py-2">{{ $m->user?->name }}</td>
                        <td class="px-4 py-2">{{ $m->notes }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">Aucun mouvement trouvé</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3">
            {{ $movements->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
