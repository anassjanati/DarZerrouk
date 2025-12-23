<div class="space-y-4">
    <div>
        <label class="block text-sm font-semibold mb-1">Code client (optionnel)</label>
        <input type="text" name="code"
               value="{{ old('code', $client->code ?? '') }}"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Nom complet</label>
        <input type="text" name="name"
               value="{{ old('name', $client->name ?? '') }}"
               class="w-full border rounded px-3 py-2 text-sm" required>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Entreprise / Nom de page</label>
        <input type="text" name="company_name"
               value="{{ old('company_name', $client->company_name ?? '') }}"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Téléphone</label>
            <input type="text" name="phone"
                   value="{{ old('phone', $client->phone ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">WhatsApp</label>
            <input type="text" name="whatsapp"
                   value="{{ old('whatsapp', $client->whatsapp ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Email</label>
        <input type="email" name="email"
               value="{{ old('email', $client->email ?? '') }}"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Ville</label>
            <input type="text" name="city"
                   value="{{ old('city', $client->city ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Adresse</label>
            <input type="text" name="address"
                   value="{{ old('address', $client->address ?? '') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $client->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm">Client actif</span>
    </div>

    @if ($errors->any())
        <div class="mt-2 p-2 bg-red-50 text-red-700 text-sm rounded">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif
</div>
