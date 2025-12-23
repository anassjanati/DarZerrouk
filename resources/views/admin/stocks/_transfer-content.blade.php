<div class="max-w-7xl mx-auto" x-data="stockTransfer()">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        Transferts de Stock (Librairie ↔ Magasinage)
    </h1>

    {{-- Recherche Livre par Code-barres --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Code-barres</label>
                <input type="text"
                       x-ref="barcodeInput"
                       x-model.trim="barcode"
                       @keyup.enter.prevent="searchBook"
                       placeholder="Scanner ou saisir le code-barres"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <button
                @click="$refs.barcodeInput.blur(); searchBook()"
                class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Rechercher
            </button>
            <a href="{{ route('admin.stocks.history') }}"
               class="px-5 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                Historique
            </a>
        </div>
        <template x-if="error">
            <p class="text-red-600 mt-3" x-text="error"></p>
        </template>
        <template x-if="book">
            <div class="mt-4">
                <p class="text-gray-700">
                    <span class="font-semibold">Livre:</span>
                    <span x-text="book.title"></span>
                    <span class="text-sm text-gray-500">
                        (<span x-text="book.barcode"></span>)
                    </span>
                </p>
                <p class="text-gray-700">
                    <span class="font-semibold">Auteur:</span>
                    <span x-text="book.author"></span>
                </p>
            </div>
        </template>
    </div>

    {{-- Stocks actuels --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" x-show="book">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3">Librairie (Exposition)</h3>
            <p class="text-sm text-gray-600 mb-2">
                Total:
                <span class="font-semibold" x-text="totals.librairie"></span> unités
            </p>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-3 py-2">Emplacement</th>
                        <th class="text-left px-3 py-2">Quantité</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="row in librairieStock" :key="row.stock_id">
                        <tr class="border-b">
                            <td class="px-3 py-2" x-text="row.location"></td>
                            <td class="px-3 py-2" x-text="row.quantity"></td>
                        </tr>
                    </template>
                    <tr x-show="librairieStock.length === 0">
                        <td class="px-3 py-2 text-gray-500" colspan="2">
                            Aucun stock en Librairie
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-3">Magasinage (Réserve)</h3>
            <p class="text-sm text-gray-600 mb-2">
                Total:
                <span class="font-semibold" x-text="totals.magasinage"></span> unités
            </p>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-3 py-2">Emplacement</th>
                        <th class="text-left px-3 py-2">Quantité</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="row in magasinageStock" :key="row.stock_id">
                        <tr class="border-b">
                            <td class="px-3 py-2" x-text="row.location"></td>
                            <td class="px-3 py-2" x-text="row.quantity"></td>
                        </tr>
                    </template>
                    <tr x-show="magasinageStock.length === 0">
                        <td class="px-3 py-2 text-gray-500" colspan="2">
                            Aucun stock en Magasinage
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Transfert précis: Librairie -> Magasinage --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6" x-show="book">
        <h3 class="text-lg font-semibold mb-4">
            Transférer de la Librairie vers le Magasinage
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Source --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Source (Librairie)
                </label>
                <select x-model="formL2M.sourceKey" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">— Choisir l'emplacement source —</option>
                    <template x-for="(row, idx) in librairieStock" :key="'L2M-src-'+idx">
                        <option :value="idx"
                                x-text="row.location + ' — ' + row.quantity + ' unités'">
                        </option>
                    </template>
                </select>
            </div>

            {{-- Destination --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Destination (Magasinage) — Zone
                </label>
                <select x-model="formL2M.to_zone_id"
                        @change="loadSousZones('magasinage', 'L2M')"
                        class="w-full px-3 py-2 border rounded-lg">
                    <option value="">— Sélectionner Zone (M1..M5) —</option>
                    @foreach($magasinageZones as $z)
                        <option value="{{ $z->id }}">{{ $z->code }} — {{ $z->name }}</option>
                    @endforeach
                </select>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Sous-zone</label>
                        <select x-model="formL2M.to_sous_zone_id"
                                @change="loadSousSousZones('L2M')"
                                class="w-full px-3 py-2 border rounded-lg">
                            <option value="">— Optionnel —</option>
                            <template x-for="sz in optionsL2M.sousZones" :key="'L2M-sz-'+sz.id">
                                <option :value="sz.id" x-text="sz.code"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Sous-sous-zone</label>
                        <select x-model="formL2M.to_sous_sous_zone_id"
                                class="w-full px-3 py-2 border rounded-lg">
                            <option value="">— Optionnel —</option>
                            <template x-for="ssz in optionsL2M.sousSousZones" :key="'L2M-ssz-'+ssz.id">
                                <option :value="ssz.id" x-text="ssz.code"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                <input type="number" min="1" x-model.number="formL2M.quantity"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Notes (optionnel)
                </label>
                <input type="text" x-model="formL2M.notes"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
        </div>

        <div class="mt-4">
            <button @click="submitL2M"
                    class="px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                Transférer vers Magasinage
            </button>
        </div>
    </div>

    {{-- Transfert précis: Magasinage -> Librairie --}}
    <div class="bg-white p-6 rounded-lg shadow" x-show="book">
        <h3 class="text-lg font-semibold mb-4">
            Transférer du Magasinage vers la Librairie
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Source --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Source (Magasinage)
                </label>
                <select x-model="formM2L.sourceKey" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">— Choisir l'emplacement source —</option>
                    <template x-for="(row, idx) in magasinageStock" :key="'M2L-src-'+idx">
                        <option :value="idx"
                                x-text="row.location + ' — ' + row.quantity + ' unités'">
                        </option>
                    </template>
                </select>
            </div>

            {{-- Destination --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Destination (Librairie) — Zone
                </label>
                <select x-model="formM2L.to_zone_id"
                        @change="loadSousZones('librairie', 'M2L')"
                        class="w-full px-3 py-2 border rounded-lg">
                    <option value="">— Sélectionner Zone (ex: 35/2, 17/2...) —</option>
                    @foreach($librarieZones as $z)
                        <option value="{{ $z->id }}">{{ $z->code }} — {{ $z->name }}</option>
                    @endforeach
                </select>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Sous-zone</label>
                        <select x-model="formM2L.to_sous_zone_id"
                                @change="loadSousSousZones('M2L')"
                                class="w-full px-3 py-2 border rounded-lg">
                            <option value="">— Optionnel —</option>
                            <template x-for="sz in optionsM2L.sousZones" :key="'M2L-sz-'+sz.id">
                                <option :value="sz.id" x-text="sz.code"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Sous-sous-zone</label>
                        <select x-model="formM2L.to_sous_sous_zone_id"
                                class="w-full px-3 py-2 border rounded-lg">
                            <option value="">— Optionnel —</option>
                            <template x-for="ssz in optionsM2L.sousSousZones" :key="'M2L-ssz-'+ssz.id">
                                <option :value="ssz.id" x-text="ssz.code"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                <input type="number" min="1" x-model.number="formM2L.quantity"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Notes (optionnel)
                </label>
                <input type="text" x-model="formM2L.notes"
                       class="w-full px-3 py-2 border rounded-lg">
            </div>
        </div>

        <div class="mt-4">
            <button @click="submitM2L"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Transférer vers Librairie
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function stockTransfer() {
    return {
        barcode: '',
        error: '',
        book: null,
        librairieStock: [],
        magasinageStock: [],
        totals: { librairie: 0, magasinage: 0 },

        optionsL2M: { sousZones: [], sousSousZones: [] },
        optionsM2L: { sousZones: [], sousSousZones: [] },

        formL2M: {
            sourceKey: '',
            to_zone_id: '',
            to_sous_zone_id: '',
            to_sous_sous_zone_id: '',
            quantity: '',
            notes: '',
        },
        formM2L: {
            sourceKey: '',
            to_zone_id: '',
            to_sous_zone_id: '',
            to_sous_sous_zone_id: '',
            quantity: '',
            notes: '',
        },
        init() {
            const params = new URLSearchParams(window.location.search);
            const bc = params.get('barcode');
            if (bc) {
                this.barcode = bc;
                this.searchBook();
            }
        },

        async searchBook() {
            this.error = '';
            this.book = null;
            this.librairieStock = [];
            this.magasinageStock = [];
            this.totals = { librairie: 0, magasinage: 0 };

            if (!this.barcode) {
                this.error = 'Veuillez saisir un code-barres.';
                return;
            }

            try {
                const url = '{{ route('admin.stocks.search-book') }}'
                    + '?barcode=' + encodeURIComponent(this.barcode);
                const res = await fetch(url);
                const data = await res.json();

                if (!res.ok || data.error) {
                    this.error = data.error || 'Livre introuvable';
                    return;
                }

                console.log('SEARCH DATA', data);

                this.book = data.book;
                this.librairieStock = data.librairie_stock || [];
                this.magasinageStock = data.magasinage_stock || [];
                this.totals = {
                    librairie: data.total_librairie || 0,
                    magasinage: data.total_magasinage || 0,
                };
            } catch (e) {
                console.error(e);
                this.error = 'Erreur lors de la recherche.';
            }
        },

        async loadSousZones(type, formKey) {
            let zoneId = (formKey === 'L2M')
                ? this.formL2M.to_zone_id
                : this.formM2L.to_zone_id;

            if (!zoneId) {
                if (formKey === 'L2M') {
                    this.optionsL2M.sousZones = [];
                    this.optionsL2M.sousSousZones = [];
                    this.formL2M.to_sous_zone_id = '';
                    this.formL2M.to_sous_sous_zone_id = '';
                } else {
                    this.optionsM2L.sousZones = [];
                    this.optionsM2L.sousSousZones = [];
                    this.formM2L.to_sous_zone_id = '';
                    this.formM2L.to_sous_sous_zone_id = '';
                }
                return;
            }

            try {
                const res = await fetch(
                    '{{ route('admin.stocks.sous-zones') }}?zone_id=' + zoneId
                );
                const data = await res.json();

                if (formKey === 'L2M') {
                    this.optionsL2M.sousZones = data;
                    this.optionsL2M.sousSousZones = [];
                    this.formL2M.to_sous_zone_id = '';
                    this.formL2M.to_sous_sous_zone_id = '';
                } else {
                    this.optionsM2L.sousZones = data;
                    this.optionsM2L.sousSousZones = [];
                    this.formM2L.to_sous_zone_id = '';
                    this.formM2L.to_sous_sous_zone_id = '';
                }
            } catch (e) {
                console.error(e);
            }
        },

        async loadSousSousZones(formKey) {
            let sousZoneId = (formKey === 'L2M')
                ? this.formL2M.to_sous_zone_id
                : this.formM2L.to_sous_zone_id;

            if (!sousZoneId) {
                if (formKey === 'L2M') {
                    this.optionsL2M.sousSousZones = [];
                    this.formL2M.to_sous_sous_zone_id = '';
                } else {
                    this.optionsM2L.sousSousZones = [];
                    this.formM2L.to_sous_sous_zone_id = '';
                }
                return;
            }

            try {
                const res = await fetch(
                    '{{ route('admin.stocks.sous-sous-zones') }}?sous_zone_id=' + sousZoneId
                );
                const data = await res.json();

                if (formKey === 'L2M') {
                    this.optionsL2M.sousSousZones = data;
                } else {
                    this.optionsM2L.sousSousZones = data;
                }
            } catch (e) {
                console.error(e);
            }
        },

        async submitL2M() {
            if (!this.book) return;
            if (this.formL2M.sourceKey === '') {
                alert('Choisissez la source (Librairie).');
                return;
            }
            const src = this.librairieStock[this.formL2M.sourceKey];
            if (!src) { alert('Source invalide.'); return; }
            if (!this.formL2M.to_zone_id) {
                alert('Choisissez la zone de destination (Magasinage).');
                return;
            }
            if (!this.formL2M.quantity || this.formL2M.quantity < 1) {
                alert('Quantité invalide.');
                return;
            }
            if (parseInt(this.formL2M.quantity) > parseInt(src.quantity)) {
                alert('Quantité supérieure au stock disponible.');
                return;
            }

            const payload = {
                book_id: this.book.id,
                from_zone_id: src.zone_id,
                from_sous_zone_id: src.sous_zone_id || '',
                from_sous_sous_zone_id: src.sous_sous_zone_id || '',
                to_zone_id: this.formL2M.to_zone_id,
                to_sous_zone_id: this.formL2M.to_sous_zone_id || '',
                to_sous_sous_zone_id: this.formL2M.to_sous_sous_zone_id || '',
                quantity: this.formL2M.quantity,
                notes: this.formL2M.notes || '',
            };

            await this.postTransfer(payload);
        },

        async submitM2L() {
            if (!this.book) return;
            if (this.formM2L.sourceKey === '') {
                alert('Choisissez la source (Magasinage).');
                return;
            }
            const src = this.magasinageStock[this.formM2L.sourceKey];
            if (!src) { alert('Source invalide.'); return; }
            if (!this.formM2L.to_zone_id) {
                alert('Choisissez la zone de destination (Librairie).');
                return;
            }
            if (!this.formM2L.quantity || this.formM2L.quantity < 1) {
                alert('Quantité invalide.');
                return;
            }
            if (parseInt(this.formM2L.quantity) > parseInt(src.quantity)) {
                alert('Quantité supérieure au stock disponible.');
                return;
            }

            const payload = {
                book_id: this.book.id,
                from_zone_id: src.zone_id,
                from_sous_zone_id: src.sous_zone_id || '',
                from_sous_sous_zone_id: src.sous_sous_zone_id || '',
                to_zone_id: this.formM2L.to_zone_id,
                to_sous_zone_id: this.formM2L.to_sous_zone_id || '',
                to_sous_sous_zone_id: this.formM2L.to_sous_sous_zone_id || '',
                quantity: this.formM2L.quantity,
                notes: this.formM2L.notes || '',
            };

            await this.postTransfer(payload);
        },

        async postTransfer(payload) {
            try {
                const res = await fetch('{{ route('admin.stocks.transfer.post') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!res.ok || data.error) {
                    alert(data.error || 'Erreur lors du transfert.');
                    return;
                }

                alert(data.message || 'Transfert effectué.');

                // Refresh book data
                await this.searchBook();

                // Reset forms
                this.formL2M = {
                    sourceKey: '',
                    to_zone_id: '',
                    to_sous_zone_id: '',
                    to_sous_sous_zone_id: '',
                    quantity: '',
                    notes: '',
                };
                this.formM2L = {
                    sourceKey: '',
                    to_zone_id: '',
                    to_sous_zone_id: '',
                    to_sous_sous_zone_id: '',
                    quantity: '',
                    notes: '',
                };
                this.optionsL2M = { sousZones: [], sousSousZones: [] };
                this.optionsM2L = { sousZones: [], sousSousZones: [] };
            } catch (e) {
                console.error(e);
                alert('Erreur réseau.');
            }
        },
    }
}
</script>
@endpush