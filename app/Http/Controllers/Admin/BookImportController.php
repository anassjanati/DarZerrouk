<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Corrector;
use App\Models\Publisher;
use App\Models\Supplier;
use App\Models\Translator;
use App\Models\Stock;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookImportController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Validation fichier
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls'],
        ]);

        // 2. Lecture première feuille
        $rows = Excel::toArray([], $request->file('file'))[0] ?? [];
        if (empty($rows)) {
            return back()->withErrors(['file' => 'Fichier vide ou invalide.']);
        }

        // 3. Nettoyage des headers
        $headerRow = array_shift($rows);
        $header = array_map(function ($h) {
            $h = trim($h);
            $h = strtolower($h);
            $h = preg_replace('/\s+/', '_', $h);
            $h = str_replace(['�', "\u{FEFF}"], '', $h);
            return $h;
        }, $headerRow);

        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            // ignorer lignes vides
            if (count($row) === 0 || trim($row[0] ?? '') === '') {
                continue;
            }

            // ajuster la taille de la ligne
            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), null);
            }

            $data = array_combine($header, $row);
            $data = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $data);

            // ignorer si pas de code-barres
            if (empty($data['barcode'])) {
                continue;
            }

            // 4. Relations par nom
            $category = !empty($data['category_name'])
                ? Category::firstOrCreate(['name' => $data['category_name']])
                : null;

            $author = !empty($data['author_name'])
                ? Author::firstOrCreate(['name' => $data['author_name']])
                : null;

            $translator = !empty($data['translator_name'])
                ? Translator::firstOrCreate(['name' => $data['translator_name']])
                : null;

            $publisher = !empty($data['publisher_name'])
                ? Publisher::firstOrCreate(['name' => $data['publisher_name']])
                : null;

            $corrector = !empty($data['corrector_name'])
                ? Corrector::firstOrCreate(['name' => $data['corrector_name']])
                : null;

            $supplier = null;
            if (!empty($data['supplier_name'])) {
                $supplier = Supplier::firstOrCreate(
                    ['name' => $data['supplier_name']],
                    [
                        'code'      => substr(md5($data['supplier_name']), 0, 10),
                        'phone'     => '0000000000',
                        'country'   => 'Morocco',
                        'is_active' => 1,
                    ]
                );
            }

            // 5. Prix & stock
            $costPrice = is_numeric($data['cost_price'] ?? null)
                ? (float) $data['cost_price']
                : 0;

            $retailPrice = is_numeric($data['retail_price'] ?? null)
                ? (float) $data['retail_price']
                : 0;

            $stockQty = is_numeric($data['stock_quantity'] ?? null)
                ? (int) $data['stock_quantity']
                : 0;

            // 6. Livre existant ?
            $book = Book::where('barcode', $data['barcode'])->first();

            // 7. Données à enregistrer (adapter aux colonnes EXISTANTES de books)
            $payload = [
                'category_id'   => $category?->id,
                'author_id'     => $author?->id,
                'translator_id' => $translator?->id,
                'publisher_id'  => $publisher?->id,
                'corrector_id'  => $corrector?->id,
                'supplier_id'   => $supplier?->id ?? null,

                'barcode'       => $data['barcode'],
                'title'         => $data['title'] ?? '',
                'title_ar'      => $data['title_ar'] ?? '',
                'description'   => $data['description'] ?? null,
                'language'      => $data['language'] ?? 'ar',
                'format'        => $data['format'] ?? 'paperback',

                'cost_price'    => $costPrice,
                'retail_price'  => $retailPrice,

                'zone'          => $data['zone'] ?? null,
                'sous_zone'     => $data['sous_zone'] ?? null,
                'sous_sous_zone'=> $data['sous_sous_zone'] ?? null,

                'is_active'     => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            ];

            // 8. Create / update
            if ($book) {
                $book->update($payload);
                $updated++;
            } else {
                $book = Book::create($payload);
                $created++;
            }

            // 9. Stock initial dans stocks (optionnel, adapter aux colonnes de stocks)
            if ($stockQty > 0) {
    Stock::create([
        'book_id'         => $book->id,
        'zone_id'         => 1,          // TODO: mets ici l'ID de ta zone par défaut
        'sous_zone_id'    => null,       // ou un ID réel si tu veux
        'sous_sous_zone_id' => null,     // ou un ID réel
        'quantity'        => $stockQty,
    ]);
}
        }

        return back()->with('success', "✅ {$created} livres créés, {$updated} livres mis à jour.");
    }
}
