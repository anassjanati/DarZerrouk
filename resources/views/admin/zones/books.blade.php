@extends('layouts.admin')

@section('title', "Livres dans la zone : $zone->name")

@section('content')
<h1 class="text-2xl font-bold mb-4">Livres dans la zone <span class="text-teal-700">{{ $zone->name }}</span></h1>
<a href="{{ route('admin.zones.overview') }}" class="mb-6 inline-block text-blue-600 hover:underline">← Retour aux zones</a>

<table class="min-w-full divide-y divide-gray-200 mt-6">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3">Livre</th>
            <th class="px-6 py-3">Auteur</th>
            <th class="px-6 py-3">Catégorie</th>
            <th class="px-6 py-3">Zone actuelle</th>
            <th class="px-6 py-3">Changer de zone</th>
        </tr>
    </thead>
    <tbody>
        @foreach($books as $book)
        <tr>
            <td class="px-6 py-4 book-title">{{ $book->title }}</td>
            <td class="px-6 py-4">{{ $book->author->name ?? '-' }}</td>
            <td class="px-6 py-4">{{ $book->category->name ?? '-' }}</td>
            <td class="px-6 py-4 text-blue-700 font-bold">{{ $book->zone->name ?? '-' }}</td>
            <td class="px-6 py-4">
                <form class="change-zone-form" data-id="{{ $book->id }}">
                    <select name="zone_id" class="rounded border">
                        @foreach($allZones as $z)
                            <option value="{{ $z->id }}" @if($book->zone_id == $z->id) selected @endif>
                                {{ $z->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-6">
    {{ $books->links() }}
</div>

<script>
document.querySelectorAll('.change-zone-form').forEach(form => {
    const select = form.querySelector('select');
    // Save the initial value to restore it if user cancels
    let originalValue = select.value;

    select.onchange = function(e) {
        const bookId = form.getAttribute('data-id');
        const newZoneId = select.value;
        const newZoneName = select.options[select.selectedIndex].text;
        const row = select.closest('tr');
        const oldZoneTd = row.querySelector('td.text-blue-700');
        const oldZoneName = oldZoneTd.textContent.trim();
        const bookTitle = row.querySelector('.book-title').textContent.trim();

        if (newZoneName === oldZoneName) {
            return; // no change
        }

        if (!confirm(`Êtes-vous sûr de vouloir déplacer « ${bookTitle} » de la zone « ${oldZoneName} » vers « ${newZoneName} » ?`)) {
            select.value = originalValue; // Reset selection
            return;
        }

        fetch('/admin/books/' + bookId + '/zone', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ zone_id: newZoneId })
        }).then(res => res.json())
          .then(data => {
              if (data.success) {
                  oldZoneTd.textContent = newZoneName;
                  originalValue = newZoneId; // Update original value
              } else {
                  alert('Erreur lors du changement de zone');
                  select.value = originalValue; // Roll back
              }
          });
    };
});
</script>
@endsection
