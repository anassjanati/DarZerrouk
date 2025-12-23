<div class="max-w-7xl mx-auto">

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-xs text-gray-500">Unités en magasinage</p>
            <p class="text-2xl font-bold mt-1">{{ $totalUnits }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-xs text-gray-500">Titres en magasinage</p>
            <p class="text-2xl font-bold mt-1">{{ $totalTitles }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-xs text-gray-500">Titres sous niveau de réserve</p>
            <p class="text-2xl font-bold mt-1 text-amber-600">{{ $lowReserveCnt }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white p-4 rounded-lg shadow mb-4" x-data="{ q: '', zoneId: '' }">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" x-model="q"
                       placeholder="Titre ou code-barres"
                       class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                <select x-model="zoneId"
                        class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">Toutes les zones</option>
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}">{{ $z->code }} — {{ $z->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.stocks.transfer') }}"
                   class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 mt-6">
                    Aller à la page de transfert
                </a>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-xs" x-data="{ rows: @js(
                $stocks->map(fn($s) => [
                    'book_id'   => $s->book_id,
                    'title'     => $s->book?->display_title,
                    'barcode'   => $s->book?->barcode,
                    'zone_id'   => $s->zone_id,
                    'zone'      => $s->zone?->code,
                    'sous_zone' => $s->sousZone?->code,
                    'ss_zone'   => $s->sousSousZone?->code,
                    'qty'       => (int)$s->quantity,
                ])
            ) }">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-3 py-2">Livre</th>
                        <th class="text-left px-3 py-2">Code-barres</th>
                        <th class="text-left px-3 py-2">Emplacement</th>
                        <th class="text-right px-3 py-2">Qté</th>
                        <th class="text-right px-3 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="row in rows.filter(r =>
                        (!q || (r.title && r.title.toLowerCase().includes(q.toLowerCase())) || (r.barcode && r.barcode.includes(q))) &&
                        (!zoneId || r.zone_id == zoneId)
                    )" :key="row.book_id + '-' + row.zone + '-' + row.sous_zone + '-' + row.ss_zone">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-2" x-text="row.title"></td>
                            <td class="px-3 py-2" x-text="row.barcode"></td>
                            <td class="px-3 py-2">
                                <span x-text="row.zone"></span>
                                <template x-if="row.sous_zone">
                                    <span> / <span x-text="row.sous_zone"></span></span>
                                </template>
                                <template x-if="row.ss_zone">
                                    <span> / <span x-text="row.ss_zone"></span></span>
                                </template>
                            </td>
                            <td class="px-3 py-2 text-right font-semibold" x-text="row.qty"></td>
                            <td class="px-3 py-2 text-right">
                                <a :href="'{{ route('admin.stocks.transfer') }}?barcode=' + encodeURIComponent(row.barcode)"
                                   class="inline-flex items-center px-3 py-1 bg-emerald-600 text-white rounded text-xs hover:bg-emerald-700">
                                    Transférer
                                </a>
                                <a :href="'{{ route('admin.books.details', 0) }}'.replace('/0/', '/' + row.book_id + '/')"
                                   class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded text-xs hover:bg-gray-300 ml-1">
                                    Détails
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="rows.length === 0">
                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                            Aucun stock en magasinage.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>