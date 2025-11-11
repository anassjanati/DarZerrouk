@extends('layouts.admin')

@section('title', 'Zones')

@section('content')
<h1 class="text-2xl font-bold mb-6">Zones & Emplacements</h1>

<div class="grid md:grid-cols-3 gap-6">
    @foreach($zones as $zone)
    <div class="bg-white rounded-xl p-6 border shadow hover:shadow-lg transition cursor-pointer"
         onclick="window.location = '{{ route('admin.zones.books', $zone->id) }}'">
        <div class="text-lg font-bold text-blue-700">{{ $zone->name }}</div>
        <div class="text-gray-600">Code: <span class="font-mono">{{ $zone->code }}</span></div>
        <div class="mt-3"><span class="font-semibold">{{ $zone->books_count }}</span> livres dans cette zone</div>
        <button class="mt-4 px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Voir les livres</button>
    </div>
    @endforeach
</div>
@endsection
