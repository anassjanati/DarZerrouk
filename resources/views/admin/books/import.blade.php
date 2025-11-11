@extends('layouts.admin')
@section('title', 'Importer Livres (CSV)')
@section('content')
<h1 class="text-2xl font-bold mb-4">Importer Livres (CSV)</h1>

@if(session('success'))
    <div class="mb-4 text-green-700 bg-green-200 p-4 rounded">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.books.import.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="file" name="csv_file" accept=".csv,.txt" required class="mb-4">
    <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700 font-semibold">
        Importer
    </button>
</form>
@endsection
