@extends('layouts.admin')

@section('title', 'Ajouter un client')

@section('content')
<div class="max-w-xl mx-auto mt-8">
    <h1 class="text-2xl font-bold mb-4">Ajouter un client</h1>

    <form method="POST" action="{{ route('admin.clients.store') }}">
        @csrf

        @include('admin.clients._form', ['client' => null])

        <button type="submit"
                class="mt-4 px-4 py-2 bg-teal-700 text-white rounded hover:bg-teal-800 font-semibold">
            Enregistrer
        </button>
    </form>
</div>
@endsection
