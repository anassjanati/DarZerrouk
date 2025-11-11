<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\Book;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function overview()
    {
        $zones = Zone::withCount('books')->orderBy('name')->get();
        return view('admin.zones.overview', compact('zones'));
    }
    public function show(Zone $zone)
{
    // Default behavior: just show the books in the zone
    return redirect()->route('admin.zones.books', $zone->id);
}


    public function booksInZone(Zone $zone)
    {
        $books = $zone->books()->with(['author', 'category'])->paginate(50);
        $allZones = Zone::orderBy('name')->get();
        return view('admin.zones.books', compact('zone', 'books', 'allZones'));
    }
    public function index()
{
    // You can either redirect to your custom "overview" page:
    return redirect()->route('admin.zones.overview');
    // OR:
    // return $this->overview();
}

}
