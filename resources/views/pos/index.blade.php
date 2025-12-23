@extends(
    auth()->user()->isCashier()
        ? 'layouts.cashier'
        : (auth()->user()->isManager()
            ? 'layouts.manager'
            : 'layouts.admin')
)



@section('title', 'Point de vente')

@section('content')
<style>
    .pos-wrapper {
        max-width: 1200px;
        margin: 16px auto 32px;
        padding: 0 8px;
    }
    .pos-grid {
        display: grid;
        grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.2fr);
        gap: 18px;
    }
    @media (max-width: 1024px) {
        .pos-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="pos-wrapper" x-data="posApp()">
    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Caisse</h1>
            <p class="text-sm text-slate-500">
                Scanner un livre, ajouter au ticket et encaisser rapidement.
            </p>
        </div>
        <div class="text-right text-xs text-slate-500">
            <div>{{ now()->format('d/m/Y') }}</div>
            <div>Caisse : {{ auth()->user()->name }}</div>
        </div>
    </div>

    <div class="pos-grid">
        {{-- Colonne gauche : ticket --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-4 flex flex-col h-full">
            {{-- Recherche / scan --}}
            <div class="mb-3">
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1">
                    Scanner code‑barres 
                </label>
                <input type="text"
       x-model="search"
       
       @keydown.enter.prevent="submitBarcode()"
       placeholder="Scanner le code‑barres ou taper le titre..."
       class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500">
            </div>

            {{-- Résultats recherche rapide --}}
            <div x-show="results.length" class="mb-3 bg-slate-50 border border-slate-200 rounded-xl max-h-52 overflow-auto">
                <template x-for="book in results" :key="book.id">
                    <button type="button"
                            @click="addItem(book); results = []"
                            class="w-full flex items-center justify-between px-3 py-2 text-left text-xs hover:bg-sky-50">
                        <div>
                            <div class="font-semibold text-slate-800" x-text="book.title"></div>
                            <div class="text-[11px] text-slate-500" x-text="book.author"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-slate-900" x-text="formatPrice(book.price)"></div>
                            <div class="text-[11px] text-slate-400" x-text="'Stock: ' + book.stock"></div>
                        </div>
                    </button>
                </template>
            </div>

            {{-- Tableau du ticket --}}
            <div class="flex-1 overflow-auto border border-slate-100 rounded-xl">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-600">Article</th>
                        <th class="px-3 py-2 text-center font-semibold text-slate-600">Qté</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-600">Prix</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-600">Total</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-if="items.length === 0">
                        <tr>
                            <td colspan="5" class="px-3 py-6 text-center text-slate-400 text-sm">
                                Aucun article. Scanner un livre pour commencer.
                            </td>
                        </tr>
                    </template>

                    <template x-for="(item, index) in items" :key="item.id">
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2">
                                <div class="text-[13px] font-medium text-slate-900" x-text="item.title"></div>
                                <div class="text-[11px] text-slate-500" x-text="item.author"></div>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="inline-flex items-center border border-slate-200 rounded-full">
                                    <button type="button" class="px-2 text-slate-500 text-xs"
                                            @click="decreaseQty(index)">−</button>
                                    <span class="px-2 text-[13px]" x-text="item.quantity"></span>
                                    <button type="button" class="px-2 text-slate-500 text-xs"
                                            @click="increaseQty(index)">+</button>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-right text-[13px]" x-text="formatPrice(item.price)"></td>
                            <td class="px-3 py-2 text-right text-[13px]"
                                x-text="formatPrice(item.quantity * item.price)"></td>
                            <td class="px-3 py-2 text-right">
                                <button type="button" class="text-rose-500 text-xs" @click="removeItem(index)">
                                    ✕
                                </button>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>

            {{-- Totaux + encaissement --}}
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 items-start">
                <div class="space-y-1 text-xs text-slate-700">
                    <div class="flex justify-between">
                        <span>Sous‑total</span>
                        <span x-text="formatPrice(subtotal())"></span>
                    </div>
    {{--            <div class="flex justify-between">
                        <span>TVA 20%</span>
                        <span x-text="formatPrice(taxAmount())"></span>
                    </div>--}}
                    <div class="flex justify-between font-semibold text-slate-900 text-sm">
                        <span>Total TTC</span>
                        <span x-text="formatPrice(total())"></span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">Montant reçu</label>
                        <input type="number" step="0.01" min="0"
                               x-model.number="amountPaid"
                               class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="flex justify-between text-xs text-slate-700">
                        <span>Monnaie à rendre</span>
                        <span x-text="formatPrice(change())"></span>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-between gap-3">
                <button type="button"
                        @click="resetTicket()"
                        class="px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Nouveau ticket
                </button>

                <button type="button"
                        @click="submitSale()"
                        :disabled="loading || items.length === 0"
                        class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow-sm
                               hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Encaisser</span>
                    <span x-show="loading">Traitement...</span>
                </button>
            </div>
        </div>

        {{-- Colonne droite : client + paiement --}}
        <div class="space-y-4">
            {{-- Client --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">
                        Client
                    </span>
                    <a href="{{ route('admin.clients.create') }}" target="_blank"
                       class="text-[11px] text-sky-600 hover:text-sky-800">
                        + Nouveau client
                    </a>
                </div>

                <select x-model="clientId"
                        class="w-full border border-slate-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500">
                    <option value="">Client de passage</option>
                    @foreach($clients ?? [] as $client)
                        <option value="{{ $client->id }}">
                            {{ $client->name }}@if($client->company_name) – {{ $client->company_name }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Mode de paiement --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-4">
                <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">
                    Mode de paiement
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <label class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border cursor-pointer"
                           :class="paymentMethod === 'espece' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200'"
                           @click="paymentMethod = 'espece'">
                        <input type="radio" class="hidden" value="espece" x-model="paymentMethod">
                        <span>Espèce</span>
                    </label>

                    <label class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border cursor-pointer"
                           :class="paymentMethod === 'tpe' ? 'border-sky-500 bg-sky-50' : 'border-slate-200'"
                           @click="paymentMethod = 'tpe'">
                        <input type="radio" class="hidden" value="tpe" x-model="paymentMethod">
                        <span>TPE</span>
                    </label>

                    <label class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border cursor-pointer"
                           :class="paymentMethod === 'virement' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200'"
                           @click="paymentMethod = 'virement'">
                        <input type="radio" class="hidden" value="virement" x-model="paymentMethod">
                        <span>Virement bancaire</span>
                    </label>

                    <label class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border cursor-pointer"
                           :class="paymentMethod === 'sans_reglement' ? 'border-amber-500 bg-amber-50' : 'border-slate-200'"
                           @click="paymentMethod = 'sans_reglement'">
                        <input type="radio" class="hidden" value="sans_reglement" x-model="paymentMethod">
                        <span>Sans règlement</span>
                    </label>
                </div>
            </div>

            {{-- Infos ticket du jour (placeholder, à brancher plus tard) --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-4 text-xs text-slate-600">
                <div class="font-semibold text-slate-800 mb-1">Résumé du jour</div>
                <p>Tickets du jour : —</p>
                <p>Total encaissé : — DH</p>
            </div>
        </div>
    </div>
</div>

{{-- Alpine component --}}
<script>
    function posApp() {
        const getBookBaseUrl = '{{ url('/pos/book') }}';
        const searchUrl      = '{{ route('pos.search') }}';
        const saleUrl        = '{{ route('pos.sale') }}';

        return {
            search: '',
            results: [],
            items: [],
            clientId: '',
            paymentMethod: 'espece',
            amountPaid: 0,
            loading: false,

            formatPrice(value) {
                return new Intl.NumberFormat('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }).format(value || 0) + ' DH';
            },

            subtotal() {
                return this.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
            },

            

            total() {
                return this.subtotal() ;
            },

            change() {
                const diff = (this.amountPaid || 0) - this.total();
                return diff > 0 ? diff : 0;
            },

            addItem(book) {
                const existing = this.items.find(i => i.id === book.id);
                if (existing) {
                    existing.quantity++;
                } else {
                    this.items.push({
                        id: book.id,
                        title: book.title,
                        author: book.author ?? 'Inconnu',
                        price: Number(book.price),
                        quantity: 1,
                    });
                }
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            increaseQty(index) {
                this.items[index].quantity++;
            },

            decreaseQty(index) {
                if (this.items[index].quantity > 1) {
                    this.items[index].quantity--;
                } else {
                    this.removeItem(index);
                }
            },

            resetTicket() {
                this.items = [];
                this.amountPaid = 0;
                this.search = '';
                this.results = [];
            },

            // Live suggestions while typing (Google-style)
            liveSearch() {
                const term = this.search.trim();
                if (!term) {
                    this.results = [];
                    return;
                }

                const params = new URLSearchParams({ term });

                fetch(`${searchUrl}?` + params.toString(), {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(res => res.ok ? res.json() : [])
                    .then(data => {
                        this.results = data; // suggestions update live
                    })
                    .catch(() => {
                        this.results = [];
                    });
            },

            findByBarcode(code) {
                code = code.trim();
                if (!code) return null;
                return this.results.find(b => String(b.barcode) === code) || null;
            },

            // Enter: if exact barcode → add; else leave suggestions
            async submitBarcode() {
                const code = this.search.trim();
                if (!code) return;

                // 1) Exact match in current suggestions
                const localMatch = this.findByBarcode(code);
                if (localMatch) {
                    this.addItem(localMatch);
                    this.search  = '';
                    this.results = [];
                    return;
                }

                // 2) Ask backend for that barcode only
                try {
                    const encoded = encodeURIComponent(code);
                    const res = await fetch(`${getBookBaseUrl}/${encoded}`, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (res.ok) {
                        const data = await res.json();
                        this.addItem({
                            id: data.id,
                            title: data.title,
                            author: data.author,
                            price: data.price,
                            stock: data.stock,
                            barcode: data.barcode,
                        });
                        this.search  = '';
                        this.results = [];
                        return;
                    }
                } catch (e) {
                    // ignore, keep suggestions
                }

                // 3) No exact match: suggestions from liveSearch stay visible
            },

            async submitSale() {
    if (this.items.length === 0) {
        alert('Aucun article dans le ticket.');
        return;
    }

    this.loading = true;

    const payload = {
        items: this.items.map(i => ({
            book_id: i.id,
            quantity: i.quantity,
            price: i.price,
        })),
        client_id: this.clientId || null,
        payment_method: this.paymentMethod,
        amount_paid: this.amountPaid || 0,
    };

    try {
        const res = await fetch(saleUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (!res.ok || data.error) {
            alert(data.error || 'Erreur lors de la vente.');
            return;
        }

        alert('Vente enregistrée. Facture ' + data.invoice_number);

        // ouverture du ticket dans une nouvelle fenêtre
        const win = window.open('', '_blank'); // réduit le risque de blocage popup [web:585][web:610]
        const url = `{{ route('pos.receipt', ['sale' => 'SALE_ID']) }}`.replace('SALE_ID', data.sale_id);
        win.location = url;

        this.resetTicket();
    } catch (e) {
        alert('Erreur réseau.');
    } finally {
        this.loading = false;
    }
},

        }
    }
</script>
@endsection
