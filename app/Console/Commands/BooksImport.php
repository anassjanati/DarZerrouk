<?php

namespace App\Imports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class BooksImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use Importable;

    protected int $imported = 0;
    protected int $skipped = 0;
    protected array $errors = [];

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Map one Excel row to a Book model.
     */
    public function model(array $row)
    {
        try {
            // Normalise keys to lowercase (matches your headings)
            $row = array_change_key_case($row, CASE_LOWER);

            $barcode = trim($row['barcode'] ?? '');
            $title   = trim($row['title']   ?? '');

            // Required fields
            if ($barcode === '' || $title === '') {
                $this->skipped++;
                $this->errors[] = [
                    'row'   => $row,
                    'error' => 'Missing barcode or title',
                ];
                return null;
            }

            // Skip duplicates
            if (Book::where('barcode', $barcode)->exists()) {
                $this->skipped++;
                $this->errors[] = [
                    'row'   => $row,
                    'error' => "Barcode {$barcode} already exists",
                ];
                return null;
            }

            // Foreign keys (use values from file, or defaults)
            $categoryId  = (int)($row['category_id']  ?? 1);
            $authorId    = (int)($row['author_id']    ?? 1);
            $publisherId = (int)($row['publisher_id'] ?? 1);

            // Enum fields
            $format = $row['format'] ?? 'paperback';
            if (! in_array($format, ['hardcover', 'paperback', 'ebook'])) {
                $format = 'paperback';
            }

            $condition = $row['condition'] ?? 'new';
            if (! in_array($condition, ['new', 'used_like_new', 'used_good'])) {
                $condition = 'new';
            }

            $language = $row['language'] ?? 'ar';

            $book = new Book([
                'barcode'      => $barcode,
                'title'        => $title,
                'title_ar'     => $row['title_ar'] ?? $title,

                'category_id'  => $categoryId,
                'author_id'    => $authorId,
                'publisher_id' => $publisherId,

                'translator_id' => ($row['translator_id'] ?? '') === '' ? null : (int)$row['translator_id'],
                'editor_id'     => ($row['editor_id']     ?? '') === '' ? null : (int)$row['editor_id'],
                'corrector_id'  => ($row['corrector_id']  ?? '') === '' ? null : (int)$row['corrector_id'],

                'description'  => $row['description'] ?? null,
                'language'     => $language,

                'pages'            => ($row['pages']            ?? '') === '' ? null : (int)$row['pages'],
                'format'           => $format,
                'edition_year'     => ($row['edition_year']     ?? '') === '' ? null : (int)$row['edition_year'],
                'edition_number'   => $row['edition_number']   ?? null,
                'publication_year' => ($row['publication_year'] ?? '') === '' ? null : (int)$row['publication_year'],

                'cover_image' => $row['cover_image'] ?? null,
                'weight'      => ($row['weight']      ?? '') === '' ? null : (float)$row['weight'],
                'height'      => ($row['height']      ?? '') === '' ? null : (float)$row['height'],
                'width'       => ($row['width']       ?? '') === '' ? null : (float)$row['width'],
                'thickness'   => ($row['thickness']   ?? '') === '' ? null : (float)$row['thickness'],

                'reorder_level'   => ($row['reorder_level']   ?? '') === '' ? 5 : (int)$row['reorder_level'],
                'min_stock_level' => ($row['min_stock_level'] ?? '') === '' ? null : (int)$row['min_stock_level'],

                'book_condition' => $condition,

                'cost_price'   => ($row['cost_price']   ?? '') === '' ? 0 : (float)$row['cost_price'],
                'retail_price' => ($row['retail_price'] ?? '') === '' ? 0 : (float)$row['retail_price'],
                'wholesale_price' => ($row['wholesale_price'] ?? '') === '' ? null : (float)$row['wholesale_price'],

                'discount_percentage' => ($row['discount_percentage'] ?? '') === '' ? 0 : (float)$row['discount_percentage'],
                'is_featured'         => (int)($row['is_featured'] ?? 0),
                'is_active'           => (int)($row['is_active']   ?? 1),
            ]);

            $this->imported++;

            return $book;

        } catch (\Throwable $e) {
            $this->skipped++;
            $this->errors[] = [
                'row'   => $row,
                'error' => $e->getMessage(),
            ];
            Log::error('BooksImport error', ['row' => $row, 'message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Validation rules per row (optional but useful).
     */
    public function rules(): array
    {
        return [
            'barcode'      => ['required', 'string'],
            'title'        => ['required', 'string'],
            'cost_price'   => ['nullable', 'numeric'],
            'retail_price' => ['nullable', 'numeric'],
        ];
    }

    /**
     * Handle rows that fail validation.
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->skipped++;
            $this->errors[] = [
                'row'   => $failure->values(),
                'error' => 'Validation: ' . implode(', ', $failure->errors()),
            ];
        }
    }
}
