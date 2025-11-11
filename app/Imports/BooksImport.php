<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Zone;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// Remove this: use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BooksImport implements ToModel, WithHeadingRow, WithChunkReading
{
    private $importedCount = 0;
    private $skippedCount = 0;
    private $errors = [];

    public function model(array $row)
    {
        static $counter = 0;
        $counter++;
        
        // Progress output every 100 rows
        if ($counter % 100 === 0) {
            echo "Processing row {$counter}...\n";
        }

        try {
            // Skip if no title
            if (empty($row['titre'])) {
                $this->skippedCount++;
                return null;
            }

            // Get or create Author
            $authorName = $row['auteur'] ?? 'Auteur Inconnu';
            $author = $this->getOrCreateAuthor($authorName);

            // Get or create Publisher
            $publisherName = $row['maison_dedition'] ?? 'Éditeur Inconnu';
            $publisher = $this->getOrCreatePublisher($publisherName);

            // Get or create Category
            $categoryName = $row['sous_categorie'] ?? 'Non classé';
            $category = $this->getOrCreateCategory($categoryName);

            // Get or create Zone
            $zoneName = $row['zone'] ?? 'A1';
            $zone = $this->getOrCreateZone($zoneName);

            // Parse price
            $prix = $this->parsePrice($row['prix'] ?? 0);

            // Parse date
            $createdAt = $this->parseDate($row['date_de_creation'] ?? null);

            // Create notes from Fournisseur and Désignation
            $notes = trim(($row['fournisseur'] ?? '') . ' ' . ($row['designation'] ?? ''));

            // Create book
            $book = Book::create([
                'barcode' => $row['ref'] ?? null,
                'title' => $row['titre'],
                'subtitle' => $row['designation'] ?? null,
                'notes' => $notes ?: null,
                'author_id' => $author->id,
                'publisher_id' => $publisher->id,
                'category_id' => $category->id,
                'zone_id' => $zone->id,
                'language' => 'fr',
                'format' => 'paperback',
                'condition' => 'new',
                'price_1' => $prix,
                'price_2' => $prix,
                'selling_price_old' => $prix,
                'cost_price' => 0,
                'stock_quantity' => intval($row['stock'] ?? 0),
                'min_stock_level' => 5,
                'is_active' => true,
                'created_at' => $createdAt,
                'updated_at' => now(),
            ]);

            $this->importedCount++;
            return $book;

        } catch (\Exception $e) {
            $this->skippedCount++;
            $this->errors[] = [
                'row' => $counter,
                'title' => $row['titre'] ?? 'N/A',
                'error' => $e->getMessage()
            ];
            echo "ERROR on row {$counter}: " . $e->getMessage() . "\n";
            return null;
        }
    }

    private function getOrCreateAuthor($name)
    {
        if (empty($name)) {
            $name = 'Auteur Inconnu';
        }
        
        return Author::firstOrCreate(
            ['name' => trim($name)],
            ['is_active' => true]
        );
    }

    private function getOrCreatePublisher($name)
    {
        if (empty($name)) {
            $name = 'Éditeur Inconnu';
        }
        
        return Publisher::firstOrCreate(
            ['name' => trim($name)],
            ['is_active' => true]
        );
    }

    private function getOrCreateCategory($name)
    {
        if (empty($name)) {
            $name = 'Non classé';
        }

        // Handle hierarchical categories if contains ">" or "/"
        if (strpos($name, '>') !== false || strpos($name, '/') !== false) {
            $separator = strpos($name, '>') !== false ? '>' : '/';
            $parts = array_map('trim', explode($separator, $name));
            $parent = null;
            $category = null;

            foreach ($parts as $index => $part) {
                $category = Category::firstOrCreate(
                    [
                        'name' => $part,
                        'parent_id' => $parent?->id
                    ],
                    [
                        'slug' => Str::slug($part),
                        'level' => $index,
                        'is_active' => true
                    ]
                );
                $parent = $category;
            }

            return $category;
        }

        // Simple category
        return Category::firstOrCreate(
            ['name' => trim($name), 'parent_id' => null],
            [
                'slug' => Str::slug($name),
                'level' => 0,
                'is_active' => true
            ]
        );
    }

    private function getOrCreateZone($name)
    {
        if (empty($name)) {
            $name = 'A1';
        }

        // Extract code (alphanumeric only)
        $code = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $name));
        if (empty($code)) {
            $code = 'A1';
        }

        return Zone::firstOrCreate(
            ['code' => $code],
            [
                'name' => trim($name),
                'is_active' => true
            ]
        );
    }

    private function parsePrice($value)
    {
        // Remove currency symbols, spaces, and convert comma to period
        $cleaned = preg_replace('/[^\d.,]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);
        return floatval($cleaned);
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return now();
        }

        try {
            // Try Excel numeric date (days since 1900-01-01)
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            }

            // Try various date formats
            $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d', 'dmY'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value);
                } catch (\Exception $e) {
                    continue;
                }
            }

            return Carbon::parse($value);
        } catch (\Exception $e) {
            return now();
        }
    }

    // REMOVED: batchSize() method

    public function chunkSize(): int
    {
        return 100;
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
