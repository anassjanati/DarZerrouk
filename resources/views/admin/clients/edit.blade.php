@extends('layouts.admin')

@section('title', 'Éditer le client')

@section('content')
<div class="max-w-xl mx-auto mt-8">
    <h1 class="text-2xl font-bold mb-4">Éditer le client</h1>

    <form method="POST" action="{{ route('admin.clients.update', $client) }}">
        @csrf
        @method('PUT')

        @include('admin.clients._form', ['client' => $client])

        <button type="submit"
                class="mt-4 px-4 py-2 bg-teal-700 text-white rounded hover:bg-teal-800 font-semibold">
            Mettre à jour
        </button>
    </form>
</div>
@endsection
