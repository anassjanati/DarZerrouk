<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($search = trim($request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($city = trim($request->get('city', ''))) {
            $query->where('city', 'like', "%{$city}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $clients = $query->orderBy('name')
                         ->paginate(25)
                         ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'         => ['nullable', 'string', 'max:50', 'unique:clients,code'],
            'name'         => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:50'],
            'whatsapp'     => ['nullable', 'string', 'max:50'],
            'email'        => ['nullable', 'email', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'address'      => ['nullable', 'string', 'max:500'],
            'is_active'    => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Client::create($data);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'code'         => ['nullable', 'string', 'max:50', 'unique:clients,code,' . $client->id],
            'name'         => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:50'],
            'whatsapp'     => ['nullable', 'string', 'max:50'],
            'email'        => ['nullable', 'email', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'address'      => ['nullable', 'string', 'max:500'],
            'is_active'    => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $client->update($data);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client supprimé.');
    }
}
